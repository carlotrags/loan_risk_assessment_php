<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an assessment.");
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? '';
$username = $_SESSION['username'] ?? '';

// Only handle POST submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Auto-set loan type to personal
    $loan_type = 'personal';
    $name = $_POST['name'] ?? '';

    $data = [
        'income' => (float)($_POST['income'] ?? 0),
        'loan_amount' => (float)($_POST['loan_amount'] ?? 0),
        'loan_term' => (int)($_POST['loan_term'] ?? 0),
        'credit_score' => (float)($_POST['credit_score'] ?? 0),
        'previous_defaults' => ($_POST['previous_defaults'] == "1") ? 1 : 0
    ];

    // Use personal loan API only
    $api_url = 'http://127.0.0.1:5000/predict/personal';

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
        $prediction = $result['prediction'];
        $explanation = $result['explanation'] ?? [];
        $message = $prediction == 1 ? "Loan Approved" : "Loan Denied";
        $statusClass = $prediction == 1 ? "approved" : "denied";

        // Save to MySQL
        $conn = new mysqli("127.0.0.1", "root", "", "loan_system", 3306);
        if ($conn->connect_error) {
            die("MySQL Connection failed: " . $conn->connect_error);
        }

        // Personal loans table
        $table = 'personal_loan_applications';

        $stmt = $conn->prepare("INSERT INTO $table 
            (name, income, loan_amount, loan_term, credit_score, previous_defaults, prediction, submitted_at, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param(
            "sddddiii",
            $name,
            $data['income'],
            $data['loan_amount'],
            $data['loan_term'],
            $data['credit_score'],
            $data['previous_defaults'],
            $prediction,
            $user_id
        );
        $stmt->execute();
        $application_id = $conn->insert_id;
        $stmt->close();

        // Insert into general history
        $stmt2 = $conn->prepare("INSERT INTO loan_application_history 
            (user_id, application_id, name, loan_amount, loan_term, loan_type, submitted_at, prediction) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt2->bind_param(
            "iisdisi",
            $user_id,
            $application_id,
            $name,
            $data['loan_amount'],
            $data['loan_term'],
            $loan_type,
            $prediction
        );
        $stmt2->execute();
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <div><span class="label">Monthly Income:</span> <span class="value">₱<?= number_format($data['income'] ?? 0, 2) ?></span></div>
                <div><span class="label">Credit Score:</span> <span class="value"><?= number_format($data['credit_score'] ?? 0, 0) ?></span></div>
                <div><span class="label">Previous Defaults:</span> <span class="value"><?= ($data['previous_defaults'] == 1 ? 'Yes' : 'No') ?></span></div>
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
            <a href="form.php" class="btn btn-primary">Submit New Assessment</a> 
        </div>

        <div class="footer-result">
            <p>Results are preliminary and subject to final verification.</p>
        </div>
    </div>
</div>

<?php include "static/footer.php"?>
</body>
</html>