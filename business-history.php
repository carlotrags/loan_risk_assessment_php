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

$userStmt = $pdo->query("SELECT DISTINCT CONCAT(first_name,' ',last_name) AS full_name FROM bank_accounts ORDER BY full_name ASC");
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
        la.company_name, 
        la.loan_amount, 
        la.prediction, 
        la.loan_term,
        la.loan_type, 
        la.submitted_at,
        ba.first_name, 
        ba.last_name, 
        ba.role,
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
        la.intensity_of_entry,
        la.substitution_threat,
        la.buyer_bargaining_power,
        la.supplier_bargaining_power,
        la.overall_industry_outlook,
        la.market_position,
        la.character_of_management,
        la.quality_and_experience_management,
        la.bank_relationship,
        la.labor_relations,
        la.existence,
        la.nfis_cmap_checkings,
        la.management_cntrl_business_planning,
        la.management_structure_succession_strategy,
        la.long_term_management_strategy
    FROM business_loan_applications AS la
    INNER JOIN bank_accounts AS ba ON la.user_id = ba.user_id";

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

    <title>History - Business Loans</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="container-fluid px-4">
            <div class="history-container w-auto">
                <h2>Loan Application Records (Business Loan Applications)</h2>

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
                            <a href="generatepdf.php?<?= htmlspecialchars($queryString) ?>&loan_type=business" class="btn btn-success btn-pdf-narrow">
                            <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </form>

                <?php if (count($rows) > 0): ?>
                    <table>
                        <thead>
                            <tr class="main-details">
                                <th>Company Name</th>
                                <th>Loan Amount</th>
                                <th>Loan Term</th>
                                <th>Prediction</th>
                                <th>Submitted At</th>
                                <th>Assessment By</th>
                                <th>More Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                                    <td><?= htmlspecialchars($row['loan_amount']) ?></td>
                                    <td><?= htmlspecialchars($row['loan_term']) ?></td>
                                    <td class="prediction <?= $row['prediction'] == 0 ? 'low' : 'high' ?>">
                                    <?= $row['prediction'] == 0 ? 'Low Risk' : 'High Risk' ?></td>
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
                                    <td class="more-details"><button class="toggle-more-details" type="button">
                                        <i class="fa-solid fa-sort-down"></i>
                                    </button></td>
                                </tr>
                                <tr class="hidden-details">
                                <!-- Hidden Variables / More detailed view -->
                                    <td colspan="8">
                                        <div class="hidden-variables-container">
                                            <div class="hidden-item">
                                            <h4>Financial Condition</h4>
                                                <p><strong>Capital to Risk Assets Ratio:</strong> <?= htmlspecialchars($row['capital_to_risk_assets_ratio']) ?></p>
                                                <p><strong>Debt to Equity Ratio:</strong> <?= htmlspecialchars($row['debt_to_equity_ratio']) ?></p>
                                                <p><strong>NPL Ratio: </strong> <?= htmlspecialchars($row['npl_ratio']) ?></p>
                                                <p><strong>NPA Ratio: </strong> <?= htmlspecialchars($row['npa_ratio']) ?></p>
                                                <p><strong>NPA Coverage Ratio: </strong> <?= htmlspecialchars($row['npa_coverage_ratio']) ?></p>
                                                <p><strong>ROAE: </strong> <?= htmlspecialchars($row['roae']) ?></p>
                                                <p><strong>ROAA: </strong> <?= htmlspecialchars($row['roaa']) ?></p>
                                                <p><strong>Cost to Income Ratio: </strong> <?= htmlspecialchars($row['cost_to_income_ratio']) ?></p>
                                                <p><strong>Liquid Assets to Borrowed Funds: </strong> <?= htmlspecialchars($row['liquid_assets_to_borrowed_funds']) ?></p>
                                                <p><strong>Debt Service Cover: </strong> <?= htmlspecialchars($row['debt_service_cover']) ?></p>
                                            </div>
                                            <div class="hidden-item">
                                                <h4>Industry/Market Analysis</h4>
                                                <p><strong>Threat of Entry: </strong> <?= htmlspecialchars($row['threat_of_entry']) ?></p>
                                                <p><strong>Intensity of Entry: </strong> <?= htmlspecialchars($row['intensity_of_entry']) ?></p>
                                                <p><strong>Substitution Threat: </strong> <?= htmlspecialchars($row['substitution_threat']) ?></p>
                                                <p><strong>Buyer Bargaining Power: </strong> <?= htmlspecialchars($row['buyer_bargaining_power']) ?></p>
                                                <p><strong>Supplier Bargaining Power: </strong> <?= htmlspecialchars($row['supplier_bargaining_power']) ?></p>
                                                <p><strong>Overall Industry Outlook: </strong> <?= htmlspecialchars($row['overall_industry_outlook']) ?></p>
                                                <p><strong>Market Position: </strong> <?= htmlspecialchars($row['market_position']) ?></p>
                                            </div>
                                            <div class="hidden-item">
                                                <h4>Management Quality</h4>
                                                <p><strong>Character of Management: </strong> <?= htmlspecialchars($row['character_of_management']) ?></p>
                                                <p><strong>Quality and Experience Management: </strong> <?= htmlspecialchars($row['quality_and_experience_management']) ?></p>
                                                <p><strong>Bank Relationship: </strong> <?= htmlspecialchars($row['bank_relationship']) ?></p>
                                                <p><strong>Labor Relations: </strong> <?= htmlspecialchars($row['labor_relations']) ?></p>
                                                <p><strong>Existence: </strong> <?= htmlspecialchars($row['existence']) ?></p>
                                                <p><strong>NFIS/CMAP Checkings: </strong> <?= htmlspecialchars($row['nfis_cmap_checkings']) ?></p>
                                                <p><strong>Management Control and Business Planning: </strong> <?= htmlspecialchars($row['management_cntrl_business_planning']) ?></p>
                                                <p><strong>Management Structure and Succession Strategy: </strong> <?= htmlspecialchars($row['management_structure_succession_strategy']) ?></p>
                                                <p><strong>Clear Long-Term Management Strategy: </strong> <?= htmlspecialchars($row['long_term_management_strategy']) ?></p>
                                            </div>
                                        </div>
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
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButtons = document.querySelectorAll('.toggle-more-details');

        toggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {

                const currentRow = btn.closest('tr');
                const currentDetails = currentRow.nextElementSibling;

                document.querySelectorAll('.hidden-details.show-details').forEach(openRow => {
                    if (openRow !== currentDetails) {
                        openRow.classList.remove('show-details');
                        
                        const otherBtn = openRow.previousElementSibling.querySelector('.toggle-more-details i');
                        otherBtn.classList.add('fa-sort-down');
                        otherBtn.classList.remove('fa-sort-up');
                    }
                });

                currentDetails.classList.toggle('show-details');

                const icon = btn.querySelector('i');
                icon.classList.toggle('fa-sort-down');
                icon.classList.toggle('fa-sort-up');
            });
        });
    });
    </script>
    <?php include "static/footer.php"?>
</body>
</html>