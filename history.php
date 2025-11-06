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
$stmt = $pdo->prepare("SELECT * FROM loan_applications ORDER BY submitted_at DESC");
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
    <link rel="stylesheet" href="static/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <title>History</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <div class="container">
        <div class="history-container">
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No loan applications found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include "static/footer.php"?>
</body>
</html>