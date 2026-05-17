<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

include 'static/config.php';

// Fetch all users for the filter dropdown
$userStmt = $pdo->query("SELECT DISTINCT CONCAT(first_name,' ',last_name) AS full_name FROM user_accounts ORDER BY full_name ASC");
$allUsers = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$conditions = [];
$params = [];

// Filtering Logic
if (!empty($_GET['searchName'])) {
    $conditions[] = 'la.company_name LIKE :name';
    $params[':name'] = '%' . $_GET['searchName'] . '%';
}

if (!empty($_GET['filterAssessmentBy'])) {
    $conditions[] = 'CONCAT(ba.first_name, " ", ba.last_name) = :assessedBy';
    $params[':assessedBy'] = $_GET['filterAssessmentBy'];
}

if (isset($_GET['filterPrediction']) && $_GET['filterPrediction'] !== '') {
    // Check either the manual adjustment or the original prediction
    $conditions[] = 'COALESCE(h.manual_risk_adjustment, la.prediction) = :prediction';
    $params[':prediction'] = $_GET['filterPrediction'];
}

if (!empty($_GET['dateFrom'])) {
    $conditions[] = 'DATE(la.submitted_at) >= :dateFrom';
    $params[':dateFrom'] = $_GET['dateFrom'];
}

if (!empty($_GET['dateTo'])) {
    $conditions[] = 'DATE(la.submitted_at) <= :dateTo';
    $params[':dateTo'] = $_GET['dateTo'];
}

$where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

