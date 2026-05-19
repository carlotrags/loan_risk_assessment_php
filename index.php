<?php
session_start();

// Standard headers to ensure the browser checks the session status every time
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

require 'static/config.php';

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$role = $_SESSION['role'];

// Summary counts for dashboard
$totals = [
    'total' => 0,
    'low_risk' => 0,
    'high_risk' => 0,
    'today' => 0,
    'month' => 0,
];

// Total assessments
$res = $conn->query("SELECT COUNT(*) AS total FROM loan_application_history");
if ($res) {
    $r = $res->fetch_assoc();
    $totals['total'] = (int)$r['total'];
}

// LOW RISK (manual overrides included)
$res = $conn->query("
    SELECT COUNT(*) AS low_risk 
    FROM loan_application_history 
    WHERE COALESCE(manual_risk_adjustment, prediction) = 1
");
if ($res) {
    $totals['low_risk'] = (int)$res->fetch_assoc()['low_risk'];
}

// HIGH RISK (manual overrides included)
$res = $conn->query("
    SELECT COUNT(*) AS high_risk 
    FROM loan_application_history 
    WHERE COALESCE(manual_risk_adjustment, prediction) = 0
");
if ($res) {
    $totals['high_risk'] = (int)$res->fetch_assoc()['high_risk'];
}

// Today's assessments
$res = $conn->query("SELECT COUNT(*) AS today FROM loan_application_history WHERE DATE(submitted_at) = CURDATE()");
if ($res) {
    $r = $res->fetch_assoc();
    $totals['today'] = (int)$r['today'];
}

// This month's assessments
$res = $conn->query("SELECT COUNT(*) AS month FROM loan_application_history WHERE YEAR(submitted_at) = YEAR(CURDATE()) AND MONTH(submitted_at) = MONTH(CURDATE())");
if ($res) {
    $r = $res->fetch_assoc();
    $totals['month'] = (int)$r['month'];
}

$sql = "SELECT l.name, l.loan_amount, l.prediction, l.manual_risk_adjustment, l.loan_type, l.submitted_at, b.first_name, b.last_name, b.role
        FROM loan_application_history AS l
        INNER JOIN user_accounts AS b ON l.user_id = b.user_id
        ORDER BY l.submitted_at DESC
        LIMIT 5";
$result = $conn->query($sql);

$rows = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS for utilities and icons and stuffs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJq0Xgk0v3Q9GqQ4jKk0rQ5F5p1bQ6I6Qe5Q5Q5Q5Q5Q5Q" crossorigin="anonymous">
    <!-- Bootstrap Icons and stuff-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/homestyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <title>Loan Risk Assessment</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="welcome-message">
            <h3>Welcome, <span><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></span></h3>
            <p><?= htmlspecialchars($_SESSION['role'])?></p>
        </div>

        <div class="summary-container">
            <h3>Assessments Summary</h3>
            <div class="summary-cells">
                <div class="summary-item card total">
                    <div class="item-inner">
                        <div class="item-icon"><i class="bi bi-bar-chart-fill" aria-hidden="true"></i></div>
                        <div class="item-body">
                            <div class="item-title">Total</div>
                            <div class="item-count"><?= htmlspecialchars($totals['total']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="summary-item card low_risk">
                    <div class="item-inner">
                        <div class="item-icon"><i class="bi bi-check-circle-fill" aria-hidden="true"></i></div>
                        <div class="item-body">
                            <div class="item-title">Low Risk</div>
                            <div class="item-count"><?= htmlspecialchars($totals['low_risk']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="summary-item card high_risk">
                    <div class="item-inner">
                        <div class="item-icon"><i class="bi bi-x-circle-fill" aria-hidden="true"></i></div>
                        <div class="item-body">
                            <div class="item-title">High Risk</div>
                            <div class="item-count"><?= htmlspecialchars($totals['high_risk']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="summary-item card today">
                    <div class="item-inner">
                        <div class="item-icon"><i class="bi bi-clock" aria-hidden="true"></i></div>
                        <div class="item-body">
                            <div class="item-title">Today</div>
                            <div class="item-count"><?= htmlspecialchars($totals['today']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="summary-item card month">
                    <div class="item-inner">
                        <div class="item-icon"><i class="bi bi-calendar3" aria-hidden="true"></i></div>
                        <div class="item-body">
                            <div class="item-title">This Month</div>
                            <div class="item-count"><?= htmlspecialchars($totals['month']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="recent-assessments">
            <h3>Recent Assessments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Loan Amount</th>
                        <th>Prediction</th>
                        <th>Loan Type</th>
                        <th>Submitted At</th>
                        <th>Assessment By</th>
                    </tr>
                </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>

                    <?php
                    $risk = ($row['manual_risk_adjustment'] !== null)
                        ? $row['manual_risk_adjustment']
                        : $row['prediction'];
                    ?>

                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>

                        <td>₱<?= number_format($row['loan_amount'], 2) ?></td>

                        <td class="prediction <?= $risk == 1 ? 'low' : 'high' ?>">
                            <?= $risk == 1 ? 'Low Risk' : 'High Risk' ?>
                        </td>

                        <td><?= htmlspecialchars($row['loan_type']) ?></td>

                        <td><?= htmlspecialchars($row['submitted_at']) ?></td>

                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    </tr>

                <?php endforeach; ?>
                </tbody>
            </table>
            <a href="history.php"><button class="btn btn-primary">See Full History</button></a>
        </div>


        <div class="tutorial-body">
            <h3>How does the assessment work?</h3>
            <ol>
                <li>Go to the <strong>assessment tab</strong> and select which loan type to assess.</li>
                <li>Fill up the form according to the <strong>client's required details</strong>.</li>
                <li>Submit assessment and let the system compute the results.</li>
                <li>The results will appear after the system calculates, showing if the client is <strong style="color: #1dff37;">Low Risk</strong> or <strong style="color: red;">High Risk</strong> for a loan.</li>
            </ol>
            <p>Click the button below to start Risk Assessment.</p>
            <a href="assessment.php"><button class="btn btn-primary">Start Assessment</button></a>
        </div>
        <br>
        
        <br>
        <div class="system-details-body">
            <p>This system is developed using HTML, CSS, PHP, and JavaScript and applies Logistic Regression as its primary predictive model for loan risk assessment.</p>
            
            <p>The model analyzes borrower data including financial capacity, credit history, employment profile, and loan characteristics to classify applicants into low-risk or high-risk categories.
            The system is designed to assist decision-making by providing data-driven insights that support faster and more consistent loan evaluation.</p>
        </div>
    </section>
    <?php include "static/footer.php"?>
<script>
    // Force the page to refresh if loaded from the back button, breaks the cache and forces PHP to re-evaluate the session
    window.addEventListener("pageshow", function (event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });

    // This pushes a new state so that the 'Back' action is intercepted
    (function() {
        window.history.pushState(null, "", window.location.href);        
        window.onpopstate = function() {
            window.history.go(-2);
        };
    })();
</script>
</body>
</html>