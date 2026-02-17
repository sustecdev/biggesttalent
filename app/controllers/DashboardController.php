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

    private function ensureAdmin(): void
    {
        if (!function_exists('isAdmin') || !isAdmin()) {
            $makeAdminUrl = str_replace('/public', '', URLROOT) . '/make-admin.php';
            header('Location: ' . $makeAdminUrl);
            exit;
        }
    }
}
