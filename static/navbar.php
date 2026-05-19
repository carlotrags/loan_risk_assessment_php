<?php
$role = $_SESSION['role'] ?? '';
?>
<div class="nav-container flex items-center justify-between bg-indigo-700 py-3 border-t-8 border-blue-900">
    
    <button class="burger-menu-toggle" id="burgerToggle" aria-label="Toggle navigation">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class='logo text-indigo-100 font-bold text-xl'>
        <a href="index.php" style="color: inherit; text-decoration: none;">Loan Risk Assessment</a>
    </div>

    <ul class="main-navbar mx-auto flex items-center gap-5 list-none m-0 p-0" id="mainNavbar">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Home</a></li>

        <li class="assessment-dropdown">
            <a href="assessment.php"><i class="fa-solid fa-clipboard-check"></i>Assessment <i class="fa-solid fa-caret-down"></i></a>
            <div class="assessment-dropdown-menu">
                <a href="business-form.php"><i class="fa-solid fa-briefcase"></i>Business Loan</a>
                <a href="home-form.php"><i class="fa-solid fa-house"></i>Home Loan</a>
            </div>
        </li>

        <li class="history-dropdown">
            <a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i>History <i class="fa-solid fa-caret-down"></i></a>
            <div class="history-dropdown-menu">
                <a href="business-history.php"><i class="fa-solid fa-briefcase"></i>Business Loan</a>
                <a href="home-history.php"><i class="fa-solid fa-house"></i>Home Loan</a>
            </div>
        </li>

        <?php if (in_array($role, ['Manager', 'System Admin'])) : ?>
            <li><a href="user-accounts.php"><i class="fa-solid fa-users"></i>User Accounts</a></li>
        <?php endif; ?>
    </ul>

    <div class="user-menu">
        <button class="user-btn bg-green-600 text-white font-bold px-3 py-2 rounded-md flex items-center gap-2" id="user-dropdown-btn">
            <i class="fa-solid fa-user"></i><?= htmlspecialchars($_SESSION['username']) ?><i class="fa-solid fa-caret-down"></i>
        </button>
        <div class="user-dropdown" id="user-dropdown">
            <a href="account.php" class="user-account"><i class="fa-solid fa-user"></i>Account</a>
            <a href="account-settings.php" class="user-account"><i class="fa-solid fa-gear"></i>Account Settings</a>
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownBtn = document.getElementById('user-dropdown-btn');
        const dropdownMenu = document.getElementById('user-dropdown');
        const burgerToggle = document.getElementById('burgerToggle');
        const mainNavbar = document.getElementById('mainNavbar');

        // Para sa Green User Dropdown Button
        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
        }

        // Para sa Burger Menu Button sa Mobile View
        if (burgerToggle && mainNavbar) {
            burgerToggle.addEventListener('click', function (event) {
                event.stopPropagation(); // Pinipigilan nito na tamaan ang document click logic sa ibaba
                mainNavbar.classList.toggle('active');
                
                const icon = burgerToggle.querySelector('i');
                if (mainNavbar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            });
        }

        // Pangkalahatang click handler para isara ang mga menu kapag nag-click sa labas
        document.addEventListener('click', function (event) {
            // Isasara ang user dropdown kapag nag-click sa labas nito
            if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            }
            
            // Isasara ang mobile navigation drawer kapag nag-click sa ibang parte ng screen
            if (mainNavbar && mainNavbar.classList.contains('active')) {
                if (!burgerToggle.contains(event.target) && !mainNavbar.contains(event.target)) {
                    mainNavbar.classList.remove('active');
                    const icon = burgerToggle.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            }
        });
    });
</script>