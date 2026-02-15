<?php
// Admin Header with Sidebar Navigation
// Note: Auth check is handled by Controller
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Biggest Talent Africa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= URLROOT ?>/css/admin.css?v=<?= time() ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#cd217d',
                        'primary-hover': '#a51a64',
                        secondary: '#9a288d',
                        accent: '#aa843f',
                        cream: '#d5c39e',
                    }
                }
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <style>
        @media (max-width :1290px) {
            .bg {
                background-position: left !important;
            }
        }

        .card-header {
            padding: 0.50rem 1.25rem;
        }

        .blocksm {
            width: 220px;
        }

        .details_upper {
            margin-left: 0px;
        }

        .details_upper2 {
            margin-left: 0px;
        }

        /* ↓↓↓ ONLY ADDITION: Reduce logo size ↓↓↓ */
        .nav-logo-img {
            height: auto;
            max-height: 80px;
            width: auto;
            max-width: 90%;
            margin: 0 auto;
        }
    </style>

</head>

<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2 class="admin-logo" style="text-align: center;">
                    <img src="<?= URLROOT ?>/images/Official logo.png" alt="Biggest Talent Africa Logo"
                        class="nav-logo-img">
                </h2>
                <p class="admin-subtitle">Admin Panel</p>
            </div>

            <nav class="admin-nav">
                <a href="<?= URLROOT ?>/dashboard"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Dashboard</span>
                </a>

                <a href="<?= URLROOT ?>/admin/contestants"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/contestants') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Contestants</span>
                </a>

                <a href="<?= URLROOT ?>/admin/nominations"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/nominations') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Nominations</span>
                    <?php
                    // Legacy check for pending count - ideally moved to View Composer or Model
                    if (isset($data['stats']['pending_applications']) && $data['stats']['pending_applications'] > 0):
                        ?>
                        <span class="nav-badge">
                            <?= $data['stats']['pending_applications'] ?>
                        </span>
                    <?php endif; ?>
                </a>

                <a href="<?= URLROOT ?>/admin/voting"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/voting') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Voting</span>
                </a>

                <a href="<?= URLROOT ?>/admin/judges"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/judges') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Judges</span>
                </a>

                <a href="<?= URLROOT ?>/admin/seasons"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/seasons') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Seasons</span>
                </a>

                <a href="<?= URLROOT ?>/admin/users"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/users') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Users</span>
                </a>

                <a href="<?= URLROOT ?>/admin/categories"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/categories') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Categories</span>
                </a>

                <a href="<?= URLROOT ?>/admin/contests"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/contests') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Contests</span>
                </a>

                <a href="<?= URLROOT ?>/admin/reports"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/reports') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Reports</span>
                </a>

                <a href="<?= URLROOT ?>/admin/settings"
                    class="admin-nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'admin/settings') !== false) ? 'active' : '' ?>">
                    <span class="nav-text">Settings</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <a href="<?= URLROOT ?>" class="admin-nav-item">
                    <span class="nav-text">View Site</span>
                </a>
                <a href="<?= URLROOT ?>/auth/logout" class="admin-nav-item">
                    <span class="nav-text">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-topbar">
                <h1 class="admin-page-title">
                    <?= $data['title'] ?? 'Dashboard' ?>
                </h1>
                <div class="admin-user-info">
                    <span>Welcome,
                        <?= htmlspecialchars($_SESSION['fname'] ?? $_SESSION['username'] ?? 'Admin') ?>
                    </span>
                </div>
            </div>

            <div class="admin-content">