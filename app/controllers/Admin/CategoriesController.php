<?php

namespace App\Controllers\Admin;

class CategoriesController extends BaseController
{
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = $this->model('Category');
    }

    public function index()
    {
        $categories = $this->categoryModel->getAll();

        $data = [
            'title' => 'Categories Management',
            'categories' => $categories
        ];

        $this->renderAdmin('admin/categories/index', $data);
    }

    public function save()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        if ($this->categoryModel->save($_POST)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save category']);
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

        $result = $this->categoryModel->delete($id);
        echo json_encode($result);
    }
}
