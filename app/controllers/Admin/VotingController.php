<?php

namespace App\Controllers\Admin;

class VotingController extends BaseController
{
    private $voteModel;

    public function __construct()
    {
        parent::__construct();
        $this->voteModel = $this->model('Vote');
    }

    public function index()
    {
        $votingStats = $this->voteModel->getVotingStats();
        $suspicious = $this->voteModel->detectSuspiciousVotes();

        $data = [
            'title' => 'Voting Management',
            'votingStats' => $votingStats,
            'suspicious' => $suspicious
        ];

        $this->renderAdmin('admin/voting/index', $data);
    }
    public function reset()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        if ($this->voteModel->resetVotes()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reset votes']);
        }
    }

    public function ban()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $ip = $_POST['ip'] ?? '';
        if (empty($ip)) {
            echo json_encode(['success' => false, 'message' => 'IP address required']);
            return;
        }

        if ($this->voteModel->banIp($ip)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to ban IP']);
        }
    }

    public function export()
    {
        $votes = $this->voteModel->getAllVotes();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="votes_export_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Nomination ID', 'UID', 'IP Address', 'Date', 'Contestant Name', 'Country']);

        foreach ($votes as $vote) {
            fputcsv($output, [
                $vote['id'],
                $vote['nomination_id'],
                $vote['uid'],
                $vote['ip_address'],
                $vote['date'],
                $vote['contestant_name'],
                $vote['country']
            ]);
        }
        fclose($output);
        exit;
    }
}
