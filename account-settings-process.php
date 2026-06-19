<?php
session_start();
include 'static/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check passwords
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    try {
        // Only update username and password
        $sql = "UPDATE user_accounts SET username = :username";
        $params = [':username' => $username, ':user_id' => $user_id];

        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $_SESSION['username'] = $username;

        echo "<script>alert('Account updated successfully!'); window.location.href='account.php';</script>";

    } catch (PDOException $e) {
        echo "Error updating account: " . $e->getMessage();
        exit;
    }
} else {
    header("Location: account-settings.php");
    exit;
}
?>