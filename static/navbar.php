<?php
$role = $_SESSION['role'] ?? '';
?>
<div class="nav-container flex items-center justify-between bg-indigo-700 px-10 py-3 border-t-8 border-blue-900">
    <div class='logo text-indigo-100 font-bold text-xl'>Loan Risk Assessment</div>
    <ul class="main-navbar mx-auto flex items-center gap-5 list-none m-0 p-0">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Home</a></li>

        <li class="assessment-dropdown">
            <a href="assessment.php"><i class="fa-solid fa-clipboard-check"></i>Assessment <i class="fa-solid fa-caret-down"></i></a>
            <div class="assessment-dropdown-menu">
                <a href="form.php"><i class="fa-solid fa-user"></i>Personal Loan</a>
                <a href="business-form.php"><i class="fa-solid fa-briefcase"></i>Business Loan</a>
            </div>
        </li>

        <li class="history-dropdown">
            <a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i>History</a>
            <div class="history-dropdown-menu">
                <a href="personal-history.php"><i class="fa-solid fa-user"></i>Personal Loan</a>
                <a href="business-history.php"><i class="fa-solid fa-briefcase"></i>Business Loan</a>
            </div>
        </li>


        <?php if ($role === 'Manager') : ?>
        <li><a href="settings.php"><i class="fa-solid fa-gear"></i>Settings</a></li>
        <?php endif; ?>
    </ul>

    <div class="user-menu">
        <button class="user-btn bg-green-600 text-white font-bold px-3 py-2 rounded-md flex items-center gap-2" id="user-dropdown-btn">
            <i class="fa-solid fa-user"></i><?= htmlspecialchars($_SESSION['username']) ?><i class="fa-solid fa-caret-down"></i>
        </button>
            <div class="user-dropdown" id="user-dropdown">
                <a href="account.php" class="user-account"><i class="fa-solid fa-user"></i>Account</a>
                <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
            </div>
        </div>
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