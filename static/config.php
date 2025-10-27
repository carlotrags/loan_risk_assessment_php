<?php
$host = "127.0.0.1"; // or "localhost"
$user = "root";
$pass = "";
$db   = "loan_system";
$port = 3307; // just the port number, not host:port

// MySQLi connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check for connection errors
if ($conn->connect_error) {
    die("MySQLi connection failed: " . $conn->connect_error);
}

try {
    $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("PDO connection failed: " . $e->getMessage());
}
?>
