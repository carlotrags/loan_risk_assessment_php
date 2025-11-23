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

$currentStep = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($currentStep < 1 || $currentStep > 4){
    $currentStep = 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $_SESSION['business_form'][$key] = $value;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <a href="business-form.php?step=1"><div class="step <?= $currentStep === 1 ? 'active' : '' ?>">A. Financial Condition</div></a>
            <a href="business-form.php?step=2"><div class="step <?= $currentStep === 2 ? 'active' : '' ?>">B. Industry/Market Analysis</div></a>
            <a href="business-form.php?step=3"><div class="step <?= $currentStep === 3 ? 'active' : '' ?>">C. Management Quality</div></a>
            <div class="step <?= $currentStep === 4 ? 'active' : '' ?>">Review</div>
        </div>

        <div class="business-forms-container">
            <form action="business_forms.php?step=<?= $currentStep + 1 ?>" method="POST">
            <?php if ($currentStep === 1): ?>
                <h3>Step 1 – Financial Condition</h3>
                <table class="table table-boredered text-center">
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
                            <td><input type="radio" name="capital_to_risk_assets_ratio" value="1">This company's blabla is bad</td>
                            <td><input type="radio" name="capital_to_risk_assets_ratio" value="2">This company's blabla needs improvement</td>
                            <td><input type="radio" name="capital_to_risk_assets_ratio" value="3">The company is normal</td>
                            <td><input type="radio" name="capital_to_risk_assets_ratio" value="4">This company's blabla is good</td>
                            <td><input type="radio" name="capital_to_risk_assets_ratio" value="5">This company has outstanding ---</td>
                        </tr>
                        <tr>
                            <td>Debt-to-Equity Ratio (X)</td>
                            <td><input type="radio" name="debt_to_equity_ratio" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="debt_to_equity_ratio" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="debt_to_equity_ratio" value="3">3 - Neutral</td>
                            <td><input type="radio" name="debt_to_equity_ratio" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="debt_to_equity_ratio" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>NPL Ratio</td>
                            <td><input type="radio" name="npl_ratio" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="npl_ratio" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="npl_ratio" value="3">3 - Neutral</td>
                            <td><input type="radio" name="npl_ratio" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="npl_ratio" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>NPA Ratio</td>
                            <td><input type="radio" name="npa_ratio" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="npa_ratio" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="npa_ratio" value="3">3 - Neutral</td>
                            <td><input type="radio" name="npa_ratio" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="npa_ratio" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>NPA Coverage Ratio</td>
                            <td><input type="radio" name="npa_coverage_ratio" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="npa_coverage_ratio" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="npa_coverage_ratio" value="3">3 - Neutral</td>
                            <td><input type="radio" name="npa_coverage_ratio" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="npa_coverage_ratio" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>ROAE</td>
                            <td><input type="radio" name="roae" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="roae" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="roae" value="3">3 - Neutral</td>
                            <td><input type="radio" name="roae" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="roae" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>ROAA</td>
                            <td><input type="radio" name="roaa" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="roaa" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="roaa" value="3">3 - Neutral</td>
                            <td><input type="radio" name="roaa" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="roaa" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>Cost to Income Ratio</td>
                            <td><input type="radio" name="cost_to_income_ratio" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="cost_to_income_ratio" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="cost_to_income_ratio" value="3">3 - Neutral</td>
                            <td><input type="radio" name="cost_to_income_ratio" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="cost_to_income_ratio" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>Liquid Assets to Borrowed Funds</td>
                            <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="3">3 - Neutral</td>
                            <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="5">5 - Highly Satisfactory</td>
                        </tr>
                        <tr>
                            <td>Debt Service Cover (X)</td>
                            <td><input type="radio" name="debt_service_cover" value="1">1 - Highly Unsatisfactory</td>
                            <td><input type="radio" name="debt_service_cover" value="2">2 - Unsatisfactory</td>
                            <td><input type="radio" name="debt_service_cover" value="3">3 - Neutral</td>
                            <td><input type="radio" name="debt_service_cover" value="4">4 - Satisfactory</td>
                            <td><input type="radio" name="debt_service_cover" value="5">5 - Highly Satisfactory</td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex justify-content-center gap-3">
                    <a href="business-form.php?step=2" class="next-button btn">Next <i class="fa-solid fa-caret-right"></i></a>
                </div>
                <?php endif; ?>
                
                <!-- Step 2 = Industry/Market Analysis -->
                <?php if ($currentStep == 2): ?>
                <h3>Step 2 – Industry/Market Analysis</h3>
                <form action="submit_business.php?step=3" method="POST">
                    <table class="table table-boredered text-center">
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
                                <td><input type="radio" name="threat_of_entry" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="threat_of_entry" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="threat_of_entry" value="3">3 - Neutral</td>
                                <td><input type="radio" name="threat_of_entry" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="threat_of_entry" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Porters 2: Intensity of Rivalry</td>
                                <td><input type="radio" name="intensity_of_rivalry" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="intensity_of_rivalry" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="intensity_of_rivalry" value="3">3 - Neutral</td>
                                <td><input type="radio" name="intensity_of_rivalry" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="intensity_of_rivalry" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Porters 3: Substitution of Threat</td>
                                <td><input type="radio" name="substitution_of_threat" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="substitution_of_threat" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="substitution_of_threat" value="3">3 - Neutral</td>
                                <td><input type="radio" name="substitution_of_threat" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="substitution_of_threat" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Porters 4: Buyer Bargaining Power</td>
                                <td><input type="radio" name="buyer_bargaining_power" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="buyer_bargaining_power" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="buyer_bargaining_power" value="3">3 - Neutral</td>
                                <td><input type="radio" name="buyer_bargaining_power" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="buyer_bargaining_power" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Porters 5: Supplier Bargaining Power</td>
                                <td><input type="radio" name="supplier_bargaining_power" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="supplier_bargaining_power" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="supplier_bargaining_power" value="3">3 - Neutral</td>
                                <td><input type="radio" name="supplier_bargaining_power" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="supplier_bargaining_power" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Overall Industry Outlook</td>
                                <td><input type="radio" name="overall_industry_outlook" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="overall_industry_outlook" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="overall_industry_outlook" value="3">3 - Neutral</td>
                                <td><input type="radio" name="overall_industry_outlook" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="overall_industry_outlook" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Market Position</td>
                                <td><input type="radio" name="market_position" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="market_position" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="market_position" value="3">3 - Neutral</td>
                                <td><input type="radio" name="market_position" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="market_position" value="5">5 - Highly Satisfactory</td>
                            </tr>
                    </table>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="business-form.php?step=1" class="next-button btn"><i class="fa-solid fa-caret-left"></i> Back</a>
                        <a href="business-form.php?step=3"class="next-button btn">Next <i class="fa-solid fa-caret-right"></i></a>
                    </div>
                </form>
                <?php endif; ?>

                <?php if ($currentStep == 3): ?>
                <h3>Step 3 – Management Quality</h3>
                <form action="submit_business.php?step=4" method="POST">
                    <table class="table table-boredered text-center">
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
                                <td><input type="radio" name="character_of_management" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="character_of_management" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="character_of_management" value="3">3 - Neutral</td>
                                <td><input type="radio" name="character_of_management" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="character_of_management" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Quality and Experience of Management</td>
                                <td><input type="radio" name="quality_and_experience_of_management" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="quality_and_experience_of_management" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="quality_and_experience_of_management" value="3">3 - Neutral</td>
                                <td><input type="radio" name="quality_and_experience_of_management" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="quality_and_experience_of_management" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Bank Relationship</td>
                                <td><input type="radio" name="bank_relationship" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="bank_relationship" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="bank_relationship" value="3">3 - Neutral</td>
                                <td><input type="radio" name="bank_relationship" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="bank_relationship" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Labor Relations</td>
                                <td><input type="radio" name="labor_relations" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="labor_relations" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="labor_relations" value="3">3 - Neutral</td>
                                <td><input type="radio" name="labor_relations" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="labor_relations" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>existence</td>
                                <td><input type="radio" name="existence" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="existence" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="existence" value="3">3 - Neutral</td>
                                <td><input type="radio" name="existence" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="existence" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>NFIS/CMAP Checkings</td>
                                <td><input type="radio" name="nfis_cmap_checkings" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="nfis_cmap_checkings" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="nfis_cmap_checkings" value="3">3 - Neutral</td>
                                <td><input type="radio" name="nfis_cmap_checkings" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="nfis_cmap_checkings" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Management Control and Business Planning</td>
                                <td><input type="radio" name="management_cntrl_business_planning" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="management_cntrl_business_planning" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="management_cntrl_business_planning" value="3">3 - Neutral</td>
                                <td><input type="radio" name="management_cntrl_business_planning" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="management_cntrl_business_planning" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Management Structure and Successtion Strategy</td>
                                <td><input type="radio" name="management_structure_succession_strategy" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="management_structure_succession_strategy" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="management_structure_succession_strategy" value="3">3 - Neutral</td>
                                <td><input type="radio" name="management_structure_succession_strategy" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="management_structure_succession_strategy" value="5">5 - Highly Satisfactory</td>
                            </tr>
                            <tr>
                                <td>Clear Long-Term Management Strategy</td>
                                <td><input type="radio" name="long_term_management_strategy" value="1">1 - Highly Unsatisfactory</td>
                                <td><input type="radio" name="long_term_management_strategy" value="2">2 - Unsatisfactory</td>
                                <td><input type="radio" name="long_term_management_strategy" value="3">3 - Neutral</td>
                                <td><input type="radio" name="long_term_management_strategy" value="4">4 - Satisfactory</td>
                                <td><input type="radio" name="long_term_management_strategy" value="5">5 - Highly Satisfactory</td>
                            </tr>
                        </table>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="business-form.php?step=2" class="next-button btn"><i class="fa-solid fa-caret-left"></i> Back</a>
                            <a href="business-form.php?step=4"class="next-button btn">Next <i class="fa-solid fa-caret-right"></i></a>
                        </div>
                    </form>
                    <?php endif; ?>


                <?php if ($currentStep == 4): ?>
                <h3>Review</h3>
                <div class="d-flex justify-content-center gap-3">
                    <a href="business-form.php?step=3" class="next-button btn"><i class="fa-solid fa-caret-left"></i> Back</a>
                </div>
                <button type="submit" class="btn btn-primary btn">Submit</button>
                <?php endif; ?>

            </form>
        </div>

    </section>
    <script src="static/form.js" defer></script>
    <?php include "static/footer.php"?>
</body>
</html> 