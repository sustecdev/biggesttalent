<?php

namespace App\Controllers\Admin;

class UsersController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $users = $this->userModel->getAll(500);

        $data = [
            'title' => 'Users & Roles',
            'users' => $users,
            'is_super_admin' => $this->isSuperAdmin()
        ];

        $this->renderAdmin('admin/users/index', $data);
    }

    public function updateRole()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $uid = (int) ($_POST['uid'] ?? 0);
        $role = trim($_POST['role'] ?? '');
        $currentRole = $_SESSION['role'] ?? 'user';
        $isSuperAdmin = $currentRole === 'super_admin';

        if (!$uid || !$role) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        $allowedRoles = ['user', 'banned'];
        if ($isSuperAdmin) {
            $allowedRoles = ['user', 'admin', 'super_admin', 'banned'];
        }

        if (!in_array($role, $allowedRoles, true)) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to set this role. Only Super Admin can add admins.']);
            return;
        }

        if ($uid == $_SESSION['uid']) {
            if ($role !== 'super_admin' && $role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Cannot remove your own admin role']);
                return;
            }
        }

        if ($this->userModel->updateRole($uid, $role)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function ban()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $uid = (int) ($_POST['uid'] ?? 0);
        $isSuperAdmin = $this->isSuperAdmin();

        if (!$uid) {
            echo json_encode(['success' => false, 'message' => 'Missing UID']);
            return;
        }

        if ($uid == $_SESSION['uid']) {
            echo json_encode(['success' => false, 'message' => 'Cannot ban yourself']);
            return;
        }

        $targetUser = $this->userModel->findById($uid);
        if ($targetUser && in_array($targetUser['role'] ?? '', ['admin', 'super_admin'], true) && !$isSuperAdmin) {
            echo json_encode(['success' => false, 'message' => 'Only Super Admin can ban admins']);
            return;
        }

        if ($this->userModel->ban($uid)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function delete()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $uid = (int) ($_POST['id'] ?? 0);
        $isSuperAdmin = $this->isSuperAdmin();

        if (!$uid) {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
            return;
        }

        if ($uid == $_SESSION['uid']) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
            return;
        }

        $targetUser = $this->userModel->findById($uid);
        if ($targetUser && in_array($targetUser['role'] ?? '', ['admin', 'super_admin'], true) && !$isSuperAdmin) {
            echo json_encode(['success' => false, 'message' => 'Only Super Admin can delete admins']);
            return;
        }

        if ($this->userModel->delete($uid)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * Pre-add admin by SafeZone username (pernum). Works before the user signs in.
     * Super Admin only.
     */
    public function addByUsername()
    {
        header('Content-Type: application/json');

        if (!$this->isSuperAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Only Super Admin can add admins by username']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $username = trim($_POST['username'] ?? '');
        if ($username === '') {
            echo json_encode(['success' => false, 'message' => 'Enter a SafeZone username (pernum)']);
            return;
        }

        if (!function_exists('addAdminByUsername')) {
            echo json_encode(['success' => false, 'message' => 'addAdminByUsername not available']);
            return;
        }

        if (addAdminByUsername($username)) {
            echo json_encode(['success' => true, 'message' => "'$username' added as admin. They will have access on their first login."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add admin']);
        }
    }
}
