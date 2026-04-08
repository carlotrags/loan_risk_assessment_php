<?php
// --- CRITICAL: Load Dompdf ---
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// --- CRITICAL SECURITY ---
ini_set('display_errors', 'Off');
error_reporting(E_ALL);

// --- START SESSION & AUTHENTICATION ---
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit("Authentication required.");
}

// --- CONFIGURATION AND DATA MAPPING ---
include 'static/config.php';

// --- RATE LIMITING ---
const RATE_WINDOW_SECONDS = 5;
const MAX_RATE_REQUESTS = 10;
const MAX_RECORDS_LIMIT = 5000;

if (!isset($_SESSION['download_timestamps']) || !is_array($_SESSION['download_timestamps'])) {
    $_SESSION['download_timestamps'] = [];
}

$currentTime = time();
$_SESSION['download_timestamps'] = array_filter(
    $_SESSION['download_timestamps'],
    fn($t) => $t > $currentTime - RATE_WINDOW_SECONDS
);

if (count($_SESSION['download_timestamps']) >= MAX_RATE_REQUESTS) {
    http_response_code(429);
    exit("Rate limit exceeded. Please slow down.");
}

$_SESSION['download_timestamps'][] = $currentTime;

// --- FILTERS ---
$loan_type = $_GET['loan_type'] ?? 'history';
$conditions = [];
$params = [];
$filterSummary = [];

// NAME FILTER
if (!empty($_GET['searchName'])) {
    $searchName = substr(trim($_GET['searchName']), 0, 100);
    $conditions[] = ($loan_type === 'business')
        ? "la.company_name LIKE :name"
        : "la.name LIKE :name";

    $params[':name'] = "%$searchName%";
    $filterSummary[] = "Name/Company: " . htmlspecialchars($searchName);
}

// ASSESSED BY FILTER
if (!empty($_GET['filterAssessmentBy'])) {
    $assessedBy = substr(trim($_GET['filterAssessmentBy']), 0, 100);
    $conditions[] = "CONCAT(ba.first_name,' ',ba.last_name) = :assessedBy";
    $params[':assessedBy'] = $assessedBy;
    $filterSummary[] = "Assessed By: " . htmlspecialchars($assessedBy);
}

// PREDICTION FILTER
if (isset($_GET['filterPrediction']) && $_GET['filterPrediction'] !== '') {
    $pred = (int)$_GET['filterPrediction'];
    if ($pred === 0 || $pred === 1) {
        $conditions[] = "COALESCE(h.manual_risk_adjustment, la.prediction) = :prediction";
        $params[':prediction'] = $pred;
        $filterSummary[] = "Risk Prediction: " . ($pred ? "High Risk" : "Low Risk");
    }
}

// DATE RANGE
if (!empty($_GET['dateFrom']) && strtotime($_GET['dateFrom'])) {
    $conditions[] = "DATE(la.submitted_at) >= :dateFrom";
    $params[':dateFrom'] = $_GET['dateFrom'];
    $filterSummary[] = "From: " . htmlspecialchars($_GET['dateFrom']);
}

if (!empty($_GET['dateTo']) && strtotime($_GET['dateTo'])) {
    $conditions[] = "DATE(la.submitted_at) <= :dateTo";
    $params[':dateTo'] = $_GET['dateTo'];
    $filterSummary[] = "To: " . htmlspecialchars($_GET['dateTo']);
}

$whereClause = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

// --- TABLE CONFIG ---
switch ($loan_type) {
    case 'personal':
        $table = 'personal_loan_applications';
        $columns = [
            'Name'=>'name',
            'Income'=>'income',
            'Credit Score'=>'credit_score',
            'Loan Amount'=>'loan_amount',
            'Loan Term'=>'loan_term',
            'Prediction'=>'prediction',
            'Submitted At'=>'submitted_at',
            'Assessment By'=>'assessment_by'
        ];
        break;

    case 'business':
        $table = 'business_loan_applications';
        $columns = [
            'Company Name'=>'company_name',
            'Loan Amount'=>'loan_amount',
            'Loan Term'=>'loan_term',
            'Prediction'=>'prediction',
            'Submitted At'=>'submitted_at',
            'Assessment By'=>'assessment_by',

            // FINANCIAL RATIOS
            'Capital to Risk Assets Ratio'=>'capital_to_risk_assets_ratio',
            'Debt to Equity Ratio'=>'debt_to_equity_ratio',
            'NPL Ratio'=>'npl_ratio',
            'NPA Ratio'=>'npa_ratio',
            'NPA Coverage Ratio'=>'npa_coverage_ratio',
            'ROAE'=>'roae',
            'ROAA'=>'roaa',
            'Cost to Income Ratio'=>'cost_to_income_ratio',
            'Liquid Assets to Borrowed Funds'=>'liquid_assets_to_borrowed_funds',
            'Debt Service Cover'=>'debt_service_cover',

            // QUALITATIVE
            'Threat of Entry'=>'threat_of_entry',
            'Intensity of Rivalry'=>'intensity_of_rivalry',
            'Substitution of Threat'=>'substitution_of_threat',
            'Buyer Bargaining Power'=>'buyer_bargaining_power',
            'Supplier Bargaining Power'=>'supplier_bargaining_power',
            'Overall Industry Outlook'=>'overall_industry_outlook',

            // MANAGEMENT
            'Market Position'=>'market_position',
            'Character of Management'=>'character_of_management',
            'Quality & Experience of Management'=>'quality_and_experience_of_management',
            'Bank Relationship'=>'bank_relationship',
            'Labor Relations'=>'labor_relations',
            'Existence'=>'existence',
            'NFIs CMAP Checkings'=>'nfis_cmap_checkings',
            'Management Control & Planning'=>'management_cntrl_business_planning',
            'Management Structure & Succession'=>'management_structure_succession_strategy',
            'Long Term Management Strategy'=>'long_term_management_strategy'
        ];
        break;

    default:
        $table = 'loan_application_history';
        $columns = [
            'Name'=>'name',
            'Loan Amount'=>'loan_amount',
            'Loan Term'=>'loan_term',
            'Prediction'=>'prediction',
            'Loan Type'=>'loan_type',
            'Submitted At'=>'submitted_at',
            'Assessment By'=>'assessment_by'
        ];
}

