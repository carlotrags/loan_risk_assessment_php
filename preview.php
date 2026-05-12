<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

// Applicant Profile
$first_name = htmlspecialchars($_POST['first_name'] ?? '');
$middle_name = htmlspecialchars($_POST['middle_name'] ?? '');
$last_name = htmlspecialchars($_POST['last_name'] ?? '');
$suffix_name = htmlspecialchars($_POST['suffix_name'] ?? '');
$full_name = trim("$first_name $middle_name $last_name $suffix_name");

$home_address = htmlspecialchars($_POST['home_address'] ?? '');
$age = htmlspecialchars($_POST['age'] ?? '');
$sex = $_POST['sex'] ?? '';
$civil_status = $_POST['civil_status'] ?? '';

// Financial
$monthly_income = htmlspecialchars($_POST['monthly_income'] ?? 0);
$credit_score = htmlspecialchars($_POST['credit_score'] ?? 0);
$dti_ratio = htmlspecialchars($_POST['dti_ratio'] ?? 0);

// Loan Details
$loan_amount = htmlspecialchars($_POST['loan_amount'] ?? 0);
$loan_term = htmlspecialchars($_POST['loan_term'] ?? 0);
$loan_intent = htmlspecialchars($_POST['loan_intent'] ?? '');

// History
$existing_loans = $_POST['existing_loans'] ?? 0;
$default_history = $_POST['default_history'] ?? 0;

// Contact
$email = htmlspecialchars($_POST['email'] ?? '');
$contact_no = htmlspecialchars($_POST['contact_no'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Preview Personal Loan Application</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJq0Xgk0v3Q9GqQ4jKk0rQ5F5p1bQ6I6Qe5Q5Q5Q5Q5Q5Q" crossorigin="anonymous">
    <link rel="stylesheet" href="static/css/home-preview-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">

<style>
.preview-table { width:100%; border-collapse:collapse; font-size:0.9rem; margin-bottom:20px; }
.section-header { background:#003366; color:#fff; font-weight:bold; }
.variable-row td { background:#f8f9fa; font-weight:600; border:1px solid #dee2e6; padding:8px; }
.answer-row td { border:1px solid #dee2e6; padding:8px; }
.preview-card { background:#fff; padding:25px; border-radius:10px; box-shadow:0 3px 12px rgba(0,0,0,0.1); }
</style>
</head>

<body>

<?php include "static/navbar.php" ?>

<div class="container">
<div class="preview-card">

<h2 class="mb-4 text-center" style="color:#003366;">
Review Personal Loan Application
</h2>

<!-- A. APPLICANT -->
<table class="preview-table">
<tr class="section-header">
<td colspan="5">A. Applicant Details</td>
</tr>

<tr class="variable-row">
<td>Full Name</td>
<td>Age</td>
<td>Sex</td>
<td>Civil Status</td>
<td>Address</td>
</tr>

<tr class="answer-row">
<td><?= $full_name ?></td>
<td><?= $age ?></td>
<td><?= $sex == 1 ? 'Male' : 'Female' ?></td>
<td><?= $civil_status == 1 ? 'Married' : 'Single' ?></td>
<td><?= $home_address ?></td>
</tr>
</table>

<!-- B. FINANCIAL -->
<table class="preview-table">
<tr class="section-header">
<td colspan="4">B. Financial Information</td>
</tr>

<tr class="variable-row">
<td>Monthly Income</td>
<td>Credit Score</td>
<td>DTI Ratio</td>
<td>Email</td>
</tr>

<tr class="answer-row">
<td>₱<?= number_format($monthly_income, 2) ?></td>
<td><?= $credit_score ?></td>
<td><?= $dti_ratio ?>%</td>
<td><?= $email ?></td>
</tr>
</table>

<!-- C. LOAN DETAILS -->
<table class="preview-table">
<tr class="section-header">
<td colspan="4">C. Loan Details</td>
</tr>

<tr class="variable-row">
<td>Loan Amount</td>
<td>Loan Term</td>
<td>Loan Intent</td>
<td>Contact No</td>
</tr>

<tr class="answer-row">
<td>₱<?= number_format($loan_amount, 2) ?></td>
<td><?= $loan_term ?> months</td>
<td><?= $loan_intent ?></td>
<td><?= $contact_no ?></td>
</tr>
</table>

<!-- D. HISTORY -->
<table class="preview-table">
<tr class="section-header">
<td colspan="3">D. Credit History</td>
</tr>

<tr class="variable-row">
<td>Existing Loans</td>
<td>Default History</td>
<td>Status</td>
</tr>

<tr class="answer-row">
<td><?= $existing_loans == 1 ? 'Yes' : 'No' ?></td>
<td><?= $default_history == 1 ? 'Yes' : 'No' ?></td>
<td><span class="badge bg-success">Ready for Assessment</span></td>
</tr>
</table>

<!-- FORM SUBMIT -->
<form method="POST" action="personal-form.php">

<!-- IMPORTANT FLAG FOR PERSONAL-FORM.PHP -->
<input type="hidden" name="is_final_submission" value="1">

<?php foreach ($_POST as $key => $value): ?>
    <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
<?php endforeach; ?>

<div class="form-check mb-3">
    <input type="checkbox" id="confirmCheck" class="form-check-input">
    <label class="form-check-label">
        I confirm that all information is correct.
    </label>
</div>

<div class="d-flex justify-content-end gap-2">
    <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
    <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
        Start Assessment
    </button>
</div>

</form>

</div>
</div>

<?php include "static/footer.php" ?>

<script>
document.getElementById('confirmCheck').addEventListener('change', function () {
    document.getElementById('submitBtn').disabled = !this.checked;
});
</script>

</body>
</html>