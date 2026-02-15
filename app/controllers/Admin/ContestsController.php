<?php

namespace App\Controllers\Admin;

class ContestsController extends BaseController
{
    private $contestModel;

    public function __construct()
    {
        parent::__construct();
        $this->contestModel = $this->model('Contest');
    }

    public function index()
    {
        $contests = $this->contestModel->getAll();

        $data = [
            'title' => 'Contests Management',
            'contests' => $contests
        ];

        $this->renderAdmin('admin/contests/index', $data);
    }

    public function save()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $result = $this->contestModel->save($_POST);
        echo json_encode($result);
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

        $result = $this->contestModel->delete($id);
        echo json_encode($result);
    }
}
