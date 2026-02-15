<?php

namespace App\Controllers\Admin;

class ContestantsController extends BaseController
{
    private $contestantModel;

    public function __construct()
    {
        parent::__construct();
        $this->contestantModel = $this->model('Contestant');
    }

    public function index()
    {
        $contestants = $this->contestantModel->getAll();

        $data = [
            'title' => 'Contestant Management',
            'contestants' => $contestants
        ];

        $this->renderAdmin('admin/contestants/index', $data);
    }

    public function edit($id)
    {
        $id = (int)$id;
        $contestant = $this->contestantModel->getById($id);

        if (!$contestant) {
            header('Location: ' . URLROOT . '/admin/contestants');
            exit;
        }

        $data = [
            'title' => 'Edit Contestant',
            'contestant' => $contestant
        ];

        $this->renderAdmin('admin/contestants/edit', $data);
    }

    public function save()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        $result = $this->contestantModel->save($_POST);
        echo json_encode($result);
    }

    public function delete()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        $id = $_POST['id'] ?? 0;
        $result = $this->contestantModel->delete($id);
        echo json_encode(['success' => $result]);
    }
}
