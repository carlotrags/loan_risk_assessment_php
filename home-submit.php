<?php
session_start();


// Check login
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an assessment.");
}


include 'static/config.php';


$user_id = $_SESSION['user_id'];
$loan_type = 'Home'; // Custom type for home loan


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    // 1. Collect and Cast Variables for the Logistic Regression Model
    $applicant_name = $_POST['applicant_name'] ?? '';
    $email = $_POST['email'] ?? ''; // Added email capture to sync with logs


    $data = [
        "age" => (int)$_POST['age'],
        "sex" => (int)$_POST['sex'],
        "civil_status" => (int)$_POST['civil_status'],
        "dependents" => (int)$_POST['dependents'],
        "years_of_stay" => (int)$_POST['years_of_stay'],
        "home_ownership" => (int)$_POST['home_ownership'],
        "employment_type" => (int)$_POST['employment_type'],
        "monthly_income" => (float)$_POST['monthly_income'],
        "years_employed" => (int)$_POST['years_employed'],
        "loan_amount" => (float)$_POST['loan_amount'],
        "loan_term" => (int)$_POST['loan_term'],
        "existing_loans" => (int)$_POST['existing_loans'],
        "monthly_debt" => (float)$_POST['monthly_debt'],
        "dti_ratio" => (float)$_POST['dti_ratio'],
        "default_history" => (int)$_POST['default_history']
    ];


    // 2. Call Flask API for Home Loan Prediction
    $api_url = 'http://127.0.0.1:5001/predict/home'; // Ensure this matches your Flask route
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
        $prediction = (int)$result['prediction'];
        $explanation = $result['explanation'] ?? [];
        $message = $prediction == 1 ? "Low Risk" : "High Risk";
        $statusClass = $prediction == 1 ? "low" : "high";


        // A. Insert into home_loan_applications (Detailed Table)
        $stmt = $conn->prepare("INSERT INTO home_loan_applications
        (name, email, tin_no, birthdate, address, collateral_type, property_value, age, sex, civil_status, dependents, years_of_stay, home_ownership, employment_type, monthly_income, years_employed, loan_amount, loan_term, existing_loans, monthly_debt, dti_ratio, default_history, prediction, assessed_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
       
        // Added email parameter to parameter mapping chain
        $stmt->bind_param(
            "ssssssdiiiiiiidididddiii",
            $applicant_name,
            $email,
            $_POST['tin_no'],
            $_POST['birthdate'],
            $_POST['home_address'],
            $_POST['collateral_type'],
            $_POST['property_value'],
            $data['age'],
            $data['sex'],
            $data['civil_status'],
            $data['dependents'],
            $data['years_of_stay'],
            $data['home_ownership'],
            $data['employment_type'],
            $data['monthly_income'],
            $data['years_employed'],
            $data['loan_amount'],
            $data['loan_term'],
            $data['existing_loans'],
            $data['monthly_debt'],
            $data['dti_ratio'],
            $data['default_history'],
            $prediction,
            $user_id
        );


        if (!$stmt->execute()) {
            die("Database insertion failure on primary home table: " . $stmt->error);
        }


        $application_id = $conn->insert_id;
        $stmt->close();


        // 3. Save to general history summary logging table including the email profile field
        $stmt2 = $conn->prepare("INSERT INTO loan_application_history
        (user_id, application_id, name, email, loan_amount, loan_term, prediction, loan_type, submitted_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt2->bind_param(
            "iissdiis",
            $user_id,
            $application_id,
            $applicant_name,
            $email,
            $data['loan_amount'],
            $data['loan_term'],
            $prediction,
            $loan_type
        );


        if (!$stmt2->execute()) {
            die("Database insertion failure on history logger table: " . $stmt2->error);
        }
        $stmt2->close();
        $conn->close();


    } else {
        die("API Error: " . ($result['error'] ?? 'Unknown error'));
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css">
    <link rel="stylesheet" href="static/css/result.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Home Loan Assessment Result</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"; ?>
    </section>


    <div class="result-container">
        <div class="result-card">
            <div class="card-header">
                <h2>Home Loan Assessment Result</h2>
                <p class="applicant-info">Applicant: <?= htmlspecialchars($applicant_name) ?></p>
            </div>


            <div class="assessment-status <?= htmlspecialchars($statusClass) ?>">
                <i class="icon"><?= $prediction == 1 ? '&#10003;' : '&#10007;' ?></i>
                <div class="status-message">
                    <h3><?= htmlspecialchars($message) ?></h3>
                </div>
            </div>


            <div class="section application-summary">
                <h4>Application Details Submitted</h4>
                <div class="data-grid">
                    <div><span class="label">Loan Amount:</span> <span class="value">₱<?= number_format($data['loan_amount'], 2) ?></span></div>
                    <div><span class="label">Collateral:</span> <span class="value"><?= htmlspecialchars($_POST['collateral_type']) ?></span></div>
                    <div><span class="label">DTI Ratio:</span> <span class="value"><?= htmlspecialchars($data['dti_ratio']) ?>%</span></div>
                    <div><span class="label">Prediction:</span> <span class="value fw-bold text-uppercase"><?= ($prediction == 1 ? 'Low Risk' : 'High Risk') ?></span></div>
                </div>
            </div>


            <?php if (!empty($explanation) && $prediction == 0): ?>
                <div class="section denial-reasons">
                    <h4>Reason(s) for Denial</h4>
                    <ul class="reason-list">
                        <?php foreach ($explanation as $reason): ?>
                            <li><i class="icon-reason">&#x25CF;</i> <?= htmlspecialchars($reason) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>


            <div class="action-buttons">
                <a href="view-details.php?id=<?= $application_id ?>&type=home" class="btn btn-success">Edit / Print Assessment</a>
                <a href="home-form.php" class="btn btn-primary">Submit New Assessment</a>
            </div>
        </div>
    </div>

    <?php include "static/footer.php"; ?>
</body>
</html>