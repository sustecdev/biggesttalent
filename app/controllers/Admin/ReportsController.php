<?php

namespace App\Controllers\Admin;

class ReportsController extends BaseController
{
    private $reportModel;

    public function __construct()
    {
        parent::__construct();
        $this->reportModel = $this->model('Report');
    }

    public function index()
    {
        $status = $_GET['status'] ?? '';
        $reports = $this->reportModel->getAll($status);

        $data = [
            'title' => 'Reports Management',
            'reports' => $reports,
            'current_status' => $status
        ];

        $this->renderAdmin('admin/reports/index', $data);
    }

    public function updateStatus()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';

        if (!$id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        if ($this->reportModel->updateStatus($id, $status)) {
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

        $id = $_POST['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
            return;
        }

        if ($this->reportModel->delete($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
}
