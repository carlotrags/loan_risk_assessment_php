<?php
$role = $_SESSION['role'] ?? '';

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
?>
<div class="nav-container">
    <h1>Loan Risk Assessment System</h1>

    <ul class="navbar">
        <li><a href="index.php">Home</a></li>
        <li><a href="form.php">Assessment</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="#">Account</a></li>
        <?php if ($role === 'Manager') : ?>
            <li><a href="settings.php">Settings</a></li>
        <?php endif; ?>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
    <p class="logged-user">Logged in as <strong><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></strong>. </p>
</div>