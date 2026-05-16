<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an assessment.");
}

// 1. Use your centralized configuration file instead of hardcoded connections
include 'static/config.php';

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? '';
$username = $_SESSION['username'] ?? '';

// Only handle POST submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Auto-set loan type to personal
    $loan_type = 'Personal'; // Matches the Enum capitalization 'Personal' in your SQL history table
    
    // Capture the name parts or full name based on your form inputs
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact_no = $_POST['contact_no'] ?? '';
    $home_address = $_POST['home_address'] ?? '';
    
    // ML Features mapping to your new schema rules
    $age = (int)($_POST['age'] ?? 0);
    $sex = (int)($_POST['sex'] ?? 1);
    $civil_status = (int)($_POST['civil_status'] ?? 0);
    $dti_ratio = (float)($_POST['dti_ratio'] ?? 0);
    $loan_intent = $_POST['loan_intent'] ?? 'Medical';
    $existing_loans = (int)($_POST['existing_loans'] ?? 0);
    $default_history = (int)($_POST['default_history'] ?? 0);

    // Build comprehensive data structure for Flask matching personal-form.php
    $data = [
        "age"             => $age, 
        "sex"             => $sex, 
        "civil_status"    => $civil_status,
        "monthly_income"  => (float)($_POST['income'] ?? 0), 
        "credit_score"    => (int)($_POST['credit_score'] ?? 0),
        "dti_ratio"       => $dti_ratio, 
        "loan_amount"     => (float)($_POST['loan_amount'] ?? 0),
        "loan_term"       => (int)($_POST['loan_term'] ?? 0), 
        "loan_intent"     => $loan_intent,
        "existing_loans"  => $existing_loans, 
        "default_history" => $default_history
    ];

    // 2. Point to the primary API endpoint used by your machine learning module
    $api_url = 'http://127.0.0.1:5000/predict';

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    $response = curl_exec($ch);

    if ($response === false) {
        die("Error calling prediction API: " . curl_error($ch));
    }
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['prediction'])) {
        $prediction = $result['prediction'] ?? "0";
        $explanation = $result['explanation'] ?? [];
        $message = $prediction == 1 ? "Loan Approved" : "Loan Denied";
        $statusClass = $prediction == 1 ? "approved" : "denied";

        // 3. Save to personal_loan_applications table using your updated column structure
        $stmt = $conn->prepare("
            INSERT INTO personal_loan_applications 
            (name, email, contact_no, home_address, loan_amount, loan_term, loan_intent,
            income, credit_score, dti_ratio, existing_loans,
            default_history, age, sex, civil_status, prediction, user_id, submitted_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        // Type tracking definition mapping: 17 total parameters matching the question marks above
        $typeString = "ssssdisddiiiiisii";
        $stmt->bind_param(
            $typeString,
            $name, $email, $contact_no, $home_address, $data['loan_amount'], $data['loan_term'], $loan_intent,
            $data['monthly_income'], $data['credit_score'], $data['dti_ratio'], $existing_loans,
            $default_history, $age, $sex, $civil_status, $prediction, $user_id
        );
        
        if (!$stmt->execute()) {
            die("Database Error on Application Save: " . $stmt->error);
        }
        $application_id = $conn->insert_id;
        $stmt->close();

        // 4. Save the entry to your history tracking table logs
        $stmt2 = $conn->prepare("INSERT INTO loan_application_history 
            (user_id, application_id, name, email, loan_amount, loan_term, loan_type, submitted_at, prediction) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt2->bind_param(
            "iisdissi",
            $user_id,
            $application_id,
            $name,
            $email,
            $data['loan_amount'],
            $data['loan_term'],
            $loan_type,
            $prediction
        );
        
        if (!$stmt2->execute()) {
            die("Database Error on History Logging: " . $stmt2->error);
        }
        $stmt2->close();

        $conn->close();

    } else {
        $message = "Error: " . ($result['error'] ?? 'Unknown error from API');
        $statusClass = "error";
        $explanation = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
<link rel="stylesheet" href="static/css/navbarstyle.css">
<link rel='stylesheet' href='static/css/result.css?v=<?= time() ?>'>
<link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
<title>Personal Loan Assessment Result</title>
</head>
<body>
<section class="header-navbar">
    <?php include "static/navbar.php"?>
</section>

<div class="result-container">
    <div class="result-card">
        <div class="card-header">
            <h2>Loan Assessment Result</h2>
            <p class="applicant-info">Applicant: <?= htmlspecialchars($name ?? '') ?></p>
        </div>

        <div class="assessment-status <?= htmlspecialchars($statusClass ?? 'info') ?>">
            <?php if (isset($prediction)): ?>
                <i class="icon"><?= $prediction == 1 ? '&#10003;' : '&#10007;' ?></i>
            <?php endif; ?>
            <div class="status-message">
                <h3><?= htmlspecialchars($message ?? 'No result available') ?></h3>
            </div>
        </div>
        
        <?php if (isset($data)): ?>
        <div class="section application-summary">
            <h4>Application Details Submitted</h4>
            <div class="data-grid">
                <div><span class="label">Loan Amount:</span> <span class="value">₱<?= number_format($data['loan_amount'] ?? 0, 2) ?></span></div>
                <div><span class="label">Loan Term:</span> <span class="value"><?= htmlspecialchars($data['loan_term'] ?? 0) ?> months</span></div>
                <div><span class="label">Monthly Income:</span> <span class="value">₱<?= number_format($data['monthly_income'] ?? 0, 2) ?></span></div>
                <div><span class="label">Credit Score:</span> <span class="value"><?= number_format($data['credit_score'] ?? 0, 0) ?></span></div>
                <div><span class="label">Previous Defaults:</span> <span class="value"><?= ($default_history == 1 ? 'Yes' : 'No') ?></span></div>
                <div><span class="label">Loan Type:</span> <span class="value">Personal</span></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($explanation) && isset($prediction) && $prediction == 0): ?>
            <div class="section denial-reasons">
                <h4>Reason(s) for Denial</h4>
                <ul class="reason-list">
                    <?php foreach ($explanation as $reason): ?>
                        <li><i class="icon-reason">&#x25CF;</i> <?= htmlspecialchars($reason) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="action-buttons d-flex gap-3">
            <a href="<?php echo 'view-details.php?id=' . urlencode($application_id) . '&type=' . urlencode($loan_type); ?>" class="btn btn-success">
                Edit / Print Assessment
            </a>
            <a href="personal-form.php" class="btn btn-primary">Submit New Assessment</a>
        </div>

        <div class="footer-result">
            <p>Results are preliminary and subject to final verification.</p>
        </div>
    </div>
</div>

<?php include "static/footer.php"?>
</body>
</html>