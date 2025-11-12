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
?>

<?php
include 'static/config.php';

// Fetch records
$stmt = $pdo->prepare("SELECT 
        la.name, 
        la.income, 
        la.credit_score, 
        la.loan_amount, 
        la.prediction, 
        la.submitted_at,
        ba.first_name, 
        ba.last_name, 
        ba.role
    FROM loan_applications AS la
    INNER JOIN bank_accounts AS ba ON la.user_id = ba.user_id
    ORDER BY la.submitted_at DESC ");
$stmt->execute();
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
    <link rel="stylesheet" href="static/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <title>History</title>
    <style>
            .header-navbar .main-navbar { margin-right: 555px !important; 
            gap: 20px !important;}
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

                <?php if (count($rows) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Income</th>
                                <th>Credit Score</th>
                                <th>Loan Amount</th>
                                <th>Prediction</th>
                                <th>Submitted At</th>
                                <th>Assessment By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['income']) ?></td>
                                    <td><?= htmlspecialchars($row['credit_score']) ?></td>
                                    <td><?= htmlspecialchars($row['loan_amount']) ?></td>
                                    <td><?= $row['prediction'] == 0 ? 'Low Risk' : 'High Risk' ?></td>
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