<?php
if (!isset($_SESSION)) session_start();

$form = $_SESSION['business_form'] ?? [];
?>

<section class="preview-container">
    <h2>Client Details Preview</h2>

    <!-- A. Client Details -->
    <table class="preview-table">
        <tr class="section-header">
            <td colspan="3">A. Client Details</td>
        </tr>
        <tr class="variable-row">
            <td>Company Name</td>
            <td>Loan Amount</td>
            <td>Loan Term</td>
        </tr>
        <tr class="answer-row">
            <td><?= $form['company_name'] ?? '-' ?></td>
            <td><?= $form['loan_amount'] ?? '-' ?></td>
            <td><?= $form['loan_term'] ?? '-' ?></td>
        </tr>
    </table>

    <br><br>

    <!-- B. Financial Condition -->
    <table class="preview-table">
        <tr class="section-header">
            <td colspan="10">B. Financial Condition</td>
        </tr>
        <tr class="variable-row">
            <td>Capital to Risk Assets Ratio</td>
            <td>Debt-to-Equity Ratio</td>
            <td>NPL Ratio</td>
            <td>NPA Ratio</td>
            <td>NPA Coverage Ratio</td>
            <td>ROAE</td>
            <td>ROAA</td>
            <td>Cost to Income Ratio</td>
            <td>Liquid Assets to Borrowed Funds</td>
            <td>Debt Service Cover (X)</td>
        </tr>
        <tr class="answer-row">
            <td><?= $form['capital_to_risk_assets_ratio'] ?? '-' ?></td>
            <td><?= $form['debt_to_equity_ratio'] ?? '-' ?></td>
            <td><?= $form['npl_ratio'] ?? '-' ?></td>
            <td><?= $form['npa_ratio'] ?? '-' ?></td>
            <td><?= $form['npa_coverage_ratio'] ?? '-' ?></td>
            <td><?= $form['roae'] ?? '-' ?></td>
            <td><?= $form['roaa'] ?? '-' ?></td>
            <td><?= $form['cost_to_income_ratio'] ?? '-' ?></td>
            <td><?= $form['liquid_assets_to_borrowed_funds'] ?? '-' ?></td>
            <td><?= $form['debt_service_cover'] ?? '-' ?></td>
        </tr>    
    </table>

    <br><br>

    <!-- C. Industry/Market Analysis -->
    <table class="preview-table">
        <tr class="section-header">
            <td colspan="7">C. Industry/Market Analysis</td>
        </tr>
        <tr class="variable-row">
            <td>Threat of Entry</td>
            <td>Intensity of Rivalry</td>
            <td>Substitution of Threat</td>
            <td>Buyer Bargaining Power</td>
            <td>Supplier Bargaining Power</td>
            <td>Overall Industry Outlook</td>
            <td>Market Position</td>
        </tr>
        <tr class="answer-row">
            <td><?= $form['threat_of_entry'] ?? '-' ?></td>
            <td><?= $form['intensity_of_rivalry'] ?? '-' ?></td>
            <td><?= $form['substitution_of_threat'] ?? '-' ?></td>
            <td><?= $form['buyer_bargaining_power'] ?? '-' ?></td>
            <td><?= $form['supplier_bargaining_power'] ?? '-' ?></td>
            <td><?= $form['overall_industry_outlook'] ?? '-' ?></td>
            <td><?= $form['market_position'] ?? '-' ?></td>
        </tr>
    </table>

    <br><br>

    <!-- D. Management Quality -->
    <table class="preview-table">
        <tr class="section-header">
            <td colspan="9">D. Management Quality</td>
        </tr>
        <tr class="variable-row">
            <td>Character of Management</td>
            <td>Quality & Experience</td>
            <td>Bank Relationship</td>
            <td>Labor Relations</td>
            <td>Existence</td>
            <td>NFIS/CMAP Checkings</td>
            <td>Management Control and Business Planning</td>
            <td>Management Structrure and Succession Strategy</td>
            <td>Clear Long-Term Management Strategy</td>
        </tr>
        <tr class="answer-row">
            <td><?= $form['character_of_management'] ?? '-' ?></td>
            <td><?= $form['quality_and_experience_of_management'] ?? '-' ?></td>
            <td><?= $form['bank_relationship'] ?? '-' ?></td>
            <td><?= $form['labor_relations'] ?? '-' ?></td>
            <td><?= $form['existence'] ?? '-' ?></td>
            <td><?= $form['nfis_cmap_checkings'] ?? '-' ?></td>
            <td><?= $form['management_cntrl_business_planning'] ?? '-' ?></td>
            <td><?= $form['management_structure_succession_strategy'] ?? '-' ?></td>
            <td><?= $form['long_term_management_strategy'] ?? '-' ?></td>
        </tr>
    </table>
</section>
