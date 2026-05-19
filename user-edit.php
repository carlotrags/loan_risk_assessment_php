<?php
session_start();
include 'static/config.php';

if (!isset($_GET['user_id'])) {
    header("Location: user-accounts.php");
    exit;
}

$user_id = $_GET['user_id'];

$stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

if ($user && $user['role'] === 'System Admin') {
    echo "<script>
        alert('System Administrator account cannot be edited.');
        window.location.href = 'user-accounts.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/useraccountsstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <title>Edit Account</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="add-user-container">
            <h1>Edit Account</h1>

            <form method="POST" action="user-edit-process.php" class="add-account-form">
                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
        
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>

                <p class="pass-p">Leave password blank to keep current password.</p>

                <div class="form-row">
                    <div class="form-group" style="position: relative;">
                        <label>Password:</label>
                        <input type="password" name="password" placeholder="Pass Change" id="password">
                        <i class="fa-solid fa-eye toggle-password"
                        style="position: absolute; right: 10px; top: 46px; cursor: pointer;"></i>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label>Confirm Password:</label>
                        <input type="password" name="confirm_password" placeholder="Pass Confirm" id="confirm_password">
                        <i class="fa-solid fa-eye toggle-password"
                        style="position: absolute; right: 10px; top: 46px; cursor: pointer;"></i>
                    </div>
                </div>


                <div class="form-row">
                    <div class="form-group">
                        <label>Role:</label>
                        <select name="role" required>
                            <option value="Manager" <?= $user['role']=='Manager'?'selected':'' ?>>Manager</option>
                            <option value="Loan Officer" <?= $user['role']=='Loan Officer'?'selected':'' ?>>Loan Officer</option>
                        </select>
                    </div>
                </div>


                <div class="form-submit-button">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i> Update Account
                    </button>
                </div>
            </form>
        </div>
    </section>


    <script src="static/form.js" defer></script>


    <!-- Password Show and Confirmation -->
    <script>
    const togglePassword = document.querySelector('.toggle-password');
    const passwordField = document.getElementById('password');


    const toggleConfirm = document.querySelector('.toggle-confirm-password');
    const confirmField = document.getElementById('confirm_password');


        togglePassword.addEventListener('click', () => {
            if(passwordField.type === 'password'){
                passwordField.type = 'text';
                togglePassword.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                togglePassword.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });


        toggleConfirm.addEventListener('click', () => {
            if(confirmField.type === 'password'){
                confirmField.type = 'text';
                toggleConfirm.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                confirmField.type = 'password';
                toggleConfirm.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });


        const form = document.querySelector('.add-account-form');
        form.addEventListener('submit', (e) => {
            if(passwordField.value !== confirmField.value){
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>


    <?php include "static/footer.php" ?>
</body>
</html>

