<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: static/login.php");
        exit;
    }

    require 'static/config.php';

    $username = $_SESSION['username'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $role = $_SESSION['role'];

    $sql = "SELECT name, income, credit_score, loan_amount, prediction, submitted_at FROM loan_applications ORDER BY submitted_at DESC LIMIT 5";
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
    <link rel="stylesheet" href="static/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/navbarstyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/homestyle.css?v=<?= time() ?>">
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
                <div class="summary-item">
                    <h3>Total Assessments</h3>
                    <p>0</p>
                </div>
                <div class="summary-item">
                    <h3>Eligible Requests</h3>
                    <p>0</p>
                </div>
                <div class="summary-item">
                    <h3>Ineligible Requests</h3>
                    <p>0</p>
                </div>
                <div class="summary-item">
                    <h3>Today's Assessments</h3>
                    <p>0</p>
                </div>
                <div class="summary-item">
                    <h3>This Month's Assessments</h3>
                    <p>0</p>
                </div>
            </div>
        </div>

        <div class="recent-assessments">
            <h3>Recent Assessments</h3>
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
            <a href="history.php"><button>See Full History</button></a>
        </div>


        <div class="tutorial-body">
            <h3>How does the assessment work?</h3>
            <ol>
                <li>Go to the <strong>assessment tab</strong></li>
                <li>Fill up the form according to the <strong>client's required details</strong>.</li>
                <li>Submit assessment and let the system compute the results.</li>
                <li>The results will appear after the system calculates, showing if the client is <strong>eligible</strong> or <strong>ineligible</strong> for a loan.</li>
            </ol>
            <p>Click the button below to start Risk Assessment.</p>
            <a href="form.php"><button>Start Assessment</button></a>
        </div>
        <br>
        
        <br>
        <div class="system-details-body">
            <p>This website is created using HTML, CSS, PHP, and JavaScript. The system utilizes Logistic Regression for its assessment.</p>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laborum exercitationem excepturi, dolorum veniam et corporis iure reiciendis expedita earum modi repellat sapiente, numquam ab quas alias dignissimos animi explicabo maxime, consequatur adipisci distinctio at! Porro nisi velit provident laboriosam, harum earum sapiente. Commodi porro blanditiis dolorum eaque velit a optio.</p>
        </div>
    </section>
    <?php include "static/footer.php"?>
</body>
</html>