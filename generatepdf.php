<?php
// CRITICAL: This line loads Dompdf and all its required files.
require 'vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

// --- CRITICAL SECURITY - Hide errors from users ---
// Prevents exposing server paths, code snippets, or database schema details
ini_set('display_errors', 'Off');
error_reporting(E_ALL);


// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    // If user is not logged in, stop the script.
    // SECURITY: Return 401 Unauthorized response code
    http_response_code(401);
    exit("Authentication required.");
}

// --- CONFIGURATION AND DATA MAPPING ---
// Includes your config and initializes $pdo
include 'static/config.php';

// --- ADVANCED SECURITY - SLIDING WINDOW RATE LIMITING ---
// Define the window size (5 seconds) and the maximum requests allowed in that window (10)
const RATE_WINDOW_SECONDS = 5;
const MAX_RATE_REQUESTS = 10;
// Hard limit on the number of records to prevent Resource Exhaustion (DoS)
const MAX_RECORDS_LIMIT = 5000; 

// Initialize session timestamp array if not present
if (!isset($_SESSION['download_timestamps']) || !is_array($_SESSION['download_timestamps'])) {
    $_SESSION['download_timestamps'] = [];
}

$currentTime = time();
$timestamps = $_SESSION['download_timestamps'];

// 1. Clean the window - Remove timestamps older than RATE_WINDOW_SECONDS
$windowStart = $currentTime - RATE_WINDOW_SECONDS;
$filteredTimestamps = array_filter($timestamps, function($t) use ($windowStart) {
    return $t > $windowStart;
});

// 2. Check the count - If the remaining count is at or above the limit, block.
if (count($filteredTimestamps) >= MAX_RATE_REQUESTS) {
    // SECURITY: Stop execution and return 429 Too Many Requests
    http_response_code(429);
    exit("Rate limit exceeded: You are limited to " . MAX_RATE_REQUESTS . " downloads per " . RATE_WINDOW_SECONDS . " seconds.");
}

// 3. Allow the request and log the timestamp
$filteredTimestamps[] = $currentTime;
// Only keep the last MAX_RATE_REQUESTS + 1 elements to prevent the session array from growing too large
$_SESSION['download_timestamps'] = array_slice($filteredTimestamps, - (MAX_RATE_REQUESTS + 1));


// Define risk mappings for clarity 
const RISK_MAPPING = [
    // Low Risk and High Risk definitions colors you can customize thisss
    0 => ['text' => 'Low Risk', 'class' => 'low', 'color' => '#28a745'], 
    1 => ['text' => 'High Risk', 'class' => 'high', 'color' => '#dc3545'], 
];

// --- START - DATA RETRIEVAL & FILTERING LOGIC ---
$conditions = [];
$params = [];
$filterSummary = [];

// 1. Filter by Name (Uses prepared statement with LIKE)
if (!empty($_GET['searchName'])) {
    $searchName = trim($_GET['searchName']);
    // SECURITY: Limit input length to prevent excessive data or malicious payload
    if (strlen($searchName) > 100) { 
        $searchName = substr($searchName, 0, 100);
    }
    $conditions[] = 'la.name LIKE :name';
    $params[':name'] = '%' . $searchName . '%';
    $filterSummary[] = "Applicant Name: " . htmlspecialchars($searchName);
}

// 2. Filter by Assessed By (Uses prepared statement)
if (!empty($_GET['filterAssessmentBy'])) {
    $assessedBy = trim($_GET['filterAssessmentBy']);
    // SECURITY: Limit input length
    if (strlen($assessedBy) > 100) {
        $assessedBy = substr($assessedBy, 0, 100);
    }
    $conditions[] = 'CONCAT(ba.first_name, " ", ba.last_name) = :assessedBy';
    $params[':assessedBy'] = $assessedBy;
    $filterSummary[] = "Assessor: " . htmlspecialchars($assessedBy);
}

// 3. Filter by Prediction (0 or 1 or 'All') - SAFEST TYPE HINTING
if (isset($_GET['filterPrediction'])) {
    $prediction = (string)$_GET['filterPrediction'];
    
    // Check if a specific prediction (0 or 1) is selected
    if ($prediction !== '') {
        $predictionInt = (int)$prediction;
        
        // SECURITY: Only allow 0 or 1 to be passed as an integer parameter
        if ($predictionInt === 0 || $predictionInt === 1) {
             $conditions[] = 'la.prediction = :prediction';
             $params[':prediction'] = $predictionInt; // Pass the integer value
            
             $riskInfo = RISK_MAPPING[$predictionInt] ?? ['text' => 'Unknown'];
             $filterSummary[] = "Risk Prediction: " . $riskInfo['text'];
        }
    } else {
        // If filterPrediction is set but empty (''), it means 'All' was selected.
        $filterSummary[] = "Risk Prediction: All";
    }
}

