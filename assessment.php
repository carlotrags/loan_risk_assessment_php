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

    <title>Loan Risk Assessment</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        
        <div class="assessment-type-container">
            <h1>Choose a Loan Type to Assess Risk:</h1>
            <a href="form.php"><div class="assessment-type">
                <h3><i class="fa-solid fa-user"></i> Personal Loan</h3>
                <p>Loan for personal needs like emergencies, education, or purchases. No collateral required.</p>
            </div></a>

            <a href="business-form.php"><div class="assessment-type">
                <h3><i class="fa-solid fa-briefcase"></i> Business Loan</h3>
                <p>Financing for business expenses such as operations, expansion, or equipment.</p>
            </div></a>
            
            <a href="home-form.php"><div class="assessment-type">
                <h3><i class="fa-solid fa-house"></i> Home Loan</h3>
                <p>Loan for buying, building, or improving a property, usually secured by the property itself.</p>
            </div></a>
            
        </div>
    </section>
    <script src="static/form.js" defer></script>
    <?php include "static/footer.php"?>
</body>
</html>