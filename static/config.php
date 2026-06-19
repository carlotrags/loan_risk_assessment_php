<?php
date_default_timezone_set('Asia/Manila');

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "loan_system";
$port = 3306;

// MySQLi connection
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("MySQLi connection failed: " . $conn->connect_error);
}

// Sync MySQLi Timezone
$conn->query("SET time_zone = '+08:00'");

try {
    $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    // Sync PDO Timezone
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    die("PDO connection failed: " . $e->getMessage());
}
?>