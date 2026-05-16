<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

include 'static/config.php';

/**
 * LOGIC: If 'is_final_submission' exists, it means the user clicked 
 * "Start Assessment" on preview.php. We save to the DB now.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['is_final_submission'])) {

    // Applicant Info
    $first_name     = $_POST['first_name'];
    $middle_name    = $_POST['middle_name'];
    $last_name      = $_POST['last_name'];
    $suffix_name    = $_POST['suffix_name'];
    $applicant_name = trim("$first_name $middle_name $last_name $suffix_name");
    $email          = $_POST['email']; // Added email capture
    $contact_no     = $_POST['contact_no']; //added contact number capture
    $home_address   = $_POST['home_address'];

    // ML Features
    $age             = (int)$_POST['age'];
    $sex             = (int)$_POST['sex'];
    $civil_status    = (int)$_POST['civil_status'];
    $monthly_income  = (float)$_POST['monthly_income'];
    $credit_score    = (int)$_POST['credit_score'];
    $dti_ratio       = (float)$_POST['dti_ratio'];
    $loan_amount     = (float)$_POST['loan_amount'];
    $loan_term       = (int)$_POST['loan_term'];
    $loan_intent     = $_POST['loan_intent'];
    $existing_loans  = (int)$_POST['existing_loans'];
    $default_history = (int)$_POST['default_history'];

    // Call Flask API
    $modelData = [
        "age"             => $age, 
        "sex"             => $sex, 
        "civil_status"    => $civil_status,
        "monthly_income"  => $monthly_income, 
        "credit_score"    => $credit_score,
        "dti_ratio"       => $dti_ratio, 
        "loan_amount"     => $loan_amount,
        "loan_term"       => $loan_term, 
        "loan_intent"     => $loan_intent,
        "existing_loans"  => $existing_loans, 
        "default_history" => $default_history
    ];

    $ch = curl_init("http://127.0.0.1:5000/predict");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($modelData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $apiResult  = json_decode($response, true);
    $prediction = $apiResult['prediction'] ?? "API_ERROR";

    // Save to Database (personal_loan_applications)
    // Updated to include 'contact_no' column and an additional '?' placeholder
    $stmt = $conn->prepare("
        INSERT INTO personal_loan_applications 
        (name, email, contact_no, home_address, loan_amount, loan_term, loan_intent,
        income, credit_score, dti_ratio, existing_loans,
        default_history, age, sex, civil_status, prediction, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Updated typeString: added an 's' for the contact string variable (total 17 chars)
    $typeString = "ssssdisddiiiiisii";
    $stmt->bind_param(
        $typeString,
        $applicant_name, $email, $contact_no, $home_address, $loan_amount, $loan_term, $loan_intent,
        $monthly_income, $credit_score, $dti_ratio, $existing_loans,
        $default_history, $age, $sex, $civil_status, $prediction, $_SESSION['user_id']
    );

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: home-history.php?success=1");
        exit;
    } else {
        echo "DB Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Personal Loan Assessment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <style>
        body { background: #f4f6f9; font-family: 'Roboto', sans-serif; }
        .custom-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .section-title { color: #003366; font-weight: 700; border-left: 5px solid #c5a059; padding-left: 12px; margin-bottom: 20px; font-size: 1.1rem; }
        label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #555; margin-bottom: 5px; }
        .form-control, .form-select { border-radius: 8px; padding: 10px; border: 1px solid #ced4da; }
        .form-control:focus { border-color: #003366; box-shadow: none; }
        .bg-readonly { background-color: #e9ecef !important; opacity: 1; }
.submit-btn {
    background-color: #003366;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    transition: 0.3s;
    font-size: 14px;
}
        .submit-btn:hover { background-color: #002244; transform: translateY(-2px); }
    </style>
</head>
<body>

<section class="header-navbar">
    <?php include "static/navbar.php" ?>
</section>

<div class="container py-5" style="max-width: 1000px;">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="color: #003366; letter-spacing: 1px;">Personal Loan Risk Assessment System</h2>
    </div>

    <!-- ONLY CHANGE: added action="preview.php" -->
    <form method="POST" action="personal-preview.php">

        <div class="custom-card p-4">
            <h5 class="section-title">I. Applicant Profile</h5>
            <div class="row g-3">

                <div class="col-md-4">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" placeholder="Juan Pedro" required>
                </div>

                <div class="col-md-3">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" placeholder="Santiago" required>
                </div>

                <div class="col-md-3">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required>
                </div>

                <div class="col-md-2">
                    <label>Suffix</label>
                    <input type="text" name="suffix_name" class="form-control" placeholder="Jr.">
                </div>

                <div class="col-md-3">
                    <label>Birthdate</label>
                    <input type="date" id="birthdate" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label>Age</label>
                    <input type="number" name="age" id="age" class="form-control bg-readonly" readonly>
                </div>

                <div class="col-md-3">
                    <label>Sex</label>
                    <select name="sex" class="form-select" required>
                        <option value="1">Male</option>
                        <option value="0">Female</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Civil Status</label>
                    <select name="civil_status" class="form-select" required>
                        <option value="0">Single</option>
                        <option value="1">Married</option>
                    </select>
                </div>

                <div class="col-12">
                    <label>Current Home Address</label>
                    <input type="text" name="home_address" class="form-control" placeholder="Unit No, Street, Brgy, City, Province" required>
                </div>

            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">II. Financial</h5>

            <div class="row g-3">

                <div class="col-md-3">
                    <label>Income</label>
                    <input type="number" name="monthly_income" class="form-control" value="0" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Credit Score</label>
                    <input type="number" name="credit_score" class="form-control" value="0" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>DTI Ratio</label>
                    <input type="number" name="dti_ratio" class="form-control" value="0" required>
                </div>

            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">III. Loan Details</h5>

            <div class="row g-3">

                <div class="col-md-4">
                    <label>Loan Amount</label>
                    <input type="number" name="loan_amount" class="form-control" value="0" required>
                </div>

                <div class="col-md-4">
                    <label>Loan Term</label>
                    <input type="number" name="loan_term" class="form-control" value="0" required>
                </div>

                <div class="col-md-4">
                    <label>Loan Intent</label>
                    <select name="loan_intent" class="form-select" required>
                        <option value="Medical">Medical</option>
                        <option value="Car">Car</option>
                        <option value="Business">Business</option>
                        <option value="Education">Education</option>
                        <option value="Home Improvement">Home Improvement</option>
                        <option value="Debt Consolidation">Debt Consolidation</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">IV. Past Loan Profile</h5>

            <div class="row g-3">

                <div class="col-md-3">
                    <label>Existing Loans</label>
                    <select name="existing_loans" class="form-select" required>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Default History</label>
                    <select name="default_history" class="form-select" required>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">V. Contact Details</h5>

            <div class="row g-3">

                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="juandelacruz@email.com" required>
                </div>

                <div class="col-md-6">
                    <label>Contact No.</label>
                    <input type="text" name="contact_no" class="form-control" required>
                </div>

            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary submit-btn shadow">
                Submit Application
            </button>
        </div>

    </form>
</div>

<?php include "static/footer.php" ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="static/home-form.js?v=<?= time() ?>"></script>

</body>
</html>