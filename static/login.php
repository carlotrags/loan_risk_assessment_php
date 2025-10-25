<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="loginstyle.css">
    <title>Login</title>
</head>
<body>
    <h3 class="bank-name">Bank Name</h3>
    <div class="login-container">
        <h1>Login</h1>
        <?php if ($error): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <label>Username</label>
            <input type="text" id="username" name="username" required>

            <label>Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Sign in</button>
        </form>
    </div>
</body>
</html>