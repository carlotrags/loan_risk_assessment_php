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
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <title>Loan Applications - Admin View</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 40px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background-color: #263238;
            color: #fff;
        }
        canvas {
            margin-top: 30px;
            max-width: 500px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

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

<h2>Risk Summary</h2>
<canvas id="riskChart" width="400" height="200"></canvas>

<script>
    const ctx = document.getElementById('riskChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Low Risk', 'High Risk'],
            datasets: [{
                data: [<?= $low_risk ?>, <?= $high_risk ?>],
                backgroundColor: ['#4CAF50', '#F44336']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Loan Risk Assessment Overview' }
            }
        }
    });
</script>

</body>
</html>
<!-- this is drafts -->