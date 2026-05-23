<?php
session_start();

// --- AUTHENTICATION CHECK ---
if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

// Include database connection configuration
include 'static/config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Home Loan Assessment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <style>
        body { background: #f4f6f9; font-family: 'Roboto', sans-serif; }
        .custom-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .section-title { color: #003366; font-weight: 700; border-left: 5px solid #c5a059; padding-left: 12px; margin-bottom: 20px; font-size: 1.1rem; }
        label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #555; margin-bottom: 5px; }
        .form-control, .form-select { border-radius: 8px; padding: 10px; border: 1px solid #ced4da; }
        .form-control:focus { border-color: #003366; box-shadow: none; }
        .bg-readonly { background-color: #e9ecef !important; opacity: 1; }
        .submit-btn {
            background-color: #003366;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 14px;
        }
        .submit-btn:hover { background-color: #002244; transform: translateY(-2px); }
    </style>
</head>
<body>

<section class="header-navbar">
    <?php include "static/navbar.php" ?>
</section>

<div class="container py-5" style="max-width: 1000px;">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="color: #003366; letter-spacing: 1px;">Individual Home Loan Risk Assessment System</h2>
    </div>

    <form action="home-preview.php" method="POST">
        <div class="custom-card p-4">
            <h5 class="section-title">I. Applicant Identification</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Full Name</label>
                    <input type="text" name="applicant_name" class="form-control" placeholder="Dela Cruz, Juan S." required>
                </div>
                <div class="col-md-6">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                </div>
                <div class="col-md-3">
                    <label>TIN Number</label>
                    <input type="text" id="tin_no" name="tin_no" class="form-control" placeholder="000-000-000-000" maxlength="15">
                </div>
                <div class="col-md-3">
                    <label>Birthdate</label>
                    <input type="date" name="birthdate" id="birthdate" class="form-control" required>
                </div>
                <div class="col-12">
                    <label>Current Home Address</label>
                    <input type="text" name="home_address" class="form-control" placeholder="Unit No, Street, Brgy, City, Province">
                </div>
            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">II. Personal Status & Residency</h5>
            <div class="row g-3">
                <div class="col-md-2">
                    <label>Age</label>
                    <input type="number" name="age" id="age" class="form-control bg-readonly" readonly>
                </div>
                <div class="col-md-2">
                    <label>Sex</label>
                    <select name="sex" class="form-select">
                        <option value="1">Male</option>
                        <option value="0">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Civil Status</label>
                    <select name="civil_status" class="form-select">
                        <option value="0">Single</option>
                        <option value="1">Married</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Dependents</label>
                    <input type="number" name="dependents" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                    <label>Years of Stay</label>
                    <input type="number" name="years_of_stay" class="form-control" value="1">
                </div>
                <div class="col-md-4 mt-3">
                    <label>Home Ownership</label>
                    <select name="home_ownership" class="form-select">
                        <option value="1">Owned</option>
                        <option value="0">Rent / Mortgaged</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">III. Employment & Financial Capacity</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Employment Type</label>
                    <select name="employment_type" class="form-select">
                        <option value="1">Regular</option>
                        <option value="0">Contractual</option>
                        <option value="2">Self-employed / OFW</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Gross Monthly Income</label>
                    <input type="number" name="monthly_income" id="income" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Date Hired</label>
                    <input type="date" name="date_hired" id="hired" class="form-control">
                </div>
                <div class="col-md-3 mt-3">
                    <label>Years Employed</label>
                    <input type="number" name="years_employed" id="years_employed" class="form-control bg-readonly" readonly>
                </div>
            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">IV. Collateral & Loan Information</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Collateral Type</label>
                    <select name="collateral_type" class="form-select">
                        <option value="House and Lot">House and Lot</option>
                        <option value="Condominium">Condominium</option>
                        <option value="Vacant Lot">Vacant Lot</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Appraisal Property Value</label>
                    <input type="number" name="property_value" class="form-control" placeholder="0.00" min="100000" step="0.01"required>
                </div>
                <div class="col-md-4">
                    <label>Loan Amount</label>
                    <input type="number" name="loan_amount" class="form-control" required>
                </div>
                <div class="col-md-3 mt-3">
                    <label>Term (Months)</label>
                    <input type="number" name="loan_term" class="form-control" placeholder="e.g. 180">
                </div>
            </div>
        </div>

        <div class="custom-card p-4">
            <h5 class="section-title">V. Risk Assessment Parameters</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Existing Loans?</label>
                    <select name="existing_loans" class="form-select">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Total Monthly Debt</label>
                    <input type="number" name="monthly_debt" id="debt" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                    <label>DTI Ratio</label>
                    <input type="text" name="dti_ratio" id="dti" class="form-control bg-readonly" readonly>
                </div>
                <div class="col-md-3">
                    <label>Default History?</label>
                    <select name="default_history" class="form-select">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
        </div>

<div>
    <button type="submit" class="btn btn-primary submit-btn shadow">
     Submit Application
    </button>
</div>
    </form>
</div>

<?php include "static/footer.php" ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="static/home-form.js?v=<?= time() ?>"></script>

</body>
</html>