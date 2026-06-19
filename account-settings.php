<?php
session_start();
include 'static/config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}


$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    echo "User not found.";
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
    <title>Account Settings</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"; ?>
    </section>


    <section class="container">
        <div class="add-user-container">
            <h1>Account Settings</h1>
            <form method="POST" action="account-settings-process.php" class="add-account-form">
                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">


                <div class="form-row">
                    <div class="form-group">
                        <label>First Name:</label>
                        <p class="account-non-edit-fields"><?= htmlspecialchars($user['first_name']) ?></p>
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <p class="account-non-edit-fields"><?= htmlspecialchars($user['last_name']) ?></p>
                    </div>
                </div>


                <div class="form-row">
                    <div class="form-group">
                        <label>Email:</label>
                        <p class="account-non-edit-fields"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>


                <div class="form-row">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                </div>


                <p class="pass-p">Leave password blank to keep current password.</p>


                <div class="form-row">
                    <div class="form-group" style="position: relative;">
                        <label>New Password:</label>
                        <input type="password" name="password" placeholder="New Password" id="password">
                        <i class="fa-solid fa-eye toggle-password"
                            style="position: absolute; right: 10px; top: 46px; cursor: pointer;"></i>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label>Confirm Password:</label>
                        <input type="password" name="confirm_password" placeholder="Confirm new password" id="confirm_password">
                        <i class="fa-solid fa-eye toggle-confirm-password"
                            style="position: absolute; right: 10px; top: 46px; cursor: pointer;"></i>
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
    <script>
        const togglePassword = document.querySelector('.toggle-password');
        const passwordField = document.getElementById('password');


        const toggleConfirm = document.querySelector('.toggle-confirm-password');
        const confirmField = document.getElementById('confirm_password');


        togglePassword.addEventListener('click', () => {
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
            togglePassword.classList.toggle('fa-eye-slash');
            togglePassword.classList.toggle('fa-eye');
        });


        toggleConfirm.addEventListener('click', () => {
            confirmField.type = confirmField.type === 'password' ? 'text' : 'password';
            toggleConfirm.classList.toggle('fa-eye-slash');
            toggleConfirm.classList.toggle('fa-eye');
        });


        const form = document.querySelector('.add-account-form');
        form.addEventListener('submit', (e) => {
            if(passwordField.value !== confirmField.value){
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>


    <?php include "static/footer.php"; ?>
</body>
</html>

