<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: static/login.php");
    exit;
}


require 'static/config.php';


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// After fetching $user from DB
$status = strtolower($user['status']);
$badge_class = $status === 'active' ? 'bg-success' : 'bg-danger';
$status_text = ucfirst($status);
$status_message = $status === 'active'
    ? 'Your account is active and in good standing'
    : 'Your account has been deactivated';


// Get total assessments done by this user
$stmt = $pdo->prepare("SELECT COUNT(*) as total_assessments FROM loan_application_history WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);


// after $status, $badge_class
$icon_class = $status === 'active' ? 'text-primary' : 'text-secondary';
$icon_bg_class = $status === 'active' ? 'bg-primary bg-opacity-10' : 'bg-danger bg-opacity-10';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Profile - Loan Risk Assessment</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="static/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="static/css/navbarstyle.css?v=<?= time() ?>">
    <link rel="icon" type="image/x-icon" href="static/images/LRA_Favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <section class="header-navbar">
        <?php include "static/navbar.php"?>
    </section>




    <!-- Account content styles below keep changes local to this page -->
    <style>
        /*Overrides to avoid touching global CSS files so don't touch thisss*/
        .account-page .container { max-width: 980px; margin: 3rem auto !important; }
        .account-page .card { padding: .5rem; max-width: none; }
        .account-page .card .card-body { padding: 1rem; }
        .account-page .profile-avatar { font-size: 4rem; width: 96px; height: 96px; display:flex; align-items:center; justify-content:center; border-radius:50%; }
        .account-page .profile-role { font-size: .9rem; color: #6c757d; }
        .account-page .detail-term { color: #6c757d; }




        .account-page .form-control-plaintext { width: 100%; }




        /* Allow the center flex column to shrink so it doesn't push the right-side buttons out
           and enable truncation of long text like email/username when space is tight */
        .account-page .d-flex > .flex-grow-1 { min-width: 0; }
        .account-page .profile-role { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }




        @media (max-width: 767px) {
            .account-page .profile-avatar { font-size:3rem; width:72px; height:72px; }
            .account-page .profile-role { white-space: normal; }
        }
    </style>




    <main class="account-page">
        <div class="container account-container py-5">
            <div class="row justify-content-center">
            <!-- Profile Card -->
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img src="https://ui-avatars.com/api/?name=<?= rawurlencode($user['first_name'] . ' ' . $user['last_name']) ?>&size=256&background=0D6EFD&color=ffffff"
                                    class="rounded-circle profile-avatar" width="96" height="96" alt="profile">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-0 me-2"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                                    <span class="badge <?= $badge_class ?> text-white"><?= $status_text ?></span>
                                </div>
                                <div class="profile-role">@<?= htmlspecialchars($user['username']) ?> · <?= htmlspecialchars($user['email']) ?></div>
                            </div>
                            <div class="text-end ms-3 flex-shrink-0 d-flex align-items-center gap-2">
                                <a href="form.php" class="btn btn-primary btn-sm">New Assessment</a>
                                <a href="history.php" class="btn btn-outline-secondary btn-sm">History</a>
                            </div>
                        </div>




                        <hr class="my-3">




                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <div class="detail-term small">Role</div>
                                <div><?= htmlspecialchars($user['role']) ?></div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="detail-term small">Total Assessments</div>
                                <div><span class="badge bg-success"><?= htmlspecialchars($stats['total_assessments']) ?></span></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="detail-term small">Joined</div>
                                <div><?= date('F j, Y', strtotime($user['creation_date'])) ?></div>
                            </div>
                        </div>




                        <hr class="my-3">




                        <div>
                            <h6 class="mb-2">Account Details</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="small detail-term">First name</div>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['first_name']) ?></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="small detail-term">Last name</div>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['last_name']) ?></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="small detail-term">Username</div>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['username']) ?></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="small detail-term">Email</div>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['email']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




            <!-- Account Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-person-vcard text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0">Account Details</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted small mb-1">First Name</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['first_name']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted small mb-1">Last Name</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['last_name']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted small mb-1">Username</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['username']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted small mb-1">Email Address</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($user['email']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted small mb-1">Role</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge bg-primary-subtle text-primary px-2">
                                            <?= htmlspecialchars($user['role']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted small mb-1">Join Date</label>
                                    <div class="form-control-plaintext">
                                        <i class="bi bi-calendar-event text-muted me-1"></i>
                                        <?= date('F j, Y', strtotime($user['creation_date'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <!-- Account Activity -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-activity text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0">Account Activity</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-graph-up text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Total Assessments Completed</h6>
                                <p class="text-muted small mb-0">You have completed <?= htmlspecialchars($stats['total_assessments']) ?> loan risk assessments</p>
                            </div>
                        </div>
                   
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="<?= $icon_bg_class ?> p-2 rounded me-3">
                                <i class="bi bi-shield-check <?= $icon_class ?>"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Account Status</h6>
                                <p class="text-muted small mb-0"><?= $status_message ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <?php include "static/footer.php"?>
</body>
</html>

