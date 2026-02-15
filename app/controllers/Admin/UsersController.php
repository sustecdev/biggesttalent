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
            'title' => 'Users Management',
            'users' => $users
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

        $uid = $_POST['uid'] ?? 0;
        $role = $_POST['role'] ?? '';

        if (!$uid || !$role) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        if ($uid == $_SESSION['uid'] && $role !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Cannot remove your own admin role']);
            return;
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

        $uid = $_POST['uid'] ?? 0;

        if (!$uid) {
            echo json_encode(['success' => false, 'message' => 'Missing UID']);
            return;
        }

        if ($uid == $_SESSION['uid']) {
            echo json_encode(['success' => false, 'message' => 'Cannot ban yourself']);
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

        $uid = $_POST['id'] ?? 0;

        if (!$uid) {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
            return;
        }

        if ($uid == $_SESSION['uid']) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
            return;
        }

        if ($this->userModel->delete($uid)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
}
