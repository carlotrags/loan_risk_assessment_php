<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an assessment.");
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';
$first_name = $_SESSION['first_name'] ?? '';

// Load previous form data from session
$form = $_SESSION['business_form'] ?? [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect POST data
    // FIXED THIS CAUSING NOT REFLECTING THE COMPANY NAME Check POST first, then fallback to the Session 'business_form' data
    $company_name = $_POST['company_name'] ?? ($_SESSION['business_form']['company_name'] ?? 'Unknown Company');
    $loan_type = 'business';

// Define all expected keys for the API call
    $expected_keys = [
        'company_name', 'loan_amount', 'loan_term',
        'capital_to_risk_assets_ratio', 'debt_to_equity_ratio', 'npl_ratio',
        'npa_ratio', 'npa_coverage_ratio', 'roae', 'roaa',
        'cost_to_income_ratio', 'liquid_assets_to_borrowed_funds', 'debt_service_cover',
        'threat_of_entry', 'intensity_of_rivalry', 'substitution_of_threat',
        'buyer_bargaining_power', 'supplier_bargaining_power', 'overall_industry_outlook',
        'market_position', 'character_of_management', 'quality_and_experience_of_management',
        'bank_relationship', 'labor_relations', 'existence',
        'nfis_cmap_checkings', 'management_cntrl_business_planning', 'management_structure_succession_strategy',
        'long_term_management_strategy'
    ];

    // Cast numeric fields
    $data = [
        'loan_amount' => (float)($_POST['loan_amount'] ?? 0),
        'loan_term' => (float)($_POST['loan_term'] ?? 0),
        // All enums cast to int
        'capital_to_risk_assets_ratio' => (int)($_POST['capital_to_risk_assets_ratio'] ?? 0),
        'debt_to_equity_ratio' => (int)($_POST['debt_to_equity_ratio'] ?? 0),
        'npl_ratio' => (int)($_POST['npl_ratio'] ?? 0),
        'npa_ratio' => (int)($_POST['npa_ratio'] ?? 0),
        'npa_coverage_ratio' => (int)($_POST['npa_coverage_ratio'] ?? 0),
        'roae' => (int)($_POST['roae'] ?? 0),
        'roaa' => (int)($_POST['roaa'] ?? 0),
        'cost_to_income_ratio' => (int)($_POST['cost_to_income_ratio'] ?? 0),
        'liquid_assets_to_borrowed_funds' => (int)($_POST['liquid_assets_to_borrowed_funds'] ?? 0),
        'debt_service_cover' => (int)($_POST['debt_service_cover'] ?? 0),
        'threat_of_entry' => (int)($_POST['threat_of_entry'] ?? 0),
        'intensity_of_rivalry' => (int)($_POST['intensity_of_rivalry'] ?? 0),
        'substitution_of_threat' => (int)($_POST['substitution_of_threat'] ?? 0),
        'buyer_bargaining_power' => (int)($_POST['buyer_bargaining_power'] ?? 0),
        'supplier_bargaining_power' => (int)($_POST['supplier_bargaining_power'] ?? 0),
        'overall_industry_outlook' => (int)($_POST['overall_industry_outlook'] ?? 0),
        'market_position' => (int)($_POST['market_position'] ?? 0),
        'character_of_management' => (int)($_POST['character_of_management'] ?? 0),
        'quality_and_experience_of_management' => (int)($_POST['quality_and_experience_of_management'] ?? 0),
        'bank_relationship' => (int)($_POST['bank_relationship'] ?? 0),
        'labor_relations' => (int)($_POST['labor_relations'] ?? 0),
        'existence' => (int)($_POST['existence'] ?? 0),
        'nfis_cmap_checkings' => (int)($_POST['nfis_cmap_checkings'] ?? 0),
        'management_cntrl_business_planning' => (int)($_POST['management_cntrl_business_planning'] ?? 0),
        'management_structure_succession_strategy' => (int)($_POST['management_structure_succession_strategy'] ?? 0),
        'long_term_management_strategy' => (int)($_POST['long_term_management_strategy'] ?? 0)
    ];
    foreach ($expected_keys as $key) {
        // Handle the two float fields
        if ($key === 'loan_amount' || $key === 'loan_term') {
            $data[$key] = (float)($form[$key] ?? 0);
        }
        // Handle all other integer (enum) fields
        else {
            $data[$key] = (int)($form[$key] ?? 0);
        }
    }
    // Save to session for preview
    $_SESSION['business_form'] = array_merge($form, $data, ['company_name' => $company_name]);

    // Call Flask API
    $api_url = 'http://127.0.0.1:5000/predict/business';
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    $response = curl_exec($ch);
    if ($response === false) {
        die("Error calling prediction API: " . curl_error($ch));
    }
    curl_close($ch);

    $result = json_decode($response, true);
    if (!isset($result['prediction'])) {
        die("API did not return a prediction.");
    }

    $prediction = (int)$result['prediction']; // make sure it's int
    $explanation = $result['explanation'] ?? [];
    $message = $prediction === 1 ? "Loan Approved" : "Loan Denied";
    $statusClass = $prediction === 1 ? "approved" : "denied";

    // Save to MySQL
    $conn = new mysqli("127.0.0.1", "root", "", "loan_system", 3307);
    if ($conn->connect_error) die("MySQL Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("INSERT INTO business_loan_applications
        (company_name, loan_amount, loan_term, capital_to_risk_assets_ratio, debt_to_equity_ratio, npl_ratio, npa_ratio, npa_coverage_ratio, roae, roaa, cost_to_income_ratio, liquid_assets_to_borrowed_funds, debt_service_cover, threat_of_entry, intensity_of_rivalry, substitution_of_threat, buyer_bargaining_power, supplier_bargaining_power, overall_industry_outlook, market_position, character_of_management, quality_and_experience_of_management, bank_relationship, labor_relations, existence, nfis_cmap_checkings, management_cntrl_business_planning, management_structure_succession_strategy, long_term_management_strategy, prediction, submitted_at, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");

    $stmt->bind_param(
        "sddiiiiiiiiiiiiiiiiiiiiiiiiiiii",
        $company_name,
        $data['loan_amount'],
        $data['loan_term'],
        $data['capital_to_risk_assets_ratio'],
        $data['debt_to_equity_ratio'],
        $data['npl_ratio'],
        $data['npa_ratio'],
        $data['npa_coverage_ratio'],
        $data['roae'],
        $data['roaa'],
        $data['cost_to_income_ratio'],
        $data['liquid_assets_to_borrowed_funds'],
        $data['debt_service_cover'],
        $data['threat_of_entry'],
        $data['intensity_of_rivalry'],
        $data['substitution_of_threat'],
        $data['buyer_bargaining_power'],
        $data['supplier_bargaining_power'],
        $data['overall_industry_outlook'],
        $data['market_position'],
        $data['character_of_management'],
        $data['quality_and_experience_of_management'],
        $data['bank_relationship'],
        $data['labor_relations'],
        $data['existence'],
        $data['nfis_cmap_checkings'],
        $data['management_cntrl_business_planning'],
        $data['management_structure_succession_strategy'],
        $data['long_term_management_strategy'],
        $prediction,
        $user_id
    );

    if (!$stmt->execute()) {
        die("DB Insert failed: " . $stmt->error);
    }

    $application_id = $conn->insert_id;
    $stmt->close();

    // Save general history
    $stmt2 = $conn->prepare("INSERT INTO loan_application_history 
        (user_id, application_id, name, loan_amount, loan_term, loan_type, submitted_at, prediction) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
    $stmt2->bind_param(
        "iisdisi",
        $user_id,
        $application_id,
        $company_name,
        $data['loan_amount'],
        $data['loan_term'],
        $loan_type,
        $prediction
    );
    $stmt2->execute();
    $stmt2->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
<link rel="stylesheet" href="static/css/navbarstyle.css">
<link rel="stylesheet" href="static/css/result.css?v=<?= time() ?>">
<link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Business Loan Assessment Result</title>
<style>
    .d-flex {
    display: flex !important;
    justify-content: flex-end;
}
</style>
</head>
<body>
<section class="header-navbar">
    <?php include "static/navbar.php"; ?>
</section>

<div class="result-container business">
    <div class="result-card business">
        <div class="card-header">
            <h2>Business Loan Assessment Result</h2>
            <p class="applicant-info">Company: <?= htmlspecialchars($company_name ?? '-') ?></p>
        </div>

        <div class="assessment-status <?= htmlspecialchars($statusClass ?? 'info') ?>">
            <?php if (isset($prediction)): ?>
                <i class="icon"><?= $prediction == 1 ? '&#10003;' : '&#10007;' ?></i>
            <?php endif; ?>
            <div class="status-message">
                <h3><?= htmlspecialchars($message ?? 'No result available') ?></h3>
            </div>
        </div>

        <!-- Display Business Application Details like Preview -->
        <section class="preview-container business">
            <h4>Application Details Submitted</h4>

            <!-- A. Client Details -->
            <table class="preview-table">
                <tr class="section-header"><td colspan="3">A. Client Details</td></tr>
                <tr class="variable-row">
                    <td>Company Name</td>
                    <td>Loan Amount</td>
                    <td>Loan Term</td>
                </tr>
                <tr class="answer-row">
                    <td><?= htmlspecialchars($company_name ?? '-') ?></td>
                    <td>₱<?= number_format($data['loan_amount'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($data['loan_term'] ?? '-') ?> months</td>
                </tr>
            </table>

            <!-- B. Financial Condition -->
            <table class="preview-table">
                <tr class="section-header"><td colspan="10">B. Financial Condition</td></tr>
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
                    <td><?= htmlspecialchars($data['capital_to_risk_assets_ratio'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['debt_to_equity_ratio'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['npl_ratio'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['npa_ratio'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['npa_coverage_ratio'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['roae'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['roaa'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['cost_to_income_ratio'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['liquid_assets_to_borrowed_funds'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['debt_service_cover'] ?? '-') ?></td>
                </tr>
            </table>

            <!-- C. Industry/Market Analysis -->
            <table class="preview-table">
                <tr class="section-header"><td colspan="7">C. Industry/Market Analysis</td></tr>
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
                    <td><?= htmlspecialchars($data['threat_of_entry'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['intensity_of_rivalry'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['substitution_of_threat'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['buyer_bargaining_power'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['supplier_bargaining_power'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['overall_industry_outlook'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['market_position'] ?? '-') ?></td>
                </tr>
            </table>

            <!-- D. Management Quality -->
            <table class="preview-table">
                <tr class="section-header"><td colspan="9">D. Management Quality</td></tr>
                <tr class="variable-row">
                    <td>Character of Management</td>
                    <td>Quality & Experience</td>
                    <td>Bank Relationship</td>
                    <td>Labor Relations</td>
                    <td>Existence</td>
                    <td>NFIS/CMAP Checkings</td>
                    <td>Management Control & Business Planning</td>
                    <td>Management Structure & Succession Strategy</td>
                    <td>Clear Long-Term Management Strategy</td>
                </tr>
                <tr class="answer-row">
                    <td><?= htmlspecialchars($data['character_of_management'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['quality_and_experience_of_management'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['bank_relationship'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['labor_relations'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['existence'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['nfis_cmap_checkings'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['management_cntrl_business_planning'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['management_structure_succession_strategy'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($data['long_term_management_strategy'] ?? '-') ?></td>
                </tr>
            </table>

        </section>

        <?php if (!empty($explanation) && isset($prediction) && $prediction == 0): ?>
            <div class="section denial-reasons">
                <h4>Reason(s) for Denial</h4>
                <ul class="reason-list">
                    <?php foreach ($explanation as $reason): ?>
                        <li><i class="icon-reason">&#x25CF;</i> <?= htmlspecialchars($reason) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="action-buttons d-flex gap-3">
            <a href="<?php echo 'view-details.php?id=' . urlencode($application_id) . '&type=' . urlencode($loan_type); ?>" class="btn btn-success">
                Edit / Print Assessment
            </a>
            <a href="business-form.php" class="btn btn-primary">Submit New Assessment</a>
        </div>

        <div class="footer-result">
            <p>Results are preliminary and subject to final verification.</p>
        </div>
    </div>
</div>

<?php include "static/footer.php"; ?>
</body>
</html>
