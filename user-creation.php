<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$role = $_SESSION['role'];

if (!in_array($_SESSION['role'], ['Manager', 'System Admin'])) {
    header("Location: index.php");
    exit;
}

include 'static/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$first_name || !$last_name || !$username || !$email || !$password || !$role) {
        die("All fields are required!");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO user_accounts
            (first_name, last_name, username, email, password, role, creation_date)
            VALUES (:first_name, :last_name, :username, :email, :password, :role, NOW())");

        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role,
        ]);

        header("Location: user-accounts.php?success=1");
        exit;

    } catch (PDOException $e) {
        die("Error adding account: " . $e->getMessage());
    }
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

    <title>Add Account</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="add-user-container">
            <h1>Create New Account</h1>

            <form method="POST" action="user_add_account_process.php" class="add-account-form">
                <!-- Row 1: First + Last Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                </div>

                <!-- Row 2: Username -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                </div>

                <!-- Row 3: Email, Password -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label>Password:</label>
                        <input type="password" name="password" placeholder="Password" required id="password">
                        <i class="fa-solid fa-eye toggle-password"
                        style="position: absolute; right: 10px; top: 46px; cursor: pointer;"></i>
                    </div>
                </div>

                <!-- Row 4: Role -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Role:</label>
                        <select name="role" required>
                            <option value="">Select Role</option>
                            <option value="Manager">Manager</option>
                            <option value="Loan Officer">Loan Officer</option>
                        </select>
                    </div>
                </div>

                <div class="form-submit-button">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-user-plus"></i> Create Account
                    </button>
                </div>
            </form>
        </div>
        </div>
    </section>
    <script src="static/form.js" defer></script>
    <script>
        const togglePassword = document.querySelector('.toggle-password');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            if(passwordField.type === 'password'){
                passwordField.type = 'text';
                togglePassword.classList.remove('fa-eye');
                togglePassword.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                togglePassword.classList.remove('fa-eye-slash');
                togglePassword.classList.add('fa-eye');
            }
        });
    </script>
    <?php include "static/footer.php"?>
</body>
</html>