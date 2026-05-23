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

$userStmt = $pdo->query("SELECT DISTINCT CONCAT(first_name,' ',last_name) AS full_name FROM user_accounts ORDER BY full_name ASC");
$allUsers = $userStmt->fetchAll(PDO::FETCH_COLUMN);

$conditions = [];
$params = [];

// NAME FILTER
if (!empty($_GET['searchName'])) {
    $conditions[] = "CONCAT(first_name,' ',last_name) LIKE :name";
    $params[':name'] = "%" . $_GET['searchName'] . "%";
}

// ROLE FILTER
if (!empty($_GET['role'])) {
    $conditions[] = "role = :role";
    $params[':role'] = $_GET['role'];
}

// STATUS FILTER
if (!empty($_GET['status'])) {
    $conditions[] = "status = :status";
    $params[':status'] = $_GET['status'];
}

$query = "
SELECT
    user_id,
    CONCAT(first_name, ' ', last_name) AS full_name,
    email,
    username,
    creation_date,
    role,
    status
FROM user_accounts
";

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Sort Order
$sort = $_GET['sort'] ?? '';
switch ($sort) {
    case 'name_asc':
        $query .= " ORDER BY CONCAT(first_name, ' ', last_name) ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY CONCAT(first_name, ' ', last_name) DESC";
        break;
    case 'date_asc':
        $query .= " ORDER BY creation_date ASC";
        break;
    case 'date_desc':
        $query .= " ORDER BY creation_date DESC";
        break;
    default:
        $query .= " ORDER BY user_id DESC";
}

$userStmt = $pdo->prepare($query);
$userStmt->execute($params);
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
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

    <title>User Accounts</title>
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>

    <section class="container">
        <div class="user-container">
            <h1>User Accounts</h1>
            <p>Manage Loan Officer accounts here.</p>
            <!-- FILTER SECTION -->
            <form method="GET" class="accounts-filter">
            <input type="text" name="searchName" placeholder="Search Name">
            <select name="role">
                <option value="">All Roles</option>
                <option value="Manager">Manager</option>
                <option value="Loan Officer">Loan Officer</option>
                <option value="System Admin">System Admin</option>
            </select>

            <select name="status">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Deactivated">Deactivated</option>
                <option value="N/A">N/A</option>
            </select>

            <select name="sort">
                <option value="">Sort By</option>
                <option value="name_asc">Name (A → Z)</option>
                <option value="name_desc">Name (Z → A)</option>
                <option value="date_asc">Creation Date (Old → New)</option>
                <option value="date_desc">Creation Date (New → Old)</option>
            </select>

            <button type="submit" class="filter-button">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>

            <a href="user-creation.php"><button type="button" class="new-account-button">
                <i class="fa-solid fa-user-plus"></i> New Account
            </button></a>
            </form>

            <!-- USER LIST -->
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Creation Date</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $row): ?>
                <?php $is_sysadmin = ($row['role'] === 'System Admin'); ?>
                <tr>
                <td>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['full_name']) ?>&size=24&background=007bff&color=fff"
                        class="rounded-circle me-2" width="24" height="24" alt="profile">
                    <?= htmlspecialchars($row['full_name']) ?>
                </td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['creation_date']) ?></td>
                    <td>
                        <?php
                        $roleClass = '';

                        switch ($row['role']) {
                            case 'Manager':
                                $roleClass = 'text-primary-emphasis bg-primary-subtle border border-primary-subtle';
                                break;

                            case 'Loan Officer':
                                $roleClass = 'text-success-emphasis bg-success-subtle border border-success-subtle';
                                break;

                            case 'System Admin':
                                $roleClass = 'text-light bg-purple border border-purple';
                                break;

                            default:
                                $roleClass = 'text-secondary bg-light border border-secondary';
                                break;
                        }
                        ?>

                        <span class="badge p-1 px-2 <?= $roleClass ?> rounded-pill">
                            <?= htmlspecialchars($row['role']) ?>
                        </span>
                    </td>
                    <td>
                        <?php
                            $statusClass = '';
                            switch ($row['status']) {
                                case 'Active':
                                    $statusClass = 'text-success-emphasis bg-success-subtle border border-success-subtle';
                                    break;
                                case 'Deactivated':
                                default:
                                    $statusClass = 'text-danger-emphasis bg-danger-subtle border border-danger-subtle';
                                    break;
                            }
                        ?>
                        <span class="badge p-1 px-2 <?= $statusClass ?> rounded-pill">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>

                    <td class="actions-buttons">

                        <!-- EDIT -->
                        <a href="user-edit.php?user_id=<?= $row['user_id'] ?>" 
                        class="<?= $is_sysadmin ? 'disabled-action' : '' ?>"
                        title="Edit">
                            <button style="color: <?= $is_sysadmin ? '#aaa' : 'green' ?>;" 
                                    <?= $is_sysadmin ? 'disabled' : '' ?>>
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </a>
                        <!-- ACTIVATE / DEACTIVATE -->
                        <form method="POST" action="user-activate-deactivate.php" style="display:inline;"
                        onsubmit="return confirm('Are you sure?');">

                            <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                            <input type="hidden" name="current_status" value="<?= $row['status'] ?>">

                            <button type="submit"
                                    style="color: <?= $is_sysadmin ? '#aaa' : ($row['status'] === 'Deactivated' ? 'green' : 'red') ?>;"
                                    <?= $is_sysadmin ? 'disabled' : '' ?>
                                    title="<?= $is_sysadmin ? 'System Admin cannot be modified' : ($row['status'] === 'Deactivated' ? 'Activate' : 'Deactivate') ?>">
                                <i class="fa-solid fa-user-<?= $row['status'] === 'Deactivated' ? 'check' : 'slash' ?>"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </section>
    <script src="static/form.js" defer></script>
    <?php include "static/footer.php"?>
</body>
</html>

