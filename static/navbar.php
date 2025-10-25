<?php
$role = $_SESSION['role'] ?? '';
?>
<div class="nav-container">
    <h1>Loan Risk Assessment System</h1>
    <ul class="navbar">
        <li><a href="index.php">Home</a></li>
        <li><a href="form.php">Assessment</a></li>
        <li><a href="#">History</a></li>
        <li><a href="#">Account</a></li>
        <?php if ($role === 'Manager') : ?>
            <li><a href="settings.php">Settings</a></li>
        <?php endif; ?>
    </ul>
    <a href="logout.php">Logout</a>
</div>