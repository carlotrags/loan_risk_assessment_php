<?php
session_start();
include 'static/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header("Location: index.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $user_id = $_POST['user_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }


    try {
        $sql = "UPDATE user_accounts SET
                    first_name = :first_name,
                    last_name = :last_name,
                    username = :username,
                    email = :email,
                    role = :role";


        $params = [
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':username' => $username,
            ':email' => $email,
            ':role' => $role,
            ':user_id' => $user_id
        ];


        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = :password";
            $params[':password'] = $hashed;
        }


        $sql .= " WHERE user_id = :user_id";


        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);


        echo "<script>alert('User updated successfully!'); window.location.href='user-accounts.php';</script>";


    } catch (PDOException $e) {
        echo "Error updating user: " . $e->getMessage();
        exit;
    }


} else {
    header("Location: user-accounts.php");
    exit;
}
?>

