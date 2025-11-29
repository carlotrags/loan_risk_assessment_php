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

<?php
// htdocs/pr
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and store POST data
    $name = htmlspecialchars($_POST['name']);
    $income = htmlspecialchars($_POST['income']);
    $credit_score = htmlspecialchars($_POST['credit_score']);
    $loan_amount = htmlspecialchars($_POST['loan_amount']);
    $loan_term = htmlspecialchars($_POST['loan_term']);
    $previous_defaults = htmlspecialchars($_POST['previous_defaults']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Preview Loan Application</title>
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>
    <div class="container">
        <div class="preview-wrapper">
            <div class="card">
                <div class="preview-card">
                    <h1>Preview Loan Details</h1>
                        <ul>
                            <li><strong>Name:</strong> <?= $name ?></li>
                            <li><strong>Income:</strong> <?= $income ?></li>
                            <li><strong>Credit Score:</strong> <?= $credit_score ?></li>
                            <li><strong>Loan Amount:</strong> <?= $loan_amount ?></li>
                            <li><strong>Loan Term:</strong> <?= $loan_term ?> months</li>
                            <li><strong>Previous Defaults:</strong> <?= $previous_defaults ?></li>
                        </ul>


                    <form action="submit.php" method="post">
                        <input type="hidden" name="name" value="<?= $name ?>">
                        <input type="hidden" name="income" value="<?= $income ?>">
                        <input type="hidden" name="credit_score" value="<?= $credit_score ?>">
                        <input type="hidden" name="loan_amount" value="<?= $loan_amount ?>">
                        <input type="hidden" name="loan_term" value="<?= $loan_term ?>">
                        <input type="hidden" name="previous_defaults" value="<?= $previous_defaults ?>">
                        <input type="hidden" name="loan_type" value="personal">

                        <label><input type="checkbox" id="confirmCheckbox">I have confirmed that the details above are correct.</label>
                        <button id="backBtn" onclick="history.back()">Back</button>
                        <button id="confirmAssessmentBtn" disabled>Start Assessment</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <?php include "static/footer.php"?>
    <script>
        const checkbox = document.getElementById('confirmCheckbox');
        const button = document.getElementById('confirmAssessmentBtn');

        checkbox.addEventListener('change', () =>{
            if(checkbox.checked){
                button.disabled = false;
                button.classList.add('active');
            } else {
                button.disabled = true;
                button.classList.remove('active');
            }
        });
    </script>
</body>
</html>
