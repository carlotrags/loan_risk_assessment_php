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

include 'static/config.php';

$userStmt = $pdo->query("SELECT DISTINCT CONCAT(first_name,' ',last_name) AS full_name FROM user_accounts ORDER BY full_name ASC");
$allUsers = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$conditions = [];
$params = [];

// NAME FILTER
if (!empty($_GET['searchName'])) {
    $conditions[] = 'la.name LIKE :name';
    $params[':name'] = '%' . $_GET['searchName'] . '%';
}

// ASSESSED BY FILTER
if (!empty($_GET['filterAssessmentBy'])) {
    $conditions[] = 'CONCAT(ba.first_name, " ", ba.last_name) = :assessedBy';
    $params[':assessedBy'] = $_GET['filterAssessmentBy'];
}

// PREDICTION FILTER
if (isset($_GET['filterPrediction']) && $_GET['filterPrediction'] !== '') {
    $conditions[] = 'CAST(la.prediction AS CHAR) = :prediction';
    $params[':prediction'] = $_GET['filterPrediction'];
}

// DATE RANGE FILTER
if (!empty($_GET['dateFrom'])) {
    $conditions[] = 'DATE(la.submitted_at) >= :dateFrom';
    $params[':dateFrom'] = $_GET['dateFrom'];
}

if (!empty($_GET['dateTo'])) {
    $conditions[] = 'DATE(la.submitted_at) <= :dateTo';
    $params[':dateTo'] = $_GET['dateTo'];
}

$where = '';
if ($conditions) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

// Fetch Records
$sql = "SELECT 
        la.name, 
        la.loan_amount, 
        la.prediction, 
        la.loan_type,
        la.loan_term,
        la.submitted_at,
        ba.first_name, 
        ba.last_name, 
        ba.role
    FROM loan_application_history AS la
    INNER JOIN user_accounts AS ba ON la.user_id = ba.user_id";

if ($conditions) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY la.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Risk summary
$low_risk = 0;
$high_risk = 0;
foreach ($rows as $row) {
    if ($row['prediction'] == 0) $low_risk++;
    else $high_risk++;
}
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

    <title>History</title>
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

                            <!-- NAME -->
                            <input type="text" name="searchName" class="form-control" placeholder="Search name" value="<?= htmlspecialchars($_GET['searchName'] ?? '') ?>">

                            <!-- ASSESSED BY -->
                            <select name="filterAssessmentBy" class="form-select">
                                <option value="">Assessment By (All)</option>
                                <?php 
                                foreach ($allUsers as $user){
                                    $selected = (($_GET['filterAssessmentBy'] ?? '') === $user) ? 'selected' : '';
                                    echo "<option value=\"$user\" $selected>$user</option>";
                                }
                                ?>
                            </select>

                            <!-- PREDICTION -->
                            <select name="filterPrediction" class="form-select">
                                <option value="">Prediction (All)</option>
                                <option value="0" <?= (isset($_GET['filterPrediction']) && $_GET['filterPrediction']=='0')?'selected':'' ?>>Low Risk</option>
                                <option value="1" <?= (isset($_GET['filterPrediction']) && $_GET['filterPrediction']=='1')?'selected':'' ?>>High Risk</option>
                            </select>

                            <!-- DATES -->
                            <input type="date" name="dateFrom" class="form-control" value="<?= htmlspecialchars($_GET['dateFrom'] ?? '') ?>">
                            <input type="date" name="dateTo" class="form-control" value="<?= htmlspecialchars($_GET['dateTo'] ?? '') ?>">
                                
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <?php 
                            // Rebuild the current query string to pass filters to the PDF script 
                            $queryString = http_build_query($_GET);?>
                            <a href="generatepdf.php?<?= htmlspecialchars($queryString) ?>&loan_type=general" class="btn btn-success btn-pdf-narrow">
                            <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                            <a href="personal-history.php" class="btn btn-primary btn-pdf-narrow"><i class="fa-solid fa-user" style="padding-right: 10px;"></i>Personal Loans History</a>
                            <a href="business-history.php" class="btn btn-primary btn-pdf-narrow"><i class="fa-solid fa-briefcase" style="padding-right: 10px;"></i>Business Loans History</a>
                        </div>
                    </div>
                </form>

                <?php if (count($rows) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Loan Amount</th>
                                <th>Loan Term</th>
                                <th>Prediction</th>
                                <th>Loan Type</th>
                                <th>Submitted At</th>
                                <th>Assessment By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['loan_amount']) ?></td>
                                    <td><?= htmlspecialchars($row['loan_term']) ?></td>
                                    <td class="prediction <?= $row['prediction'] == 0 ? 'low' : 'high' ?>">
                                    <?= $row['prediction'] == 0 ? 'Low Risk' : 'High Risk' ?></td>
                                    <td><?= htmlspecialchars($row['loan_type']) ?></td>
                                    <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?> 
                                        <span class="badge align-items-center p-1 pe-2 ms-2 <?= $row['role'] === 'Manager' ? 
                                            'text-danger-emphasis bg-danger-subtle border border-danger-subtle' : 
                                            'text-primary-emphasis bg-primary-subtle border border-primary-subtle' ?> rounded-pill">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['first_name'] . '+' . $row['last_name']) ?>&size=16" class="rounded-circle me-1" width="24" height="24"  alt="profile">
                                            <?= htmlspecialchars($row['role']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No loan applications found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php include "static/footer.php"?>
</body>
</html>