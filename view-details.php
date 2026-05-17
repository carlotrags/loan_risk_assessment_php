<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

include 'static/config.php';

$app_id = $_GET['id'] ?? null;
$type = strtolower($_GET['type'] ?? ''); // Force lowercase for consistency

if (!$app_id) {
    die("Application ID missing.");
}

if ($type === 'business') {
    $table = "business_loan_applications";
    $id_column = "business_application_id";
    $is_business = true;
    $db_loan_type = 'Business'; // Matching DB Enum casing
} elseif ($type === 'home') {
    $table = "home_loan_applications";
    $id_column = "home_application_id";
    $is_business = false;
    $db_loan_type = 'Home'; // Matching DB Enum casing
} else {
    $table = "personal_loan_applications";
    $id_column = "application_id";
    $is_business = false;
    $db_loan_type = 'Personal'; // Matching DB Enum casing
}

try {
    // Fetch data using the specific table and ID column identified above
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $id_column = :id");
    $stmt->execute([':id' => $app_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Record not found.");
    }

    $histStmt = $pdo->prepare("SELECT history_id, name, officer_notes, manual_risk_adjustment, prediction, submitted_at, updated_at FROM loan_application_history WHERE application_id = :id AND loan_type = :type");
    $histStmt->execute([':id' => $app_id, ':type' => $db_loan_type]);
    $history = $histStmt->fetch(PDO::FETCH_ASSOC);

    if (!$history) {
        die("History record not found.");
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// --- FIELD GROUPINGS: Define which of the 23 variables to show ---
if ($type === 'business') {
    $profile_fields = ['company_name', 'email'];
    $financial_condition = ['capital_to_risk_assets_ratio', 'debt_to_equity_ratio', 'npl_ratio', 'npa_ratio', 'npa_coverage_ratio', 'roae', 'roaa', 'cost_to_income_ratio', 'liquid_assets_to_borrowed_funds', 'debt_service_cover'];
    // FIXED: Mapping initialized fields to allow the rendering loop to load data
    $industry_market_analysis = ['threat_of_entry', 'intensity_of_rivalry', 'substitution_of_threat', 'buyer_bargaining_power', 'supplier_bargaining_power', 'overall_industry_outlook', 'market_position'];
    $management_quality = ['character_of_management', 'quality_and_experience_of_management', 'bank_relationship', 'labor_relations', 'existence', 'nfis_cmap_checkings', 'management_cntrl_business_planning', 'management_structure_succession_strategy', 'long_term_management_strategy'];
    $collateral_info = [];
} elseif ($type === 'home') {
    // These are your Home Loan variables
    $profile_fields = ['tin_no', 'birthdate', 'address']; 
    $financial_condition = ['age', 'sex', 'civil_status', 'dependents', 'years_of_stay', 'home_ownership', 'employment_type', 'monthly_income', 'years_employed'];
    $management_quality = [];
    $collateral_info = ['collateral_type', 'property_value', 'existing_loans', 'monthly_debt', 'dti_ratio', 'default_history'];
} else {
    // Corrected to use the real columns found in your personal_loan_applications schema
    $profile_fields = ['email', 'contact_no', 'home_address', 'age'];
    $financial_condition = ['income', 'credit_score', 'dti_ratio'];
    $management_quality = [];
    $collateral_info = ['existing_loans', 'default_history', 'loan_intent'];
}

function formatLabel($key) {
    return ucwords(str_replace('_', ' ', $key));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/viewdetails.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Report: <?= htmlspecialchars($history['name'] ?? 'Details') ?></title>
</head>
<body>
<section class="header-navbar">
    <div class="no-print">
        <?php include "static/navbar.php" ?>
    </div>
</section>
<div class="container report-wrapper" id="view-details-page">
    <div class="row g-4 justify-content-center w-100">

        <div class="col-12 col-lg-7">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h4 class="mb-0 text-primary fw-bold">Loan Application Report</h4>
                    <span class="badge bg-secondary rounded-pill"><?= strtoupper($type) ?></span>
                </div>

                <div class="report-section">
                    <p class="text-uppercase small fw-bold text-primary mb-3"><?= $is_business ? 'Company Profile' : 'Client Profile' ?></p>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="label-text">Name / Entity</div>
                            <div class="value-text fs-5 fw-semibold text-dark"><?= htmlspecialchars($history['name']) ?></div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($profile_fields as $f): if (isset($data[$f])): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="label-text"><?= formatLabel($f) ?></div>
                            <div class="value-text"><?= htmlspecialchars($data[$f]) ?></div>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>

                <?php if ($is_business): ?>
                <div class="report-section">
                    <p class="text-uppercase small fw-bold text-primary mb-3">Financial Condition</p>
                    <div class="row g-3">
                        <?php foreach ($financial_condition as $f): if (isset($data[$f])): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="label-text"><?= formatLabel($f) ?></div>
                            <div class="value-text">
                                <?php 
                                    // Displays the raw numerical rating score clearly (e.g., Score: 4/5)
                                    echo htmlspecialchars($data[$f]) . " / 5"; 
                                ?>
                            </div>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($collateral_info)): ?>
                <div class="report-section">
                    <p class="text-uppercase small fw-bold text-primary mb-3">Collateral & Risk Details</p>
                    <div class="row g-3">
                        <?php foreach ($collateral_info as $f): if (isset($data[$f])): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="label-text"><?= ucwords(str_replace('_', ' ', $f)) ?></div>
                            <div class="value-text">
                                <?php 
                                    if ($f === 'property_value' || $f === 'monthly_debt') echo '₱' . number_format($data[$f], 2);
                                    elseif (in_array($data[$f], [0, 1]) && ($f === 'existing_loans' || $f === 'default_history')) echo ($data[$f] == 1 ? 'Yes' : 'No');
                                    else echo htmlspecialchars($data[$f]); 
                                ?>
                            </div>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($industry_market_analysis)): ?>
                <div class="report-section">
                    <p class="text-uppercase small fw-bold text-primary mb-3">Industry/Market Analysis</p>
                    <div class="row g-3">
                        <?php foreach ($industry_market_analysis as $f): if (isset($data[$f])): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="label-text"><?= formatLabel($f) ?></div>
                            <div class="value-text"><?= htmlspecialchars($data[$f]) ?></div>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($management_quality)): ?>
                <div class="report-section">
                    <p class="text-uppercase small fw-bold text-primary mb-3">Management Quality</p>
                    <div class="row g-3">
                        <?php foreach ($management_quality as $f): if (isset($data[$f])): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="label-text"><?= formatLabel($f) ?></div>
                            <div class="value-text"><?= htmlspecialchars($data[$f]) ?></div>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="report-section border-0">
                    <p class="text-uppercase small fw-bold text-primary mb-3">Loan Specifics</p>
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="label-text">Requested Amount</div>
                            <div class="value-text fw-bold">₱<?= number_format($data['loan_amount'] ?? 0,2) ?></div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="label-text">Term</div>
                            <div class="value-text"><?= htmlspecialchars($data['loan_term'] ?? '0') ?> Months</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="label-text">Application Date</div>
                            <div class="value-text"><?= date("M d, Y", strtotime($history['submitted_at'])) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card p-4 h-100">
                <h6 class="text-center text-muted mb-4">Risk Assessment Summary</h6>

                <?php 
                $finalRisk = ($history['manual_risk_adjustment'] !== null)
                    ? $history['manual_risk_adjustment']
                    : ($history['prediction'] ?? 0);

                $isLow = ($finalRisk == 0); 
                ?>

                <div class="risk-box <?= $isLow ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' ?> mb-2">
                    <h3 class="fw-bold mb-0"><?= $isLow ? 'LOW RISK' : 'HIGH RISK' ?></h3>
                    <p class="small mb-0 opacity-75">
                        <?= ($history['manual_risk_adjustment'] !== null) ? 'Verified by Officer' : 'AI Model Analysis' ?>
                    </p>
                </div>

                <div class="text-center mb-3">
                    <?php if (!empty($history['updated_at'])): ?>
                        <span class="edit-timer text-center" data-timestamp="<?= strtotime($history['updated_at']) ?>"></span>
                    <?php endif; ?>
                </div>

                <div class="d-grid mb-4 no-print">
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#updateModal">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Update Assessment
                    </button>
                </div>

                <div class="mb-4">
                    <label class="label-text mb-1">Officer Decision Notes</label>
                    <div class="p-3 bg-light rounded small" style="min-height:100px;">
                        <?= !empty($history['officer_notes']) 
                            ? nl2br(htmlspecialchars($history['officer_notes'])) 
                            : '<em>No additional remarks recorded.</em>' ?>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-auto no-print">
                    <?php
                    $recipientEmail = $data['email'] ?? '';

                    $subject = "Loan Assessment Result - " . ($history['name'] ?? '');

                    $body = "Hi,\n\nPlease download the generated PDF from your system and attach it before sending.\n\nThank you.";

                    $gmailLink = "https://mail.google.com/mail/?view=cm&fs=1"
                        . "&to=" . urlencode($recipientEmail)
                        . "&su=" . urlencode($subject)
                        . "&body=" . urlencode($body);
                    ?>

                    <a href="<?= $gmailLink ?>"
                    target="_blank"
                    class="btn btn-success">
                    Send Email
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fa-solid fa-print me-2"></i>Print Report
                    </button>
                    <a href="<?= strtolower($type) ?>-history.php" class="btn btn-dark">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to History
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="updateDetailsForm"class="modal-content border-0 shadow-lg" action="actions/update_loan.php" method="POST">
            <input type="hidden" name="redirect_to" value="view-details.php?id=<?= $app_id ?>&type=<?= $type ?>">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Update Assessment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="history_id" value="<?= $history['history_id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Risk Level Adjustment</label>
                    <select name="manual_risk_adjustment" class="form-select">
                        <option value="0" <?= ($finalRisk == 0) ? 'selected' : '' ?>>Low Risk</option>
                        <option value="1" <?= ($finalRisk == 1) ? 'selected' : '' ?>>High Risk</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold small text-uppercase text-muted">Remarks / Justification</label>
                    <textarea name="officer_notes" class="form-control" rows="4" placeholder="Enter notes here..."><?= htmlspecialchars($history['officer_notes'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Apply Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include "static/footer.php" ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="static/history-actions.js?v=<?= time() ?>" defer></script>

</body>
</html>