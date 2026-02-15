<?php

namespace App\Controllers\Admin;

class SettingsController extends BaseController
{
    private $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingModel = $this->model('Setting');
    }

    public function index()
    {
        $settings = $this->settingModel->getAll();

        $data = [
            'title' => 'Settings',
            'settings' => $settings
        ];

        $this->renderAdmin('admin/settings/index', $data);
    }

    public function save()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $result = $this->settingModel->save($_POST);
        echo json_encode($result);
    }
}