// 4. Filter by Date Range (SAFE: Uses prepared statements)
if (!empty($_GET['dateFrom'])) {
    $dateFrom = $_GET['dateFrom'];
    // SECURITY - Validate date format
    if (strtotime($dateFrom) !== false) {
        $conditions[] = 'DATE(la.submitted_at) >= :dateFrom';
        $params[':dateFrom'] = $dateFrom;
        $filterSummary[] = "From Date: " . htmlspecialchars($dateFrom);
    }
}
if (!empty($_GET['dateTo'])) {
    $dateTo = $_GET['dateTo'];
    // SECURITY - Validate date format
    if (strtotime($dateTo) !== false) {
        $conditions[] = 'DATE(la.submitted_at) <= :dateTo';
        $params[':dateTo'] = $dateTo;
        $filterSummary[] = "To Date: " . htmlspecialchars($dateTo);
    }
}

// Construct the base SQL components
$whereClause = $conditions ? " WHERE " . implode(' AND ', $conditions) : "";
$joinClause = "INNER JOIN bank_accounts AS ba ON la.user_id = ba.user_id";

// --- NEW SECURITY CHECK - PRE-COUNT the number of records ---
$countSql = "SELECT COUNT(*) AS total FROM loan_applications AS la " . $joinClause . $whereClause;

try {
    $countStmt = $pdo->prepare($countSql);
    
    // Bind parameters for the COUNT query
    if (isset($params[':prediction'])) {
        $countStmt->bindValue(':prediction', $params[':prediction'], PDO::PARAM_INT);
    }
    // Bind all other parameters
    foreach ($params as $key => &$value) {
        // Skip  -prediction as it's already bound or handled by the main query binding below
        if ($key !== ':prediction') {
            $countStmt->bindValue($key, $value);
        }
    }
    
    $countStmt->execute();
    $totalRecords = (int)$countStmt->fetchColumn();

    // RESOURCE EXHAUSTION PROTECTION - Check limit early
    if ($totalRecords > MAX_RECORDS_LIMIT) {
        http_response_code(413); // Payload Too Large
        exit("The number of matching records (" . $totalRecords . ") exceeds the server limit of " . MAX_RECORDS_LIMIT . ". Please narrow your search.");
    }
    
} catch (PDOException $e) {
    // SECURITY: Log the actual error
    error_log("Database COUNT Error in generate_report.php: " . $e->getMessage());
    http_response_code(500);
    exit("Internal Server Error: Could not verify record count.");
}

// Construct the main data SQL query
$sql = "SELECT 
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
    " . $joinClause . $whereClause;

$sql .= " ORDER BY la.submitted_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    
    // EXECUTION - Bind specific types for added security and efficiency
    if (isset($params[':prediction'])) {
        $stmt->bindValue(':prediction', $params[':prediction'], PDO::PARAM_INT);
        // Remove from $params so we don't try to bind it twice in the generic loop
        unset($params[':prediction']);
    }
    
    // Execute with remaining parameters (which are all strings)
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ensure the number of fetched rows matches the count (optional check, main check is pre-count)
    if (count($rows) !== $totalRecords) {
        // Log a warning - Data mismatch between COUNT and FETCH results
        error_log("Warning: Fetched row count (" . count($rows) . ") does not match pre-count (" . $totalRecords . ").");
    }

} catch (PDOException $e) {
    // SECURITY - Log the actual error to a file/system log, NOT directly to the user
    error_log("Database FETCH Error in generate_report.php: " . $e->getMessage());
    
    // Handle database error gracefully without exposing details
    http_response_code(500);
    exit("Internal Server Error: Could not process the request.");
}
// --- END: DATA RETRIEVAL & FILTERING LOGIC ---


