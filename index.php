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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/style.css?v=<?= time() ?>">
    <title>Loan Risk Assessment</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="header-body">
            <h3>Welcome to the Loan Risk Assessment Website, <?= htmlspecialchars($last_name) ?></h3>
            <div class="tutorial-body">
                <h3>How does the assessment work?</h3>
                <ol>
                    <li>Go to the assessment tab</li>
                    <li>Fill up the form according to the client's required details</li>
                    <li>Submit assessment and let the system compute the results</li>
                    <li>The results will appear after the system calculates, showing if the client is eligible or ineligible for a loan</li>
                </ol>
            </div>
            <p>Click the button below to start Risk Assessment.</p>
            <a href="form.php"><button>Start Assessment</button></a>
        </div>
        <br>
        
        <br>
        <div class="system-details-body">
            <p>This website is created using -----. The system utilizes Logistic Regression for its assessment.</p>
        </div>
    </section>
    <?php include "static/footer.php"?>
</body>
</html>