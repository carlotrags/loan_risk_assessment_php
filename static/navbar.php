<?php
$role = $_SESSION['role'] ?? '';
?>
<div class="nav-container">
    <h1>Loan Risk Assessment System</h1>

    <ul class="main-navbar">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Home</a></li>

        <li><a href="form.php"><i class="fa-solid fa-clipboard-check"></i>Assessment</a></li>

        <li><a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i>History</a></li>

        <?php if ($role === 'Manager') : ?>
        <li><a href="settings.php"><i class="fa-solid fa-gear"></i>Settings</a></li>
        <?php endif; ?>
    </ul>

    <div class="user-menu">
        <button class="user-btn" id="user-dropdown-btn">
            <i class="fa-solid fa-user"></i><?= htmlspecialchars($_SESSION['username']) ?><i class="fa-solid fa-caret-down"></i>
        </button>
        <div class="user-dropdown" id="user-dropdown">
            <a href="account.php" class="user-account"><i class="fa-solid fa-user"></i>Account</a>
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        </div>
    <div>
</div>

<script>
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('user-dropdown');
        const button = document.getElementById('user-dropdown-btn');

        if (button.contains(event.target)) {
            dropdown.classList.toggle('show');
        } else {
            dropdown.classList.remove('show');
        }
    });
</script>