// Professional JOIN Query: Ratios from Business table + Decision data from History table
$sql = "SELECT 
        la.business_application_id, 
        la.company_name,
        la.email,
        la.loan_amount, 
        la.prediction, 
        la.loan_term,
        la.loan_type, 
        la.submitted_at,
        -- Technical Ratios
        la.capital_to_risk_assets_ratio,
        la.debt_to_equity_ratio,
        la.npl_ratio,
        la.npa_ratio,
        la.npa_coverage_ratio,
        la.roae,
        la.roaa,
        la.cost_to_income_ratio,
        la.liquid_assets_to_borrowed_funds,
        la.debt_service_cover,
        la.threat_of_entry,
        la.intensity_of_rivalry,
        la.substitution_of_threat,
        la.buyer_bargaining_power,
        la.supplier_bargaining_power,
        la.overall_industry_outlook,
        la.market_position,
        la.character_of_management,
        la.quality_and_experience_of_management,
        la.bank_relationship,
        la.labor_relations,
        la.existence,
        la.nfis_cmap_checkings,
        la.management_cntrl_business_planning,
        la.management_structure_succession_strategy,
        la.long_term_management_strategy,
        -- History and Assessment Data
        h.history_id,
        h.manual_risk_adjustment,
        h.officer_notes,          
        h.updated_at, 
        ba.first_name, 
        ba.last_name, 
        ba.role
    FROM business_loan_applications AS la
    LEFT JOIN loan_application_history AS h 
        ON la.business_application_id = h.application_id 
        AND h.loan_type = 'business'
    INNER JOIN user_accounts AS ba ON la.user_id = ba.user_id" . $where . " 
    ORDER BY la.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/historystyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>History - Business Loans</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="container-fluid px-4">
            <div class="history-container w-auto">
                <h2>Business Loan Records</h2>

                <form method="GET">
                    <div class="filter-bar">
                        <div class="filter-grid">
                            <input type="text" name="searchName" class="form-control" placeholder="Search name" value="<?= htmlspecialchars($_GET['searchName'] ?? '') ?>">
                            
                            <select name="filterAssessmentBy" class="form-select">
                                <option value="">Assessment By (All)</option>
                                <?php foreach ($allUsers as $user): ?>
                                    <option value="<?= $user ?>" <?= (($_GET['filterAssessmentBy'] ?? '') === $user) ? 'selected' : '' ?>><?= $user ?></option>
                                <?php endforeach; ?>
                            </select>

                            <select name="filterPrediction" class="form-select">
                                <option value="">Prediction (All)</option>
                                <option value="0" <?= (isset($_GET['filterPrediction']) && $_GET['filterPrediction']=='0')?'selected':'' ?>>Low Risk</option>
                                <option value="1" <?= (isset($_GET['filterPrediction']) && $_GET['filterPrediction']=='1')?'selected':'' ?>>High Risk</option>
                            </select>

                            <input type="date" name="dateFrom" class="form-control" value="<?= htmlspecialchars($_GET['dateFrom'] ?? '') ?>">
                            <input type="date" name="dateTo" class="form-control" value="<?= htmlspecialchars($_GET['dateTo'] ?? '') ?>">
                                
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <?php $queryString = http_build_query($_GET); ?>
                            <a href="generatepdf.php?<?= htmlspecialchars($queryString) ?>&loan_type=business" class="btn btn-success btn-pdf-narrow">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                            </a>
                            <a href="personal-history.php" class="btn btn-primary">
                                <i class="fa-solid fa-user" style="padding-right: 10px;"></i>Personal Loans
                            </a>
                            <a href="home-history.php" class="btn btn-primary">
                                <i class="fa-solid fa-briefcase" style="padding-right: 10px;"></i>Home Loans
                            </a>
                        </div>
                    </div>
                </form>

                <div style="margin-bottom:10px;">
                    <button type="submit" form="bulkForm" id="deleteBtn" class="btn btn-secondary" disabled onclick="return confirm('Delete selected records?')">
                        <i class="fa-solid fa-trash"></i> Delete Selected
                    </button>
                </div>

                <form method="POST" action="actions/bulk_delete.php" id="bulkForm">
                    <?php if (count($rows) > 0): ?>
                        <table>
                            <thead>
                                <tr class="main-details">
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Company Name/Email</th>
                                    <th>Loan Amount</th>
                                    <th>Loan Term</th>
                                    <th>Prediction</th>
                                    <th>Submitted At</th>
                                    <th>Assessment By</th>
                                    <th>More Details</th>
                                    <th>Action</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" class="rowCheckbox" name="selected_ids[]" value="<?= $row['business_application_id'] ?>"></td>
                                        <td><?= htmlspecialchars($row['company_name']) ?><br><p style="font-size: 12px; color: #838995;"><?= htmlspecialchars($row['email']) ?></p></td>
                                        <td><?= number_format($row['loan_amount'], 2) ?></td>
                                        <td><?= htmlspecialchars($row['loan_term']) ?> mos</td>
                                        
                                        <td class="prediction <?= (($row['manual_risk_adjustment'] ?? $row['prediction']) == 0) ? 'low' : 'high' ?>">
                                            <span class="risk-label">
                                                <?= ($row['manual_risk_adjustment'] !== null) ? 
                                                    (($row['manual_risk_adjustment'] == 0) ? 'Low Risk' : 'High Risk') : 
                                                    (($row['prediction'] == 0) ? 'Low Risk' : 'High Risk') 
                                                ?>
                                            </span>
                                            <?php if (!empty($row['updated_at'])): ?>
                                                <span class="edit-timer" data-timestamp="<?= strtotime($row['updated_at']) ?>"></span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                                        
                                        <td>
                                            <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?> 
                                            <span class="badge align-items-center p-1 pe-2 ms-2 <?= $row['role'] === 'Manager' ? 'text-danger-emphasis bg-danger-subtle border border-danger-subtle' : 'text-primary-emphasis bg-primary-subtle border border-primary-subtle' ?> rounded-pill">
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['first_name'] . '+' . $row['last_name']) ?>&size=16" class="rounded-circle me-1" width="24" height="24" alt="profile">
                                                <?= htmlspecialchars($row['role']) ?>
                                            </span>
                                        </td>

                                        <td class="more-details">
                                            <button class="toggle-more-details" type="button">
                                                <i class="fa-solid fa-sort-down"></i>
                                            </button>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-3">
                                            <a href="view-details.php?id=<?= $row['business_application_id'] ?>&type=business" class="text-info"><i class="fa-solid fa-eye"></i></a>
                                            <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['business_application_id'] ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="actions/delete_loan.php?id=<?= $row['history_id'] ?>" class="text-danger" onclick="return confirm('Are you sure you want to delete this business loan record?')" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <tr class="hidden-details">
                                        <td colspan="9">
                                            <div class="hidden-variables-container">
                                                <div class="hidden-item">
                                                    <h4>Financial Condition</h4>
                                                    <p><strong>Capital to Risk Assets Ratio:</strong> <?= htmlspecialchars($row['capital_to_risk_assets_ratio']) ?></p>
                                                    <p><strong>Debt to Equity Ratio:</strong> <?= htmlspecialchars($row['debt_to_equity_ratio']) ?></p>
                                                    <p><strong>NPL Ratio:</strong> <?= htmlspecialchars($row['npl_ratio']) ?></p>
                                                    <p><strong>NPA Ratio:</strong> <?= htmlspecialchars($row['npa_ratio']) ?></p>
                                                    <p><strong>NPA Coverage Ratio:</strong> <?= htmlspecialchars($row['npa_coverage_ratio']) ?></p>
                                                    <p><strong>ROAE:</strong> <?= htmlspecialchars($row['roae']) ?></p>
                                                    <p><strong>ROAA:</strong> <?= htmlspecialchars($row['roaa']) ?></p>
                                                    <p><strong>Cost to Income Ratio:</strong> <?= htmlspecialchars($row['cost_to_income_ratio']) ?></p>
                                                    <p><strong>Liquid Assets to Borrowed Funds:</strong> <?= htmlspecialchars($row['liquid_assets_to_borrowed_funds']) ?></p>
                                                    <p><strong>Debt Service Cover:</strong> <?= htmlspecialchars($row['debt_service_cover']) ?></p>
                                                </div>
                                                <div class="hidden-item">
                                                    <h4>Industry/Market Analysis</h4>
                                                    <p><strong>Threat of Entry:</strong> <?= htmlspecialchars($row['threat_of_entry']) ?></p>
                                                    <p><strong>Intensity of Rivalry:</strong> <?= htmlspecialchars($row['intensity_of_rivalry']) ?></p>
                                                    <p><strong>Substitution of Threat:</strong> <?= htmlspecialchars($row['substitution_of_threat']) ?></p>
                                                    <p><strong>Buyer Bargaining Power:</strong> <?= htmlspecialchars($row['buyer_bargaining_power']) ?></p>
                                                    <p><strong>Supplier Bargaining Power:</strong> <?= htmlspecialchars($row['supplier_bargaining_power']) ?></p>
                                                    <p><strong>Overall Industry Outlook:</strong> <?= htmlspecialchars($row['overall_industry_outlook']) ?></p>
                                                    <p><strong>Market Position:</strong> <?= htmlspecialchars($row['market_position']) ?></p>
                                                </div>
                                                <div class="hidden-item">
                                                    <h4>Management Quality</h4>
                                                    <p><strong>Character of Management:</strong> <?= htmlspecialchars($row['character_of_management']) ?></p>
                                                    <p><strong>Quality and Experience of Management:</strong> <?= htmlspecialchars($row['quality_and_experience_of_management']) ?></p>
                                                    <p><strong>Bank Relationship:</strong> <?= htmlspecialchars($row['bank_relationship']) ?></p>
                                                    <p><strong>Labor Relations:</strong> <?= htmlspecialchars($row['labor_relations']) ?></p>
                                                    <p><strong>Existence:</strong> <?= htmlspecialchars($row['existence']) ?></p>
                                                    <p><strong>NFIS/CMAP Checkings:</strong> <?= htmlspecialchars($row['nfis_cmap_checkings']) ?></p>
                                                    <p><strong>Management Control and Planning:</strong> <?= htmlspecialchars($row['management_cntrl_business_planning']) ?></p>
                                                    <p><strong>Management Structure:</strong> <?= htmlspecialchars($row['management_structure_succession_strategy']) ?></p>
                                                    <p><strong>Long-Term Strategy:</strong> <?= htmlspecialchars($row['long_term_management_strategy']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No business loan applications found.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </section>

    <?php foreach ($rows as $row): ?>
    <div class="modal fade" id="editModal<?= $row['business_application_id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg update-form" method="POST" action="actions/update_loan.php">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Update Business Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="history_id" value="<?= $row['history_id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Risk Adjustment</label>
                        <select name="manual_risk_adjustment" class="form-select">
                            <option value="0" <?= (($row['manual_risk_adjustment'] ?? $row['prediction']) == 0) ? 'selected' : '' ?>>Low Risk</option>
                            <option value="1" <?= (($row['manual_risk_adjustment'] ?? $row['prediction']) == 1) ? 'selected' : '' ?>>High Risk</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-uppercase text-muted">Officer Remarks</label>
                        <textarea name="officer_notes" class="form-control" rows="4"><?= htmlspecialchars($row['officer_notes'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/history-actions.js" defer></script>

    <?php include "static/footer.php"?>
</body>
</html>