<?php
include '../static/config.php';

if (!empty($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
    $ids = $_POST['selected_ids'];

    try {
        foreach ($ids as $id) {
            $id = (int)$id;

            // Check if the parameter represents a central history index or a raw business app sequence id
            $checkStmt = $pdo->prepare("SELECT history_id, application_id, loan_type FROM loan_application_history WHERE history_id = :id");
            $checkStmt->execute([':id' => $id]);
            $record = $checkStmt->fetch(PDO::FETCH_ASSOC);

            // Fallback route: Check if the value matches a raw business reference entry column pattern directly
            if (!$record) {
                $checkBusiness = $pdo->prepare("SELECT history_id, application_id, loan_type FROM loan_application_history WHERE application_id = :id AND loan_type = 'Business'");
                $checkBusiness->execute([':id' => $id]);
                $record = $checkBusiness->fetch(PDO::FETCH_ASSOC);
            }

            if ($record) {
                $history_id = $record['history_id'];
                $app_id = $record['application_id'];
                $loan_type = strtolower($record['loan_type']);

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

                // Delete the specific metric entry records
                if (!empty($target_table) && !empty($id_column)) {
                    $delApp = $pdo->prepare("DELETE FROM $target_table WHERE $id_column = :app_id");
                    $delApp->execute([':app_id' => $app_id]);
                }

                // Delete the core reference logging items
                $delHist = $pdo->prepare("DELETE FROM loan_application_history WHERE history_id = :hid");
                $delHist->execute([':hid' => $history_id]);
            }
        }
    } catch (PDOException $e) {
        die("Bulk deletion processing error: " . $e->getMessage());
    }
}

// Route back to the originating reference workspace window automatically
$referer = $_SERVER['HTTP_REFERER'] ?? '../history.php';
header("Location: " . $referer);
exit;