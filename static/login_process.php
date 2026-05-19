<?php
session_start();
include 'config.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Please enter both username and password.";
    header("Location: login.php");
    exit;
}

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {

    // 🚫 BLOCK DEACTIVATED USERS FIRST
    if ($user['status'] === 'Deactivated') {
        $_SESSION['login_error'] = "Your account is deactivated. Please contact the administrator.";
        header("Location: login.php");
        exit;
    }

    // Password check
    if (
        password_verify($password, $user['password']) ||
        $password === $user['password']
    ) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php");
        exit;

    } else {
        $_SESSION['login_error'] = "Incorrect password.";
        header("Location: login.php");
        exit;
    }

} else {
    $_SESSION['login_error'] = "Username not found.";
    header("Location: login.php");
    exit;
}
?>