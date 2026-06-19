<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

// config.php handles Asia/Manila timezone for both PHP and PDO
include 'static/config.php';

$app_id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? '';

if (!$app_id) {
    die("Application ID missing.");
}

$is_business = (strtolower($type) === 'business');
$table = $is_business ? "business_loan_applications" : "personal_loan_applications";

try {
    // Dynamic Primary Key detection
    $pkQuery = $pdo->query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
    $pkData = $pkQuery->fetch(PDO::FETCH_ASSOC);
    $id_column = $pkData['Column_name'] ?? 'id'; 

    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $id_column = :id");
    $stmt->execute([':id' => $app_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Record not found.");
    }

    $histStmt = $pdo->prepare("SELECT history_id, name, officer_notes, manual_risk_adjustment, prediction, submitted_at, updated_at FROM loan_application_history WHERE application_id = :id AND loan_type = :type");
    $histStmt->execute([':id' => $app_id, ':type' => $type]);
    $history = $histStmt->fetch(PDO::FETCH_ASSOC);

    if (!$history) {
        die("History record not found.");
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Define fields based on schema
if ($is_business) {
    $profile_fields = ['company_name', 'existence'];
    $financial_fields = [
        'capital_to_risk_assets_ratio', 'debt_to_equity_ratio', 'npl_ratio', 
        'roae', 'roaa', 'cost_to_income_ratio', 'liquid_assets_to_borrowed_funds'
    ];
    $management_fields = [
        'character_of_management', 'quality_and_experience_of_management', 
        'bank_relationship', 'labor_relations', 'long_term_management_strategy'
    ];
} else {
    $profile_fields = ['email', 'phone', 'gender', 'married', 'dependents', 'education', 'self_employed'];
    $financial_fields = ['applicant_income', 'coapplicant_income', 'total_income', 'credit_history', 'property_area'];
    $management_fields = [];
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

                <div class="report-section">
                    <p class="text-uppercase small fw-bold text-primary mb-3">Financial Indicators</p>
                    <div class="row g-3">
                        <?php foreach ($financial_fields as $f): if (isset($data[$f])): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="label-text"><?= formatLabel($f) ?></div>
                            <div class="value-text">
                                <?php 
                                if (is_numeric($data[$f]) && strpos($f, 'income') !== false) {
                                    echo '₱' . number_format($data[$f], 2);
                                } else {
                                    echo htmlspecialchars($data[$f]);
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($management_fields)): ?>
                <div class="report-section">
                    <p class="text-uppercase small fw-bold text-primary mb-3">Management Ratings (1-5 Scale)</p>
                    <div class="row g-3">
                        <?php foreach ($management_fields as $f): if (isset($data[$f])): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="label-text"><?= formatLabel($f) ?></div>
                            <div class="value-text"><?= htmlspecialchars($data[$f]) ?> / 5</div>
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
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fa-solid fa-print me-2"></i>Print Report
                    </button>
                    <a href="history.php" class="btn btn-dark">
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