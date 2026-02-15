<?php

namespace App\Controllers\Admin;

class JudgesController extends BaseController
{
    private $judgeModel;

    public function __construct()
    {
        parent::__construct();
        $this->judgeModel = $this->model('Judge');
    }

    public function index()
    {
        $judges = $this->judgeModel->getAll();

        $data = [
            'title' => 'Judges Management',
            'judges' => $judges
        ];

        $this->renderAdmin('admin/judges/index', $data);
    }

    public function save()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $data = $_POST;

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/images/judges/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                $data['image'] = 'images/judges/' . $imageName;
            }
        }

        $result = $this->judgeModel->save($data);
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
        $result = $this->judgeModel->delete($id);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete']);
        }
    }


    public function edit($id)
    {
        $id = (int) $id;
        $judge = $this->judgeModel->getById($id);

        if (!$judge) {
            // Redirect or show error if not found
            header('Location: ' . URLROOT . '/admin/judges');
            exit;
        }

        $data = [
            'title' => 'Edit Judge',
            'judge' => $judge
        ];

        $this->renderAdmin('admin/judges/edit', $data);
    }
}
