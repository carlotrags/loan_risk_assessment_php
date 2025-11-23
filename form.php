<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
  <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

  <title>Personal Loan Risk Assessment</title>
</head>
<body>
  <section class="header-navbar">
    <?php include "static/navbar.php"?>
  </section>

  <section class="container">
    <div class="form-wrapper">
      <div class="card">
        <div class="form-card">
          <h1>Loan Risk Assessment</h1>
          <p class="subtitle">Fill out the form to check loan risk.</p>
          <form action="preview.php" method="post">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required />
          </div>
          <div class="form-group">
            <label for="income">Monthly Income</label>
            <input type="number" id="income" name="income" required />
          </div>
          <div class="form-group">
            <label for="credit_score">Credit Score</label>
            <input type="number" id="credit_score" name="credit_score" required />
          </div>
          <div class="form-group">
            <label for="loan_amount">Loan Amount</label>
            <input type="number" id="loan_amount" name="loan_amount" required />
          </div>
          <div class="form-group">
            <label for="loan_term">Loan Term (months)</label>
            <input type="number" id="loan_term" name="loan_term" value="12" required />
          </div>
          <div class="form-group">
            <label for="previous_defaults">Previous Defaults</label>
            <select id="previous_defaults" name="previous_defaults" required>
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
          <button type="submit" id="submitAssessmentBtn">Submit Application</button>
        </form>
      </div>
    </div>
  </section>
  <script src="static/form.js" defer></script>
  <?php include "static/footer.php"?>
</body>
</html>