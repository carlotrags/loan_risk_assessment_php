<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

include 'static/config.php';

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$role = $_SESSION['role'];

// Fetch Users for Filter
$userStmt = $pdo->query("SELECT DISTINCT CONCAT(first_name,' ',last_name) AS full_name FROM user_accounts ORDER BY full_name ASC");
$allUsers = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$conditions = [];
$params = [];

// FILTERS
if (!empty($_GET['searchName'])) {
    $conditions[] = 'la.name LIKE :name';
    $params[':name'] = '%' . $_GET['searchName'] . '%';
}
if (!empty($_GET['filterAssessmentBy'])) {
    $conditions[] = 'CONCAT(ba.first_name, " ", ba.last_name) = :assessedBy';
    $params[':assessedBy'] = $_GET['filterAssessmentBy'];
}
if (isset($_GET['filterPrediction']) && $_GET['filterPrediction'] !== '') {
    $conditions[] = 'la.prediction = :prediction';
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

$sql = "SELECT 
        la.history_id, 
        la.application_id,
        la.name,
        la.email,
        la.loan_amount, 
        la.prediction, 
        la.manual_risk_adjustment,
        la.loan_type,
        la.loan_term,
        la.submitted_at,
        la.updated_at,
        la.officer_notes,
        ba.first_name, 
        ba.last_name, 
        ba.role
    FROM loan_application_history AS la
    INNER JOIN user_accounts AS ba ON la.user_id = ba.user_id 
    $where
    ORDER BY la.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/historystyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>History</title>

    <style>
        .edit-timer { 
            display: block; 
            font-size: 10px; 
            color: #8e8e8e; 
            margin-top: 2px; 
            font-weight: normal;
        }
        td.prediction { line-height: 1.2; vertical-align: middle !important; }
    </style>
</head>

<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="container-fluid px-4">
            <div class="history-container w-auto">
                <h2>Loan Application Records</h2>

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
                                <option value="1" <?= (isset($_GET['filterPrediction']) && $_GET['filterPrediction']=='1')?'selected':'' ?>>Low Risk</option>
                                <option value="0" <?= (isset($_GET['filterPrediction']) && $_GET['filterPrediction']=='0')?'selected':'' ?>>High Risk</option>
                            </select>

                            <input type="date" name="dateFrom" class="form-control" value="<?= htmlspecialchars($_GET['dateFrom'] ?? '') ?>">
                            <input type="date" name="dateTo" class="form-control" value="<?= htmlspecialchars($_GET['dateTo'] ?? '') ?>">
                                
                            <button type="submit" class="btn btn-primary">Filter</button>
                            
                            <?php $queryString = http_build_query($_GET); ?>
                            <a href="generatepdf.php?<?= htmlspecialchars($queryString) ?>&loan_type=general" class="btn btn-success btn-pdf-narrow">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                            <!-- <a href="personal-history.php" class="btn btn-primary">
                                <i class="fa-solid fa-user" style="padding-right: 10px;"></i>Personal Loans
                            </a> -->
                            <a href="business-history.php" class="btn btn-primary">
                                <i class="fa-solid fa-briefcase" style="padding-right: 10px;"></i>Business Loans
                            </a>
                            <a href="home-history.php" class="btn btn-primary">
                                <i class="fa-solid fa-house" style="padding-right: 10px;"></i>Home Loans
                            </a>
                        </div>
                    </div>
                </form>

                <div style="margin-bottom:10px;">
                    <button type="submit" form="bulkForm" id="deleteBtn" class="btn btn-secondary" disabled onclick="return confirm('Delete selected records?')">
                        <i class="fa-solid fa-trash"></i> Delete Selected
                    </button>
                </div>

                <?php if (count($rows) > 0): ?>
                    <form method="POST" action="actions/bulk_delete.php" id="bulkForm">
                        <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Name / Email Address</th>
                                    <th>Loan Amount</th>
                                    <th>Loan Term</th>
                                    <th>Prediction</th>
                                    <th>Loan Type</th>
                                    <th>Submitted At</th>
                                    <th>Assessment By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr id="row-<?= $row['history_id'] ?>">
                                        <td><input type="checkbox" class="rowCheckbox" name="selected_ids[]" value="<?= $row['history_id'] ?>"></td>
                                        <td><?= htmlspecialchars($row['name']) ?><br><p style="font-size: 12px; color: #838995;"><?= htmlspecialchars($row['email']) ?></p></td>
                                        <td>₱<?= number_format($row['loan_amount'], 2) ?></td>
                                        <td><?= htmlspecialchars($row['loan_term']) ?></td>
                                        
                                        <td class="prediction <?= (($row['manual_risk_adjustment'] ?? $row['prediction']) == 1) ? 'low' : 'high' ?>">
                                            <span class="risk-label">
                                                <?= ($row['manual_risk_adjustment'] !== null) ? 
                                                    (($row['manual_risk_adjustment'] == 1) ? 'Low Risk' : 'High Risk') : 
                                                    (($row['prediction'] == 1) ? 'Low Risk' : 'High Risk') 
                                                ?>
                                            </span>
                                            
                                            <?php if (!empty($row['updated_at'])): ?>
                                                <span class="edit-timer" data-timestamp="<?= strtotime($row['updated_at']) ?>"></span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= htmlspecialchars($row['loan_type']) ?></td>
                                        <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                                        
                                        <td>
                                            <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?> 
                                            <span class="badge align-items-center p-1 pe-2 ms-2 
                                            <?= $row['role'] === 'System Admin' 
                                                ? 'text-purple-emphasis bg-purple-subtle border border-purple-subtle' 
                                                : ($row['role'] === 'Manager'
                                                    ? 'text-danger-emphasis bg-danger-subtle border border-danger-subtle'
                                                    : 'text-primary-emphasis bg-primary-subtle border border-primary-subtle'
                                                )
                                            ?>
                                            rounded-pill">
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['first_name'] . '+' . $row['last_name']) ?>&size=16" class="rounded-circle me-1" width="24" height="24" alt="profile">
                                                <?= htmlspecialchars($row['role']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-3">
                                                <a href="view-details.php?id=<?= $row['application_id'] ?>&type=<?= strtolower($row['loan_type']) ?>" class="text-info" ><i class="fa-solid fa-eye" alt="View"></i></a>
                                                <a href="#" class="text-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['history_id'] ?>" title="Edit"><i class="fa-solid fa-pen-to-square" alt="Edit"></i></a>
                                                <a href="actions/delete_loan.php?id=<?= $row['history_id'] ?>" 
                                                class="text-danger" 
                                                onclick="return confirm('Are you sure you want to delete this loan record?')" 
                                                title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    </form>
                <?php else: ?>
                    <p>No loan applications found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php foreach ($rows as $row): ?>
    <div class="modal fade" id="editModal<?= $row['history_id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg update-form" data-id="<?= $row['history_id'] ?>" method="POST">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Update Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="history_id" value="<?= $row['history_id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Risk Adjustment</label>
                        <select name="manual_risk_adjustment" class="form-select">
                            <option value="1" <?= (($row['manual_risk_adjustment'] ?? $row['prediction']) == 1) ? 'selected' : '' ?>>Low Risk</option>
                            <option value="0" <?= (($row['manual_risk_adjustment'] ?? $row['prediction']) == 0) ? 'selected' : '' ?>>High Risk</option>
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