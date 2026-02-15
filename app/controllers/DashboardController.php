<?php

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Init Model
        $this->dashboardModel = $this->model('Dashboard');

        // Ensure legacy functions are loaded (for YemChain mainly)
        // Legacy functions are loaded via index.php dependencies or helpers
        // ensureAdmin handles logic.
    }

    public function index()
    {
        // Require authentication
        if (!function_exists('isAuthenticated') || !isAuthenticated()) {
            $_SESSION['redirect_after_login'] = 'dashboard';
            header('Location: ' . URLROOT . '/safezone');
            exit;
        }

        // Check Admin Role
        $this->ensureAdmin();

        // Get Stats from Model
        $stats = $this->dashboardModel->getStats();

        // Get YemChain balance
        $balanceData = [];
        if (function_exists('yemchain_get_user_balance')) {
            $balanceData = yemchain_get_user_balance('DBV');
        }

        $data = [
            'stats' => $stats,
            'balanceData' => $balanceData,
            'userIP' => $_SERVER['REMOTE_ADDR'],
            'userID' => $_SESSION['uid'] ?? 0,
            'userRole' => $_SESSION['role'] ?? 'user',
            'pernum' => $_SESSION['pernum'] ?? ''
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('dashboard/index', $data);
        $this->view('admin/layouts/footer');
    }

    private function ensureAdmin()
    {
        $uid = (int) ($_SESSION['uid'] ?? 0);
        $isAdmin = false;

        // Check session first
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $isAdmin = true;
        } else {
            // Check DB
            // Assuming $GLOBALS['mysqli'] is available from config.php
            if (isset($GLOBALS['mysqli'])) {
                $roleQuery = "SELECT role FROM pi_account WHERE uid = $uid";
                $roleResult = $GLOBALS['mysqli']->query($roleQuery);
                if ($roleResult && $roleResult->num_rows > 0) {
                    $row = $roleResult->fetch_assoc();
                    if (($row['role'] ?? '') === 'admin') {
                        $_SESSION['role'] = 'admin';
                        $isAdmin = true;
                    }
                }
            }
        }

        if (!$isAdmin) {
            // Redirect to make-admin page if not admin
            // Use absolute path or relative to URLROOT depending on where make-admin.php is
            // It is in project root, so likely ../make-admin.php relative to public/index.php
            // But via URL it should be URLROOT/../make-admin.php or just make-admin.php if routed?
            // Since make-admin.php is in root and NOT routed by MVC, we need absolute path.
            // However, Config::URLROOT points to public.
            // Let's assume URLROOT/../make-admin.php works if served from root, but URLROOT includes /public
            // So we need to go up one level.
            $makeAdminUrl = str_replace('/public', '', URLROOT) . '/make-admin.php';
            header('Location: ' . $makeAdminUrl);
            exit;
        }
    }
}
