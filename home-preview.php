<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

// Map lahat ng POST data para maipasa sa hidden inputs later
$name = htmlspecialchars($_POST['applicant_name'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$tin_no = htmlspecialchars(trim($_POST['tin_no'] ?? ''), ENT_QUOTES, 'UTF-8');
$birthdate = htmlspecialchars($_POST['birthdate'] ?? '');
$address = htmlspecialchars(trim($_POST['home_address'] ?? ''), ENT_QUOTES, 'UTF-8');
$collateral_type = htmlspecialchars($_POST['collateral_type'] ?? '');
$property_value = htmlspecialchars($_POST['property_value'] ?? '0');

// Map numerical fields for ML model
$age = htmlspecialchars($_POST['age'] ?? '');
$sex = htmlspecialchars($_POST['sex'] ?? '');
$civil_status = htmlspecialchars($_POST['civil_status'] ?? '');
$dependents = htmlspecialchars($_POST['dependents'] ?? '');
$years_of_stay = htmlspecialchars($_POST['years_of_stay'] ?? '');
$home_ownership = htmlspecialchars($_POST['home_ownership'] ?? '');
$employment_type = htmlspecialchars($_POST['employment_type'] ?? '');
$monthly_income = htmlspecialchars($_POST['monthly_income'] ?? '');
$years_employed = htmlspecialchars($_POST['years_employed'] ?? '');
$loan_amount = htmlspecialchars($_POST['loan_amount'] ?? '');
$loan_term = htmlspecialchars($_POST['loan_term'] ?? '');
$existing_loans = htmlspecialchars($_POST['existing_loans'] ?? '');
$monthly_debt = htmlspecialchars($_POST['monthly_debt'] ?? '');
$dti_ratio = htmlspecialchars($_POST['dti_ratio'] ?? '');
$default_history = htmlspecialchars($_POST['default_history'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview Home Loan Application</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJq0Xgk0v3Q9GqQ4jKk0rQ5F5p1bQ6I6Qe5Q5Q5Q5Q5Q5Q" crossorigin="anonymous">
    <link rel="stylesheet" href="static/css/home-preview-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <style>
        .preview-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem; }
        .section-header { background-color: #003366; color: white; font-weight: bold; padding: 10px; }
        .variable-row td { background-color: #f8f9fa; font-weight: 600; padding: 8px; border: 1px solid #dee2e6; }
        .answer-row td { padding: 8px; border: 1px solid #dee2e6; }
        .container { margin-top: 30px; margin-bottom: 50px; }
    </style>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php" ?>
    </section>
<div class="preview-page">
    <div class="container">

        <div class="preview-card">

            <h2 class="preview-title">
                Review Home Loan Details
            </h2>

            <!-- A -->
            <div class="preview-table-wrapper">
                <table class="preview-table">
                    <tr class="section-header">
                        <td colspan="5">A. Client Details</td>
                    </tr>

                    <tr class="variable-row">
                        <td>Full Name</td>
                        <td>Email Address</td>
                        <td>TIN No.</td>
                        <td>Birthdate</td>
                        <td>Address</td>
                    </tr>

                    <tr class="answer-row">
                        <td><?= $name ?></td>
                        <td><?= $email ?></td>
                        <td><?= $tin_no ?></td>
                        <td><?= $birthdate ?></td>
                        <td><?= $address ?></td>
                    </tr>
                </table>
            </div>

            <!-- B -->
            <div class="preview-table-wrapper">
                <table class="preview-table">

                    <tr class="section-header">
                        <td colspan="5">B. Personal & Financial Status</td>
                    </tr>

                    <tr class="variable-row">
                        <td>Age</td>
                        <td>Sex</td>
                        <td>Civil Status</td>
                        <td>Dependents</td>
                        <td>Years of Stay</td>
                    </tr>

                    <tr class="answer-row">
                        <td><?= $age ?></td>
                        <td><?= $sex == 1 ? 'Male' : 'Female' ?></td>
                        <td><?= $civil_status == 1 ? 'Married' : 'Single' ?></td>
                        <td><?= $dependents ?></td>
                        <td><?= $years_of_stay ?></td>
                    </tr>

                    <tr class="variable-row">
                        <td>Home Ownership</td>
                        <td>Employment Type</td>
                        <td>Monthly Income</td>
                        <td>Years Employed</td>
                        <td>DTI Ratio (%)</td>
                    </tr>

                    <tr class="answer-row">
                        <td><?= $home_ownership == 1 ? 'Owned' : 'Rent/Mortgaged' ?></td>
                        <td><?= $employment_type ?></td>
                        <td>₱<?= number_format($monthly_income, 2) ?></td>
                        <td><?= $years_employed ?> yrs</td>
                        <td><?= $dti_ratio ?>%</td>
                    </tr>

                </table>
            </div>

            <!-- C -->
            <div class="preview-table-wrapper">
                <table class="preview-table">

                    <tr class="section-header">
                        <td colspan="4">C. Loan & Collateral Details</td>
                    </tr>

                    <tr class="variable-row">
                        <td>Requested Amount</td>
                        <td>Loan Term</td>
                        <td>Collateral Type</td>
                        <td>Property Value</td>
                    </tr>

                    <tr class="answer-row">
                        <td class="fw-bold text-primary">
                            ₱<?= number_format($loan_amount, 2) ?>
                        </td>

                        <td><?= $loan_term ?> months</td>
                        <td><?= $collateral_type ?></td>
                        <td>₱<?= number_format($property_value, 2) ?></td>
                    </tr>

                    <tr class="variable-row">
                        <td>Existing Loans</td>
                        <td>Monthly Debt</td>
                        <td>Default History</td>
                        <td>Status</td>
                    </tr>

                    <tr class="answer-row">
                        <td><?= $existing_loans == 1 ? 'Yes' : 'No' ?></td>
                        <td>₱<?= number_format($monthly_debt, 2) ?></td>
                        <td><?= $default_history == 1 ? 'Yes' : 'No' ?></td>
                        <td>
                            <span class="badge bg-success">
                                Ready for Assessment
                            </span>
                        </td>
                    </tr>

                </table>
            </div>

            <form action="home-submit.php" method="post">

                <!-- Hidden Inputs -->
                <input type="hidden" name="applicant_name" value="<?= $name ?>">
                <input type="hidden" name="tin_no" value="<?= htmlspecialchars($tin_no, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="birthdate" value="<?= $birthdate ?>">
                <input type="hidden" name="home_address" value="<?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="collateral_type" value="<?= $collateral_type ?>">
                <input type="hidden" name="property_value" value="<?= $property_value ?>">
                <input type="hidden" name="age" value="<?= $age ?>">
                <input type="hidden" name="sex" value="<?= $sex ?>">
                <input type="hidden" name="civil_status" value="<?= $civil_status ?>">
                <input type="hidden" name="dependents" value="<?= $dependents ?>">
                <input type="hidden" name="years_of_stay" value="<?= $years_of_stay ?>">
                <input type="hidden" name="home_ownership" value="<?= $home_ownership ?>">
                <input type="hidden" name="employment_type" value="<?= $employment_type ?>">
                <input type="hidden" name="monthly_income" value="<?= $monthly_income ?>">
                <input type="hidden" name="years_employed" value="<?= $years_employed ?>">
                <input type="hidden" name="loan_amount" value="<?= $loan_amount ?>">
                <input type="hidden" name="loan_term" value="<?= $loan_term ?>">
                <input type="hidden" name="existing_loans" value="<?= $existing_loans ?>">
                <input type="hidden" name="monthly_debt" value="<?= $monthly_debt ?>">
                <input type="hidden" name="dti_ratio" value="<?= $dti_ratio ?>">
                <input type="hidden" name="default_history" value="<?= $default_history ?>">

                <div class="confirm-box mb-4">

                    <div class="form-check">
                        <input
                            type="checkbox"
                            id="confirmCheckbox"
                            class="form-check-input">

                        <label class="form-check-label fw-semibold">
                            I confirm that the details above are correct
                            for the Home Loan assessment.
                        </label>
                    </div>

                </div>

                <div class="preview-actions d-flex justify-content-end gap-3">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="history.back()">

                        <i class="bi bi-arrow-left me-2"></i>
                        Back
                    </button>

                    <button
                        type="submit"
                        id="confirmAssessmentBtn"
                        class="btn btn-primary"
                        disabled>

                        Start Assessment

                    </button>

                </div>

            </form>

        </div>

    </div>
</div>
    <?php include "static/footer.php" ?>

    <script>
        // Logic to enable button only if checkbox is checked
        const checkbox = document.getElementById('confirmCheckbox');
        const button = document.getElementById('confirmAssessmentBtn');

        checkbox.addEventListener('change', () => {
            button.disabled = !checkbox.checked;
        });
    </script>
</body>
</html>