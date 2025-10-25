<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "loan_system";
$port = "3306";

$conn = new mysqli($host, $user, $pass, $db);

try {
    $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>