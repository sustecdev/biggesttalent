<?php

namespace App\Controllers\Admin;

use App\Services\MailService;

class NominationsController extends BaseController
{
    private $nominationModel;

    public function __construct()
    {
        parent::__construct();
        $this->nominationModel = $this->model('Nomination');
    }

    public function index()
    {
        $status = $_GET['status'] ?? '';
        $nominations = $this->nominationModel->getAll($status);
        $nominationsByCountry = $this->nominationModel->getCountByCountry();

        $data = [
            'title' => 'Nominations Management',
            'status' => $status,
            'nominations' => $nominations,
            'nominationsByCountry' => $nominationsByCountry
        ];

        $this->renderAdmin('admin/nominations/index', $data);
    }

    public function updateStatus()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            $rejection_reason = trim($_POST['rejection_reason'] ?? '');
            
            if ($id <= 0 || !in_array($status, ['approved', 'rejected'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                exit;
            }

            $nomination = $this->nominationModel->getById($id);
            if (!$nomination) {
                echo json_encode(['success' => false, 'message' => 'Nomination not found']);
                exit;
            }
            
            $result = $this->nominationModel->updateStatus($id, $status, $rejection_reason);
            
            if ($result) {
                // Send email notification on approve/reject
                $recipientEmail = trim($nomination['email'] ?? '');
                if (empty($recipientEmail) && !empty($nomination['nominee_email'])) {
                    $recipientEmail = trim($nomination['nominee_email']);
                }
                if (!empty($recipientEmail) && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    $recipientName = trim(($nomination['fname'] ?? '') . ' ' . ($nomination['lname'] ?? ''));
                    $nomineeName = $nomination['aname'] ?? 'Your nominee';
                    $mail = new MailService();
                    $mail->sendNominationStatus(
                        $recipientEmail,
                        $recipientName,
                        $status,
                        $nomineeName,
                        $status === 'rejected' ? $rejection_reason : null
                    );
                }
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
            exit;
        }
    }
    public function delete()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
                exit;
            }

            $nomination = $this->nominationModel->getById($id);
            if (!$nomination) {
                echo json_encode(['success' => false, 'message' => 'Nomination not found']);
                exit;
            }
            
            $result = $this->nominationModel->delete($id);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Nomination deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete nomination']);
            }
            exit;
        }
    }
}
