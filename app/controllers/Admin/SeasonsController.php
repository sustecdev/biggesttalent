<?php

namespace App\Controllers\Admin;

class SeasonsController extends BaseController
{
    private $seasonModel;

    public function __construct()
    {
        parent::__construct();
        $this->seasonModel = $this->model('Season');
    }

    public function index()
    {
        $seasons = $this->seasonModel->getAll();

        $data = [
            'title' => 'Seasons Management',
            'seasons' => $seasons
        ];

        $this->renderAdmin('admin/seasons/index', $data);
    }

    public function save()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        $result = $this->seasonModel->save($_POST);
        echo json_encode($result);
    }

    public function close()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        $id = $_POST['id'] ?? 0;
        $result = $this->seasonModel->close($id);
        echo json_encode(['success' => $result]);
    }

    public function toggleActive()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        $id = $_POST['id'] ?? 0;
        $result = $this->seasonModel->setActiveSeason($id);
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
        $result = $this->seasonModel->delete($id);
        echo json_encode(['success' => $result]);
    }

    public function edit($id)
    {
        $id = (int) $id;
        $season = $this->seasonModel->getById($id);

        if (!$season) {
            header('Location: ' . URLROOT . '/admin/seasons');
            exit;
        }

        $data = [
            'title' => 'Edit Season',
            'season' => $season
        ];

        $this->renderAdmin('admin/seasons/edit', $data);
    }
    public function progressStage()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $activeSeason = $this->seasonModel->getById($id);

        if (!$activeSeason) {
            echo json_encode(['success' => false, 'message' => 'Season not found']);
            return;
        }

        $currentStage = $activeSeason['current_stage'];
        $nextStage = '';
        $limit = 0;

        switch ($currentStage) {
            case 'national':
                $nextStage = 'round_1';
                $limit = 32;
                break;
            case 'round_1':
                $nextStage = 'round_2';
                $limit = 16;
                break;
            case 'round_2':
                $nextStage = 'round_3';
                $limit = 8;
                break;
            case 'round_3':
                $nextStage = 'closed';
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Cannot progress from current stage']);
                return;
        }

        if ($nextStage === 'closed') {
            // Just close it
            $this->seasonModel->updateStage($id, 'closed');
            echo json_encode(['success' => true, 'message' => 'Season closed successfully']);
            return;
        }

        // Get winners of current stage
        // We need a Vote model instance or method to get top N
        // We can use the Vote model's getStageResults or similar
        $voteModel = $this->model('Vote');
        $results = $voteModel->getStageResults($id, $currentStage, $limit);

        $qualifiers = [];
        foreach ($results as $row) {
            $qualifiers[] = $row['id'];
        }

        if (count($qualifiers) < $limit && count($qualifiers) > 0) {
            // Warn if fewer qualifiers than limit? Or just proceed?
            // User might want to know. But let's proceed for now.
        }

        // Add qualifiers for NEXT stage
        // i.e. The winners of 'national' become qualifiers for 'round_1'
        if (!empty($qualifiers)) {
            $this->seasonModel->addQualifiers($id, $nextStage, $qualifiers);
        }

        // Update Season Stage
        if ($this->seasonModel->updateStage($id, $nextStage)) {
            echo json_encode(['success' => true, 'message' => 'Proceeded to ' . $nextStage . ' with ' . count($qualifiers) . ' qualifiers']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update stage']);
        }
    }
    public function getStageSimulation()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $activeSeason = $this->seasonModel->getById($id);

        if (!$activeSeason) {
            echo json_encode(['success' => false, 'message' => 'Season not found']);
            return;
        }

        $currentStage = $activeSeason['current_stage'];
        $nextStage = '';
        $limit = 0;

        switch ($currentStage) {
            case 'national':
                $nextStage = 'round_1';
                $limit = 32;
                break;
            case 'round_1':
                $nextStage = 'round_2';
                $limit = 16;
                break;
            case 'round_2':
                $nextStage = 'round_3';
                $limit = 8;
                break;
            case 'round_3':
                $nextStage = 'closed';
                $limit = 8; // Show all 8 finalists
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Cannot progress from current stage']);
                return;
        }

        // Removed early return for 'closed' to ensure we return finalists for Winner Selection

        // Get winners of current stage
        $voteModel = $this->model('Vote');
        $results = $voteModel->getStageResults($id, $currentStage, $limit);

        echo json_encode([
            'success' => true,
            'current_stage' => $currentStage,
            'next_stage' => $nextStage,
            'limit' => $limit,
            'qualifiers' => $results
        ]);
    }

    public function saveWinner()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $seasonId = isset($_POST['season_id']) ? (int) $_POST['season_id'] : 0;
        $winnerId = isset($_POST['winner_id']) ? (int) $_POST['winner_id'] : 0;

        if ($seasonId <= 0 || $winnerId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        // Set Winner
        if ($this->seasonModel->setWinner($seasonId, $winnerId)) {
            // Also close the season
            $this->seasonModel->updateStage($seasonId, 'closed');
            echo json_encode(['success' => true, 'message' => 'Winner declared and season closed!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save winner']);
        }
    }

    public function toggleFeature()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $seasonId = $_POST['season_id'] ?? null;
        $feature = $_POST['feature'] ?? null; // 'nominations' or 'voting'
        $status = $_POST['status'] ?? 0; // 0 or 1

        if (!$seasonId || !$feature) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        $column = ($feature === 'nominations') ? 'is_nominations_open' : 'is_voting_open';

        // Validate column name safely
        if (!in_array($column, ['is_nominations_open', 'is_voting_open'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid feature']);
            return;
        }

        $db = \App\Core\Database::getInstance();
        $conn = $db->getConnection();

        // When opening voting, close nominations (and vice versa) - only one phase at a time
        if ($status == 1) {
            if ($feature === 'voting') {
                $stmt = $conn->prepare("UPDATE bt_seasons SET is_voting_open = 1, is_nominations_open = 0 WHERE id = ?");
            } else {
                $stmt = $conn->prepare("UPDATE bt_seasons SET is_nominations_open = 1, is_voting_open = 0 WHERE id = ?");
            }
            $stmt->bind_param("i", $seasonId);
        } else {
            $stmt = $conn->prepare("UPDATE bt_seasons SET $column = 0 WHERE id = ?");
            $stmt->bind_param("i", $seasonId);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => ucfirst($feature) . ' status updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
    }
}
