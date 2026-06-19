<?php
include '../static/config.php';

if (!empty($_POST['selected_ids'])) {
    $ids = $_POST['selected_ids'];

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM loan_application_history WHERE history_id IN ($placeholders)");
    $stmt->execute($ids);
}

header("Location: ../history.php");
exit;