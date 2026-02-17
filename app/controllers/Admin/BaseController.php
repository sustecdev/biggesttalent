<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

/**
 * Base Controller for Admin Panel
 * All admin controllers should extend this class
 */
abstract class BaseController extends Controller
{
    public function __construct()
    {
        // Ensure legacy functions are available
        // Legacy functions are loaded in index.php
        
        // Ensure legacy functions have access to database connection
        if (!isset($GLOBALS['mysqli'])) {
            $GLOBALS['mysqli'] = \App\Core\Database::getInstance()->getConnection();
        }
        
        // Check admin access on every request
        $this->ensureAdmin();
    }

    /**
     * Ensure user is authenticated and has admin or super_admin role
     */
    protected function ensureAdmin(): void
    {
        if (function_exists('requireAdmin')) {
            requireAdmin();
        } else {
            $role = $_SESSION['role'] ?? '';
            if (!isset($_SESSION['uid']) || !in_array($role, ['admin', 'super_admin'], true)) {
                header('Location: ' . URLROOT);
                exit;
            }
        }
    }

    /**
     * Check if current user is super admin
     */
    protected function isSuperAdmin(): bool
    {
        return ($_SESSION['role'] ?? '') === 'super_admin';
    }

    /**
     * Render admin view with header and footer
     */
    protected function renderAdmin($viewPath, $data = [])
    {
        $this->view('admin/layouts/header', $data);
        $this->view($viewPath, $data);
        $this->view('admin/layouts/footer');
    }
}
