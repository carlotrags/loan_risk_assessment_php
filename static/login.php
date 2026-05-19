<?php
session_start();

// 1. Strict Cache Control
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// 2. Updated Logic: If logged in, tell the browser to "skip" this page in history
if (isset($_SESSION['user_id'])) {
    echo "<script>
        if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
            // If they hit back to get here, push them back one more step to exit the site
            history.back();
        } else {
            // Otherwise, just send them to the dashboard and replace this page in history
            window.location.replace('../index.php');
        }
    </script>";
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/loginstyle.css?v=<?= filemtime('css/loginstyle.css') ?>">
    <link rel="icon" type="image/x-icon" href="images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Login</title>
</head>
<body>
    <header class="login-header">
        <div class="logo">
            <img src="images/transfers.png" alt="Bank Logo" class="bank-logo">
        </div>
    </header>

    <div class="login-container">
        <h1>Login</h1>
        <?php if ($error): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <label>Username</label>
            <input type="text" id="username" name="username" placeholder="Input username">

            <label class="password-label">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Input password">
                <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <button type="submit">Sign in</button>
        </form>
    </div>
<script src="static/login.js?v=<?= time() ?>"></script>
</body>
</html>