<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

if ($_SESSION['role'] !== 'Manager') {
    header("Location: index.php");
    exit;
}

include 'static/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $role       = $_POST['role'] ?? '';

    // Validation
    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($username) ||
        empty($email) ||
        empty($password) ||
        empty($role)
    ) {
        die("All fields are required.");
    }

    try {

        // Check if username already exists
        $checkUsername = $pdo->prepare("
            SELECT COUNT(*) 
            FROM user_accounts 
            WHERE username = :username
        ");

        $checkUsername->execute([
            ':username' => $username
        ]);

        if ($checkUsername->fetchColumn() > 0) {
            die("Username already exists.");
        }

        // Check if email already exists
        $checkEmail = $pdo->prepare("
            SELECT COUNT(*) 
            FROM user_accounts 
            WHERE email = :email
        ");

        $checkEmail->execute([
            ':email' => $email
        ]);

        if ($checkEmail->fetchColumn() > 0) {
            die("Email already exists.");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert account
        $stmt = $pdo->prepare("
            INSERT INTO user_accounts
            (
                first_name,
                last_name,
                username,
                email,
                password,
                role,
                creation_date
            )
            VALUES
            (
                :first_name,
                :last_name,
                :username,
                :email,
                :password,
                :role,
                NOW()
            )
        ");

        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name'  => $last_name,
            ':username'   => $username,
            ':email'      => $email,
            ':password'   => $hashed_password,
            ':role'       => $role
        ]);

        header("Location: user-accounts.php?success=1");
        exit;

    } catch (PDOException $e) {
        die("Error adding account: " . $e->getMessage());
    }
}
?>