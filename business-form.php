<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

$form = $_SESSION['business_form'] ?? [];

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$role = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentStep = isset($_POST['step']) ? (int)$_POST['step'] : 1;
    foreach ($_POST as $key => $value) {
        if ($key !== 'step') {
            $_SESSION['business_form'][$key] = $value;
        }
    }
} else {
    $currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
}

if ($currentStep < 1 || $currentStep > 5) $currentStep = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/businessformsstyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <title>Business Loan Risk Assessment</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <!-- 5 - Highly Satisfactory, 4 - Satisfactory, 3 - Neutral, 2 - Unsatisfactory, 1 - Highly Unsatisfactory -->

    <section class="container">
        <h1 style="text-align: center; padding-bottom:20px;">Business Loan Risk Assessment</h1>
        <div class="step-container">
            <a href="business-form.php?step=1"><div class="step <?= $currentStep === 1 ? 'active' : '' ?>">A. Basic Details</div></a>
            <a href="business-form.php?step=2"><div class="step <?= $currentStep === 2 ? 'active' : '' ?>">B. Financial Condition</div></a>
            <a href="business-form.php?step=3"><div class="step <?= $currentStep === 3 ? 'active' : '' ?>">C. Industry/Market Analysis</div></a>
            <a href="business-form.php?step=4"><div class="step <?= $currentStep === 4 ? 'active' : '' ?>">D. Management Quality</div></a>
            <div class="step <?= $currentStep === 5 ? 'active' : '' ?>">Preview</div>
        </div>

        <div class="business-form-container">
            <form method="POST" action="business-submit.php">
                <input type="hidden" name="step" value="<?= $currentStep ?>">

                <!-- A. BASIC DETAILS -->
                <?php if ($currentStep == 1): ?>
                    <div class="company-basic-details-form">
                        <h3>Step 1 – Client Basic Details</h3>
                        <div class="business-form-item">
                            <label for="company_name">Company Name:</label>
                            <input type="text" id="company_name" name="company_name" required value="<?= $_SESSION['business_form']['company_name'] ?? '' ?>">
                        </div>

                        <div class="business-form-item">
                            <label for="loan_amount">Loan Amount:</label>
                            <input type="number" id="loan_amount" name="loan_amount" required value="<?= $_SESSION['business_form']['loan_amount'] ?? '' ?>">
                        </div>

                        <div class="business-form-item">
                            <label for="loan_term">Loan Term (Months):</label>
                            <input type="number" id="loan_term" name="loan_term" required value="<?= $_SESSION['business_form']['loan_term'] ?? '' ?>">
                        </div>
                        <br>
                        <div class="button-container">
                            <a href="business-form.php?step=2" class="next-button">Next <i class="fa-solid fa-caret-right"></i></a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- B. Financial Condition -->
                <?php include "business-form-pages/b-financial-condition.php"?>

                <!-- C. Industry/Market Analysis -->
                <?php include "business-form-pages/c-industry-market-analysis.php"?>

                <!-- D. Management quality -->
                <?php include "business-form-pages/d-management-quality.php"?>

                <!-- Last Page - Preview -->
                <?php if ($currentStep == 5): ?>
                <?php include "business-form-preview.php"?>
                <input type="hidden" name="company_name" value="<?= htmlspecialchars($form['company_name'] ?? '') ?>">
                <div class="button-container">
                    <a href="business-form.php?step=4" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</a>
                        <br><br>
                    <button type="submit" id="submitAssessmentBtn" class="submit-btn">Submit Application</button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // select all inputs (radio, text, number)
        const inputs = document.querySelectorAll('input');

        inputs.forEach(input => {
            input.addEventListener('change', () => {
                const data = new FormData();
                data.append(input.name, input.value);
                data.append('step', <?= $currentStep ?>);

                fetch('business-form-save.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.text())
                .then(res => console.log(res))
                .catch(err => console.error('Save failed', err));
            });
        });
    });
    </script>

    <script src="static/js/business-form.js?v=<?= time() ?>"></script>
    <?php include "static/footer.php"?>
</body>
</html>