<?php
session_start();
include '../static/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM loan_application_history WHERE history_id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: ../history.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        die("Error deleting record: " . $e->getMessage());
    }
}