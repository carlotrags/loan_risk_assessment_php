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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <title>Business Loan Risk Assessment</title>

    <style>
        table.table td, table.table th {
            vertical-align: middle;
        }

        table.table input[type="radio"] {
            transform: scale(1.2);
            cursor: pointer;
        }

    </style>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <!-- 5 - Highly Satisfactory, 4 - Satisfactory, 3 - Neutral, 2 - Unsatisfactory, 1 - Highly Unsatisfactory -->

    <section class="container my-5">
        <form action="submit_business.php" method="POST">
            <table class="table table-boredered text-center">
                <thead>
                    <tr>
                        <th>Variable</th>
                        <th>5 - Highly Satisfactory</th>
                        <th>4 - Satisfactory</th>
                        <th>3 - Neutral</th>
                        <th>2 - Unsatisfactory</th>
                        <th>1 - Highly Unsatisfactory</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Capital to Risk Assets Ratio (%)</td>
                        <td><input type="radio" name="capital_to_risk_assets_ratio" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="capital_to_risk_assets_ratio" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="capital_to_risk_assets_ratio" value="3">3 - Neutral</td>
                        <td><input type="radio" name="capital_to_risk_assets_ratio" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="capital_to_risk_assets_ratio" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>Debt-to-Equity Ratio (X)</td>
                        <td><input type="radio" name="debt_to_equity_ratio" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="debt_to_equity_ratio" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="debt_to_equity_ratio" value="3">3 - Neutral</td>
                        <td><input type="radio" name="debt_to_equity_ratio" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="debt_to_equity_ratio" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>NPL Ratio</td>
                        <td><input type="radio" name="npl_ratio" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="npl_ratio" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="npl_ratio" value="3">3 - Neutral</td>
                        <td><input type="radio" name="npl_ratio" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="npl_ratio" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>NPA Ratio</td>
                        <td><input type="radio" name="npa_ratio" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="npa_ratio" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="npa_ratio" value="3">3 - Neutral</td>
                        <td><input type="radio" name="npa_ratio" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="npa_ratio" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>NPA Coverage Ratio</td>
                        <td><input type="radio" name="npa_coverage_ratio" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="npa_coverage_ratio" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="npa_coverage_ratio" value="3">3 - Neutral</td>
                        <td><input type="radio" name="npa_coverage_ratio" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="npa_coverage_ratio" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>ROAE</td>
                        <td><input type="radio" name="roae" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="roae" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="roae" value="3">3 - Neutral</td>
                        <td><input type="radio" name="roae" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="roae" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>ROAA</td>
                        <td><input type="radio" name="roaa" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="roaa" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="roaa" value="3">3 - Neutral</td>
                        <td><input type="radio" name="roaa" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="roaa" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>Cost to Income Ratio</td>
                        <td><input type="radio" name="cost_to_income_ratio" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="cost_to_income_ratio" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="cost_to_income_ratio" value="3">3 - Neutral</td>
                        <td><input type="radio" name="cost_to_income_ratio" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="cost_to_income_ratio" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>Liquid Assets to Borrowed Funds</td>
                        <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="3">3 - Neutral</td>
                        <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="5">5 - Highly Satisfactory</td>
                    </tr>
                    <tr>
                        <td>Debt Service Cover (X)</td>
                        <td><input type="radio" name="debt_service_cover" value="1">1 - Highly Unsatisfactory</td>
                        <td><input type="radio" name="debt_service_cover" value="2">2 - Unsatisfactory</td>
                        <td><input type="radio" name="debt_service_cover" value="3">3 - Neutral</td>
                        <td><input type="radio" name="debt_service_cover" value="4">4 - Satisfactory</td>
                        <td><input type="radio" name="debt_service_cover" value="5">5 - Highly Satisfactory</td>
                    </tr>
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </section>
    <script src="static/form.js" defer></script>
    <?php include "static/footer.php"?>
</body>
</html> 