// 1. HTML GENERATION for the PDF content you can customize styles here if needed lols
// --- HTML HEADER AND TABLE OPENING ---
$html = '
<html>
<head>
    <style>
        @page { margin: 40px; }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
        }
        h2 { 
            text-align: center; 
            margin-bottom: 5px; 
            color: #263238; /* Dark Grey */
        }
        .report-info {
            font-size: 10pt;
            margin-bottom: 20px;
            color: #555;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }
        .report-info strong {
            font-weight: bold;
            color: #333;
        }
        .filter-list {
            margin-top: 5px;
            font-size: 9pt;
            list-style: none;
            padding-left: 0;
        }
        .filter-list li {
            display: inline-block;
            margin-right: 15px;
            padding: 2px 5px;
            background-color: #f0f0f0;
            border-radius: 3px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 9pt; /* Smaller font for landscape fit */
        }
        th, td { 
            padding: 10px 8px; 
            border: 1px solid #e0e0e0; 
            text-align: left; 
        }
        th { 
            background-color: #263238; /* Header Background */
            color: #fff; 
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        /* Dynamic Prediction Styles based on RISK_MAPPING */
        td.prediction.low { 
            background-color: ' . RISK_MAPPING[0]['color'] . '; 
            color: #fff; 
            font-weight: bold; 
            text-align: center; 
        }
        td.prediction.high { 
            background-color: ' . RISK_MAPPING[1]['color'] . '; 
            color: #fff; 
            font-weight: bold; 
            text-align: center; 
        }

        /* --- START: Bootstrap Badge Emulation for PDF --- */
        .badge-pdf {
            display: inline-block;
            padding: 3px 6px;
            font-size: 8pt;
            font-weight: bold;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            border-radius: 0.25rem; /* Standard Bootstrap radius */
            color: #fff; /* Ensure white text for all badges */
        }
        
        /* Role Styles (Manager -> Danger Badge) */
        .role-manager {
            background-color: #dc3545; /* Bootstrap danger red */
        }
        
        /* Role Styles (Other -> Primary Badge) */
        .role-other {
            background-color: #0d6efd; /* Bootstrap primary blue */
        }
        /* --- END: Bootstrap Badge Emulation for PDF --- */
    </style>
</head>
<body>
    <h2>Loan Application Records Report</h2>
    <div class="report-info">
        <strong>Generated:</strong> ' . date('Y-m-d H:i:s') . '<br>
        <strong>Total Records:</strong> ' . count($rows) . '
        ' . (count($filterSummary) > 0 ? '
            <p><strong>Filters Applied:</strong></p>
            <ul class="filter-list">' . implode('', array_map(function($f) { return '<li>' . $f . '</li>'; }, $filterSummary)) . '</ul>' 
            : '<p>No Filters Applied (Showing all data).</p>') . '
    </div>
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
        <tbody>';

// --- LOOP THROUGH DATA AND BUILD ROWS ---
foreach ($rows as $row) {
    // Safely retrieve prediction info using the constant array
    // PERFORMANCE - Cast to int only once
    $predictionInt = (int)$row['prediction'];
    $predictionData = RISK_MAPPING[$predictionInt] ?? ['text' => 'Unknown', 'class' => 'default'];
    
    // Determine the role class (example.. role-manager, role-other)
    $roleClass = $row['role'] === 'Manager' ? 'role-manager' : 'role-other';
    
    // Format Assessor Text, now applying the base badge-pdf class
    $assessorHtml = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . 
                    ' (<span class="badge-pdf ' . $roleClass . '">' . htmlspecialchars($row['role']) . '</span>)';

    $html .= '<tr>
        <td>' . htmlspecialchars($row['name']) . '</td>
        <td>$' . number_format((float)$row['income'], 0) . '</td>
        <td>' . htmlspecialchars($row['credit_score']) . '</td>
        <td>$' . number_format((float)$row['loan_amount'], 0) . '</td>
        <td class="prediction ' . $predictionData['class'] . '">' . $predictionData['text'] . '</td>
        <td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($row['submitted_at']))) . '</td>
        <td>' . $assessorHtml . '</td>
    </tr>';
}
//closing html tags
$html .= '
        </tbody>
    </table>
</body>
</html>';


// 2. DOMPDF CONFIGURATION AND RENDERING
$options = new Options();
// Enable HTML5 parsing and PHP (already enabled in original)
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true); 
// SECURITY - Keep remote content disabled
$options->set('isRemoteEnabled', false); 

$dompdf = new Dompdf($options);

// Set paper size to A4 Landscape for better table fit
$dompdf->setPaper('A4', 'landscape'); 

$dompdf->loadHtml($html);
// This is the main performance bottleneck, minimize unnecessary complexity in HTML/CSS
$dompdf->render();

// 3. STREAM THE PDF TO THE BROWSER
$filename = 'Loan_Report_' . date('Ymd_His') . '.pdf';

// Stream outputs the PDF content with the correct headers for download
$dompdf->stream($filename, ["Attachment" => true]);
exit;