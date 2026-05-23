<?php
session_start();
include 'static/config.php';

// ✅ Allow Manager + System Administrator only
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Manager', 'System Administrator'])) {
    header("Location: index.php");
    exit;
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['current_status'])) {

    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];

    // 🔥 SECURITY: Check if target user is System Administrator
    $check = $pdo->prepare("SELECT role FROM user_accounts WHERE user_id = :id");
    $check->execute([':id' => $user_id]);
    $user = $check->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['role'] === 'System Admin') {
        die("System Administrator account cannot be modified.");
    }

    // Toggle status
    $new_status = ($current_status === 'Deactivated') ? 'Active' : 'Deactivated';

    try {
        $stmt = $pdo->prepare("
            UPDATE user_accounts 
            SET status = :new_status 
            WHERE user_id = :user_id
        ");

        $stmt->execute([
            ':new_status' => $new_status,
            ':user_id' => $user_id
        ]);

        echo "<script>
                alert('User status is now $new_status.');
                window.location.href='user-accounts.php';
            </script>";

    } catch (PDOException $e) {
        echo "Error updating status: " . $e->getMessage();
        exit;
    }

} else {
    header("Location: user-accounts.php");
    exit;
}
?>