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
            <form method="POST" action="business-form.php">
                <input type="hidden" name="step" value="<?= $currentStep ?>">

                <!-- A. BASIC DETAILS -->
                <?php if ($currentStep === 1): ?>
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
                            <input type="number" id="loan_term" name="loan_term" value="12" required value="<?= $_SESSION['business_form']['loan_term'] ?? '' ?>">
                        </div>
                        <br>
                        <button type="submit" class="next-button">Next <i class="fa-solid fa-caret-right"></i></button>
                    </div>
                <?php endif; ?>

                <!-- B. Financial Condition -->
                <?php if ($currentStep === 2): ?>
                    <h3>Step 2 – Financial Condition</h3>
                    <table class="business-form-table">
                        <thead>
                            <tr>
                                <th>Variable</th>
                                <th>1 - Highly Unsatisfactory</th>
                                <th>2 - Unsatisfactory</th>
                                <th>3 - Neutral</th>
                                <th>4 - Satisfactory</th>
                                <th>5 - Highly Satisfactory</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Capital to Risk Assets Ratio (%)
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="1" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='1') ? 'checked' : '' ?>>This company's blabla is bad</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="2" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='2') ? 'checked' : '' ?>>This company's blabla needs improvement</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="3" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='3') ? 'checked' : '' ?>>The company is normal</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="4" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='4') ? 'checked' : '' ?>>This company's blabla is good</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="5" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='5') ? 'checked' : '' ?>>This company has outstanding ---</td>
                            </tr>

                            <tr>
                                <td>Debt-to-Equity Ratio (X)
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="1" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="2" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="3" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="4" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="5" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>

                            <tr>
                                <td>NPL Ratio
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="npl_ratio" value="1" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="npl_ratio" value="2" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="npl_ratio" value="3" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="npl_ratio" value="4" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="npl_ratio" value="5" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>

                            <tr>
                                <td>NPA Ratio
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="npa_ratio" value="1" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="npa_ratio" value="2" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="npa_ratio" value="3" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="npa_ratio" value="4" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="npa_ratio" value="5" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>

                            <tr>
                                <td>NPA Coverage Ratio
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="npa_coverage_ratio" value="1" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="2" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="3" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="4" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="5" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>ROAE
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="roae" value="1" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="roae" value="2" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="roae" value="3" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="roae" value="4" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="roae" value="5" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>

                            <tr>
                                <td>ROAA
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="roaa" value="1" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="roaa" value="2" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="roaa" value="3" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="roaa" value="4" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="roaa" value="5" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>

                            <tr>
                                <td>Cost to Income Ratio
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="cost_to_income_ratio" value="1" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="2" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="3" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="4" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="5" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>

                            <tr>
                                <td>Liquid Assets to Borrowed Funds
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="1" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="2" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="3" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="4" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="5" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>

                            <tr>
                                <td>Debt Service Cover (X)
                                    <p class="variable-details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Temporibus, id!</p>
                                </td>
                                <td><input type="radio" name="debt_service_cover" value="1" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="debt_service_cover" value="2" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                <td><input type="radio" name="debt_service_cover" value="3" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                <td><input type="radio" name="debt_service_cover" value="4" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                <td><input type="radio" name="debt_service_cover" value="5" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="submit" name="step" value="1" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</button>
                    <button type="submit" name="step" value="3" class="next-button">Next <i class="fa-solid fa-caret-right"></i></button>
                    <?php endif; ?>

                    
                    <!-- C. Industry/Market Analysis -->
                    <?php if ($currentStep == 3): ?>
                    <h3>Step 3 – Industry/Market Analysis</h3>
                        <table class="business-form-table">
                            <thead>
                                <tr>
                                    <th>Variable</th>
                                    <th>5 - Highly Satisfactory</th>
                                    <th>4 - Satisfactory</th>
                                    <th>3 - Neutral</th>
                                    <th>2 - Unsatisfactory</th>
                                    <th>1 - Highly Unsatisfactory</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Porters 1: Threat of Entry</td>
                                    <td><input type="radio" name="threat_of_entry" value="1" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="threat_of_entry" value="2" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="threat_of_entry" value="3" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="threat_of_entry" value="4" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="threat_of_entry" value="5" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Porters 2: Intensity of Rivalry</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="1" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="2" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="3" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="4" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="5" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Porters 3: Substitution of Threat</td>
                                    <td><input type="radio" name="substitution_of_threat" value="1" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="substitution_of_threat" value="2" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="substitution_of_threat" value="3" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="substitution_of_threat" value="4" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="substitution_of_threat" value="5" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Porters 4: Buyer Bargaining Power</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="1" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="2" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="3" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="4" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="5" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Porters 5: Supplier Bargaining Power</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="1" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="2" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="3" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="4" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="5" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Overall Industry Outlook</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="1" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="2" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="3" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="4" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="5" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Market Position</td>
                                    <td><input type="radio" name="market_position" value="1" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="market_position" value="2" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="market_position" value="3" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="market_position" value="4" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="market_position" value="5" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                        </table>
                        <a href="business-form.php?step=2" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</a>
                        <a href="business-form.php?step=4"class="next-button">Next <i class="fa-solid fa-caret-right"></i></a>
                    <?php endif; ?>

                    <!-- D. Management quality -->
                    <?php if ($currentStep == 4): ?>
                    <h3>Step 4 – Management Quality</h3>
                        <table class="business-form-table">
                            <thead>
                                <tr>
                                    <th>Variable</th>
                                    <th>5 - Highly Satisfactory</th>
                                    <th>4 - Satisfactory</th>
                                    <th>3 - Neutral</th>
                                    <th>2 - Unsatisfactory</th>
                                    <th>1 - Highly Unsatisfactory</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Character of Management</td>
                                    <td><input type="radio" name="character_of_management" value="1" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="character_of_management" value="2" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="character_of_management" value="3" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="character_of_management" value="4" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="character_of_management" value="5" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Quality and Experience of Management</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="1" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="2" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="3" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="4" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="5" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Bank Relationship</td>
                                    <td><input type="radio" name="bank_relationship" value="1" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="bank_relationship" value="2" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="bank_relationship" value="3" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="bank_relationship" value="4" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="bank_relationship" value="5" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Labor Relations</td>
                                    <td><input type="radio" name="labor_relations" value="1" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="labor_relations" value="2" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="labor_relations" value="3" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="labor_relations" value="4" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="labor_relations" value="5" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Existence</td>
                                    <td><input type="radio" name="existence" value="1" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="existence" value="2" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="existence" value="3" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="existence" value="4" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="existence" value="5" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>NFIS/CMAP Checkings</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="1" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="2" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="3" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="4" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="5" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Management Control and Business Planning</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="1" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="2" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="3" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="4" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="5" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Management Structure and Successtion Strategy</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="1" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="2" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="3" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="4" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="5" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                                <tr>
                                    <td>Clear Long-Term Management Strategy</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="1" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='1') ? 'checked' : '' ?>>1 - Highly Unsatisfactory</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="2" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='2') ? 'checked' : '' ?>>2 - Unsatisfactory</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="3" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='3') ? 'checked' : '' ?>>3 - Neutral</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="4" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='4') ? 'checked' : '' ?>>4 - Satisfactory</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="5" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='5') ? 'checked' : '' ?>>5 - Highly Satisfactory</td>
                                </tr>
                            </table>
                            <a href="business-form.php?step=3" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</a>
                            <a href="business-form.php?step=5" class="next-button">Next <i class="fa-solid fa-caret-right"></i></a>
                        <?php endif; ?>

                    <?php if ($currentStep == 5): ?>
                    <?php include "business-form-preview.php"?>
                    <a href="business-form.php?step=4" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</a>
                    <br><br>
                    <button type="submit" class="submit-btn">Submit</button>
                    <?php endif; ?>
            </form>
        </div>
    </section>

    <script>
    document.querySelectorAll('.step-container a').forEach(link => {
        link.addEventListener('click', function(e){
            e.preventDefault();
            const form = document.querySelector('form');
            if(form){
                let stepInput = document.createElement('input');
                stepInput.type = 'hidden';
                stepInput.name = 'step';
                stepInput.value = this.href.split('step=')[1];
                form.appendChild(stepInput);
                
                form.submit();
            } else {
                window.location.href = this.href;
            }
        });
    });

    // window.addEventListener('beforeunload', function() {
    //     navigator.sendBeacon('static/clear_business_form.php');
    // });
    </script>

    <script src="static/form.js" defer></script>
    <?php include "static/footer.php"?>
</body>
</html> 