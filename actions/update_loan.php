<?php
session_start();
include '../static/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['history_id'])) {
    $history_id = $_POST['history_id'];
    $risk = $_POST['manual_risk_adjustment'];
    $notes = $_POST['officer_notes'];

    try {
        // We manually set updated_at = NOW() to lock in the edit time
        $sql = "UPDATE loan_application_history 
                SET manual_risk_adjustment = :risk, 
                    officer_notes = :notes, 
                    updated_at = NOW() 
                WHERE history_id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':risk'  => $risk,
            ':notes' => $notes,
            ':id'    => $history_id
        ]);

        // If coming from view-details.php, redirect back there
        if (isset($_POST['redirect_to'])) {
            header("Location: ../" . $_POST['redirect_to']);
            exit;
        }

        echo "Success";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Database Error: " . $e->getMessage();
    }
}