// --- PRE-COUNT ---
$countSql = "SELECT COUNT(*) 
             FROM $table la 
             INNER JOIN user_accounts ba ON la.user_id = ba.user_id 
             $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRecords = (int)$stmt->fetchColumn();

if ($totalRecords > MAX_RECORDS_LIMIT) {
    http_response_code(413);
    exit("Too many records ($totalRecords). Apply more filters.");
}

// --- FETCH DATA ---
$sql = "SELECT 
            ba.first_name, 
            ba.last_name, 
            ba.role, 
            la.*, 
            h.manual_risk_adjustment
        FROM $table la
        INNER JOIN user_accounts ba ON la.user_id = ba.user_id
        LEFT JOIN loan_application_history h 
            ON la.application_id = h.application_id 
            AND la.loan_type = h.loan_type
        $whereClause
        ORDER BY la.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- HTML ---
$html = '<html><head><style>
body{font-family:Arial,sans-serif;font-size:9pt;}
h2{text-align:center;margin-bottom:5px;}
.details-section{margin-bottom:15px;}
.details-section p{margin:2px 0;}
table{border-collapse:collapse;width:100%;margin-top:20px;}
th,td{border:1px solid #ccc;padding:6px;}
th{background:#263238;color:#fff;text-transform:uppercase;}
tr:nth-child(even){background:#f7f7f7;}
td.prediction.low{background:#28a745;color:#fff;font-weight:bold;text-align:center;}
td.prediction.high{background:#dc3545;color:#fff;font-weight:bold;text-align:center;}

.badge-pdf{
    display:inline-flex;
    align-items:center;
    padding:2px 6px;
    border-radius:50rem;
    font-size:8pt;
    margin-left:6px;
}

.role-manager{
    background:#f8d7da;
    color:#842029;
    border:1px solid #f8d7da;
}

.role-other{
    background:#cfe2ff;
    color:#0c63e4;
    border:1px solid #cfe2ff;
}

.filter-badge{
    background:#f0f0f0;
    padding:3px 6px;
    margin-right:5px;
    border-radius:3px;
    font-size:8pt;
}
</style></head><body>';

// HEADER
$html .= '<h2>Loan Report</h2>';
$html .= '<div class="details-section">';
$html .= '<p><strong>Generated:</strong> '.date('Y-m-d H:i:s').'</p>';
$html .= '<p><strong>Total Records:</strong> '.$totalRecords.'</p>';

if ($filterSummary) {
    $html .= '<p><strong>Filters Applied:</strong></p>';
    foreach ($filterSummary as $f) {
        $html .= '<span class="filter-badge">'.$f.'</span>';
    }
} else {
    $html .= '<p><strong>Filters Applied:</strong> None</p>';
}
$html .= '</div>';

// TABLE HEADER
$html .= '<table><thead><tr>';
foreach ($columns as $label => $db) {
    $html .= '<th>'.$label.'</th>';
}
$html .= '</tr></thead><tbody>';

// --- TABLE BODY ---
foreach ($rows as $row) {
    $html .= '<tr>';

    foreach ($columns as $db_col) {
        $value = $row[$db_col] ?? "";

        // Assessed By + BADGE
        if ($db_col === 'assessment_by') {
            $roleClass = ($row['role'] === 'Manager') ? 'role-manager' : 'role-other';
            $value = htmlspecialchars($row['first_name'].' '.$row['last_name'])
                   . '<span class="badge-pdf '.$roleClass.'">'
                   . htmlspecialchars($row['role'])
                   . '</span>';
        }

// PREDICTION
elseif ($db_col === 'prediction') {
    // Use manual override if exists, otherwise default prediction
    $finalRisk = ($row['manual_risk_adjustment'] !== null) 
                 ? $row['manual_risk_adjustment'] 
                 : $row['prediction'];

    $value = ($finalRisk == '1') ? "High Risk" : "Low Risk";
}
        // SUBMITTED_AT
        elseif ($db_col === 'submitted_at') {
            $value = date('Y-m-d H:i', strtotime($value));
        }

        // REMOVE DECIMALS
        elseif (is_numeric($value)) {
            $value = (string)(int)$value; 
        }

        // PESO for income + loan amount
        if (in_array($db_col, ['income','loan_amount']) && is_numeric($value)) {
            $value = "" . $value;
        }

        $class = ($db_col === 'prediction')
        ? ' class="prediction '.(($row['manual_risk_adjustment'] ?? $row['prediction'])=='1'?'high':'low').'"'
        : '';

        $html .= "<td{$class}>{$value}</td>";
    }

    $html .= '</tr>';
}

$html .= '</tbody></table></body></html>';

// --- DOMPDF ---
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);

$dompdf = new Dompdf($options);
$dompdf->setPaper('A4','landscape');
$dompdf->loadHtml($html);
$dompdf->render();

$filename = 'Loan_Report_'.date('Ymd_His').'.pdf';
$dompdf->stream($filename, ["Attachment"=>true]);
exit;
?>
