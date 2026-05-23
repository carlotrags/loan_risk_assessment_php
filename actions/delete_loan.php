<?php
session_start();
include '../static/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // 1. Query the log row first to fetch relational application information
        $queryStmt = $pdo->prepare("SELECT application_id, loan_type FROM loan_application_history WHERE history_id = :id");
        $queryStmt->execute([':id' => $id]);
        $record = $queryStmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            $app_id = $record['application_id'];
            $loan_type = strtolower($record['loan_type']);

            // 2. Identify the matching data table mapping target
            $target_table = '';
            $id_column = '';
            
            if ($loan_type === 'business') {
                $target_table = 'business_loan_applications';
                $id_column = 'business_application_id';
            } elseif ($loan_type === 'home') {
                $target_table = 'home_loan_applications';
                $id_column = 'home_application_id';
            } elseif ($loan_type === 'personal') {
                $target_table = 'personal_loan_applications';
                $id_column = 'application_id';
            }

            // 3. Purge the matching raw input parameters from the specific loan data table
            if (!empty($target_table) && !empty($id_column)) {
                $deleteAppStmt = $pdo->prepare("DELETE FROM $target_table WHERE $id_column = :app_id");
                $deleteAppStmt->execute([':app_id' => $app_id]);
            }
        }

        // 4. Delete the central audit log index tracking row
        $stmt = $pdo->prepare("DELETE FROM loan_application_history WHERE history_id = :id");
        $stmt->execute([':id' => $id]);

        // 5. Direct the browser back to whichever workspace tab referred the client execution action
        $referer = $_SERVER['HTTP_REFERER'] ?? '../history.php';
        header("Location: " . $referer);
        exit;

    } catch (PDOException $e) {
        die("Error executing synchronized deletion: " . $e->getMessage());
    }
}