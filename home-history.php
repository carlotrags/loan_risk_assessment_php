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

/* BASE CONDITION */
$conditions[] = "1=1";

/* FILTERS */
if (!empty($_GET['searchName'])) {
    $conditions[] = 'h.name LIKE :name';
    $params[':name'] = '%' . $_GET['searchName'] . '%';
}

if (!empty($_GET['filterAssessmentBy'])) {
    $conditions[] = 'CONCAT(ba.first_name, " ", ba.last_name) = :assessedBy';
    $params[':assessedBy'] = $_GET['filterAssessmentBy'];
}

if (isset($_GET['filterPrediction']) && $_GET['filterPrediction'] !== '') {
    $conditions[] = 'COALESCE(lh.manual_risk_adjustment, h.prediction) = :prediction';
    $params[':prediction'] = $_GET['filterPrediction'];
}

if (!empty($_GET['dateFrom'])) {
    $conditions[] = 'DATE(h.submitted_at) >= :dateFrom';
    $params[':dateFrom'] = $_GET['dateFrom'];
}

if (!empty($_GET['dateTo'])) {
    $conditions[] = 'DATE(h.submitted_at) <= :dateTo';
    $params[':dateTo'] = $_GET['dateTo'];
}

$where = ' WHERE ' . implode(' AND ', $conditions);

/* QUERY */
$sql = "SELECT 
        h.home_application_id,
        h.name,
        h.email,
        h.loan_amount,
        h.loan_term,
        COALESCE(lh.manual_risk_adjustment, h.prediction) AS final_prediction,
        h.submitted_at,
        h.assessed_by,
        lh.updated_at,
        lh.history_id,
        lh.manual_risk_adjustment,
        lh.officer_notes,
        ba.first_name,
        ba.last_name,
        ba.role
    FROM home_loan_applications AS h

    LEFT JOIN loan_application_history AS lh 
        ON h.home_application_id = lh.application_id
        AND lh.loan_type = 'home'
        AND lh.history_id = (
            SELECT MAX(history_id)
            FROM loan_application_history lh2
            WHERE lh2.application_id = h.home_application_id
        )

    INNER JOIN user_accounts AS ba 
        ON h.assessed_by = ba.user_id
    $where
    ORDER BY h.submitted_at DESC";

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
    <title>History - Home Loans</title>
</head>

<body>

<section class="header-navbar">
    <?php include "static/navbar.php" ?>
</section>

<section class="container">
    <div class="container-fluid px-4">
        <div class="history-container w-auto">

            <h2>Home Loan Records</h2>

            <form method="GET">
                <div class="filter-bar">
                    <div class="filter-grid">

                        <input type="text" name="searchName" class="form-control"
                            placeholder="Search name"
                            value="<?= htmlspecialchars($_GET['searchName'] ?? '') ?>">

                        <select name="filterAssessmentBy" class="form-select">
                            <option value="">Assessment By (All)</option>
                            <?php foreach ($allUsers as $user): ?>
                                <option value="<?= $user ?>" <?= (($_GET['filterAssessmentBy'] ?? '') === $user) ? 'selected' : '' ?>>
                                    <?= $user ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="filterPrediction" class="form-select">
                            <option value="">Prediction (All)</option>
                            <option value="0" <?= (($_GET['filterPrediction'] ?? '') === '1') ? 'selected' : '' ?>>Low Risk</option>
                            <option value="1" <?= (($_GET['filterPrediction'] ?? '') === '0') ? 'selected' : '' ?>>High Risk</option>
                        </select>

                        <input type="date" name="dateFrom" class="form-control"
                            value="<?= htmlspecialchars($_GET['dateFrom'] ?? '') ?>">

                        <input type="date" name="dateTo" class="form-control"
                            value="<?= htmlspecialchars($_GET['dateTo'] ?? '') ?>">

                        <button type="submit" class="btn btn-primary">Filter</button>

                        <?php $queryString = http_build_query($_GET); ?>

                        <a href="generatepdf.php?<?= htmlspecialchars($queryString) ?>&loan_type=home"
                            class="btn btn-success btn-pdf-narrow">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        <!-- <a href="personal-history.php" class="btn btn-primary">
                            <i class="fa-solid fa-user" style="padding-right: 10px;"></i>Personal Loans
                        </a> -->
                        <a href="history.php" class="btn btn-primary">
                            <i class="fa-solid fa-clock-rotate-left" style="padding-right: 10px;"></i>General History
                        </a>
                        <a href="business-history.php" class="btn btn-primary">
                            <i class="fa-solid fa-briefcase" style="padding-right: 10px;"></i>Business Loans
                        </a>
                    </div>
                </div>
            </form>

            <div style="margin-bottom:10px;">
                <button type="submit" form="bulkForm" id="deleteBtn"
                    class="btn btn-secondary" disabled
                    onclick="return confirm('Delete selected records?')">
                    <i class="fa-solid fa-trash"></i> Delete Selected
                </button>
            </div>

            <?php if (count($rows) > 0): ?>
                <form method="POST" action="actions/bulk_delete.php" id="bulkForm">

                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Name / Email</th>
                                <th>Loan Amount</th>
                                <th>Loan Term</th>
                                <th>Prediction</th>
                                <th>Submitted At</th>
                                <th>Assessment By</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="rowCheckbox"
                                            name="selected_ids[]"
                                            value="<?= $row['home_application_id'] ?>">
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['name']) ?><br>
                                        <span style="font-size:12px;color:#838995;">
                                            <?= htmlspecialchars($row['email']) ?>
                                        </span>
                                    </td>

                                    <td>₱<?= number_format($row['loan_amount'], 2) ?></td>

                                    <td><?= htmlspecialchars($row['loan_term']) ?> Months</td>

                                    <td class="prediction <?= ($row['final_prediction'] == 1) ? 'low' : 'high' ?>">
                                        <span class="risk-label">
                                            <?= ($row['final_prediction'] == 1) ? 'Low Risk' : 'High Risk' ?>
                                        </span>

                                        <?php if (!empty($row['updated_at'])): ?>
                                            <span class="edit-timer" data-timestamp="<?= strtotime($row['updated_at']) ?>"></span>
                                        <?php endif; ?>
                                    </td>
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

                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['first_name'] . '+' . $row['last_name']) ?>&size=16"
                                                class="rounded-circle me-1" width="24" height="24" alt="profile">

                                            <?= htmlspecialchars($row['role']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-3">
                                            <a href="view-details.php?id=<?= $row['home_application_id'] ?>&type=home" class="text-info"><i class="fa-solid fa-eye"></i></a>
                                            <a href="#" class="text-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['home_application_id'] ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="actions/delete_home_loan.php?id=<?= $row['home_application_id'] ?>" class="text-danger" onclick="return confirm('Delete this record?')"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </form>
            <?php else: ?>
                <p>No home loan applications found.</p>
            <?php endif; ?>

        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="static/history-actions.js" defer></script>

<?php include "static/footer.php" ?>

</body>
</html>