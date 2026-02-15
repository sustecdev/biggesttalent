<?php
session_start();
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

// Load Config
Config::load();

// Init DB connection
$db = Database::getInstance();
$GLOBALS['mysqli'] = $db->getConnection();

require_once '../app/helpers/functions.php';

header('Content-Type: application/json');

// Set charset
if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli']) {
    $GLOBALS['mysqli']->set_charset("utf8");

    // Auto-create bt_votes table if it doesn't exist
    $checkTable = "SHOW TABLES LIKE 'bt_votes'";
    $result = $GLOBALS['mysqli']->query($checkTable);

    if (!$result || $result->num_rows == 0) {
        $createTable = "CREATE TABLE IF NOT EXISTS `bt_votes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `nomination_id` int(11) NOT NULL,
          `stage` varchar(50) NOT NULL DEFAULT 'national',
          `uid` int(11) DEFAULT NULL,
          `ip_address` varchar(45) NOT NULL,
          `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `nomination_id` (`nomination_id`),
          KEY `stage` (`stage`),
          KEY `uid` (`uid`),
          KEY `ip_address` (`ip_address`),
          KEY `date` (`date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $GLOBALS['mysqli']->query($createTable);
    } else {
        // Ensure stage column exists for existing tables (phase-aware voting)
        $colCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_votes LIKE 'stage'");
        if ($colCheck && $colCheck->num_rows == 0) {
            $GLOBALS['mysqli']->query("ALTER TABLE bt_votes ADD COLUMN stage VARCHAR(50) NOT NULL DEFAULT 'national' AFTER nomination_id");
            $GLOBALS['mysqli']->query("ALTER TABLE bt_votes ADD INDEX idx_stage (stage)");
        }
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Handle different actions
switch ($action) {
    case 'get_stats':
        $totalVotes = (int) getTotRecords('*', 'bt_votes', '');
        echo json_encode(['success' => true, 'total_votes' => $totalVotes]);
        exit;

    case 'get_vote_count':
        $nomination_id = isset($_GET['nomination_id']) ? (int) $_GET['nomination_id'] : 0;
        if ($nomination_id > 0) {
            $voteCount = (int) getTotRecords('*', 'bt_votes', "WHERE nomination_id=$nomination_id");
            $active = function_exists('getActiveSeasonSimple') ? getActiveSeasonSimple() : null;
            $stageColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_votes LIKE 'stage'");
            if ($active && $stageColCheck && $stageColCheck->num_rows > 0) {
                $st = (isset($active['current_stage']) && $active['current_stage']) ? $active['current_stage'] : 'national';
                $st = $GLOBALS['mysqli']->real_escape_string($st);
                $r = $GLOBALS['mysqli']->query("SELECT COUNT(*) as c FROM bt_votes WHERE nomination_id = $nomination_id AND (stage = '$st' OR stage IS NULL)");
                $voteCount = ($r && $row = $r->fetch_assoc()) ? (int) $row['c'] : $voteCount;
            }
            echo json_encode(['success' => true, 'vote_count' => $voteCount]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid nomination ID']);
        }
        exit;

    default:
        // Handle vote submission
        handleVoteSubmission();
        break;
}

function handleVoteSubmission()
{
    // Require authentication
    if (!isAuthenticated()) {
        echo json_encode([
            'success' => false,
            'message' => 'Please login to vote',
            'redirect' => 'safezone.php?redirect=vote.php'
        ]);
        exit;
    }

    // Get input
    $nomination_id = isset($_POST['nomination_id']) ? (int) $_POST['nomination_id'] : 0;
    $uid = isset($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;
    $ip_address = GetIP();

    // Validation
    if ($nomination_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid nomination ID']);
        exit;
    }

    if ($uid <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please login to vote',
            'redirect' => 'safezone.php?redirect=vote.php'
        ]);
        exit;
    }

    // Check if voting is open for the active season
    $activeSeason = function_exists('getActiveSeasonSimple') ? getActiveSeasonSimple() : null;
    if ($activeSeason && isset($activeSeason['is_voting_open']) && (int) $activeSeason['is_voting_open'] === 0) {
        echo json_encode(['success' => false, 'message' => 'Voting is currently closed. Please check back when voting opens.']);
        exit;
    }

    // Check if nomination exists and is approved
    $nominationCheck = "SELECT id, status FROM bt_nominations WHERE id = $nomination_id";
    $result = $GLOBALS['mysqli']->query($nominationCheck);

    if (!$result || $result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Contestant not found']);
        exit;
    }

    $nomination = $result->fetch_assoc();
    if ($nomination['status'] !== 'approved') {
        echo json_encode(['success' => false, 'message' => 'This contestant is not available for voting']);
        exit;
    }

    // Stage eligibility: for round_1/2/3, contestant must be in bt_contest_stage_qualifiers
    $currentStage = (isset($activeSeason['current_stage']) && $activeSeason['current_stage']) ? $activeSeason['current_stage'] : 'national';
    if (in_array($currentStage, ['round_1', 'round_2', 'round_3'])) {
        $seasonId = (int) ($activeSeason['id'] ?? 0);
        $stageEscaped = $GLOBALS['mysqli']->real_escape_string($currentStage);
        $qualTableCheck = $GLOBALS['mysqli']->query("SHOW TABLES LIKE 'bt_contest_stage_qualifiers'");
        if ($qualTableCheck && $qualTableCheck->num_rows > 0 && $seasonId > 0) {
            $qualCheck = $GLOBALS['mysqli']->query("SELECT 1 FROM bt_contest_stage_qualifiers WHERE season_id = $seasonId AND nomination_id = $nomination_id AND stage = '$stageEscaped' LIMIT 1");
            if (!$qualCheck || $qualCheck->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'This contestant is not in the current voting round.']);
                exit;
            }
        }
    }

    // Check if user/IP has reached daily vote limit (1 vote per day)
    $votesToday = getVotesToday($nomination_id, $uid, $ip_address);
    if ($votesToday >= 1) {
        echo json_encode(['success' => false, 'message' => 'You have reached your daily voting limit (1 vote per day) for this contestant']);
        exit;
    }

    // Check if IP is banned (only if table exists)
    $tableCheck = "SHOW TABLES LIKE 'bt_banned_ips'";
    $tableResult = $GLOBALS['mysqli']->query($tableCheck);
    if ($tableResult && $tableResult->num_rows > 0) {
        $bannedCheck = "SELECT id FROM bt_banned_ips WHERE ip_address = '" . $GLOBALS['mysqli']->real_escape_string($ip_address) . "'";
        $bannedResult = $GLOBALS['mysqli']->query($bannedCheck);
        if ($bannedResult && $bannedResult->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Your IP address has been banned from voting']);
            exit;
        }
    }

    // Check if bt_votes has stage column for stage-aware voting
    $hasStageCol = false;
    $stageColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_votes LIKE 'stage'");
    if ($stageColCheck && $stageColCheck->num_rows > 0) {
        $hasStageCol = true;
    }
    $currentStage = (isset($activeSeason['current_stage']) && $activeSeason['current_stage']) ? $activeSeason['current_stage'] : 'national';
    $stageEscaped = $GLOBALS['mysqli']->real_escape_string($currentStage);

    if ($hasStageCol) {
        $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date, stage) VALUES (?, ?, ?, NOW(), ?)");
        if ($stmt) {
            $stmt->bind_param("iiss", $nomination_id, $uid, $ip_address, $stageEscaped);
        }
    } else {
        $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date) VALUES (?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("iis", $nomination_id, $uid, $ip_address);
        }
    }

    if (!$stmt) {
        // Log the actual error for debugging
        error_log("Vote prepare failed: " . $GLOBALS['mysqli']->error);
        echo json_encode([
            'success' => false,
            'message' => 'Database error preparing statement',
            'debug' => $GLOBALS['mysqli']->error
        ]);
        exit;
    }

    if ($stmt->execute()) {
        $stmt->close();

        // Get updated vote count (stage-filtered when stage column exists)
        if ($hasStageCol) {
            $voteCountRes = $GLOBALS['mysqli']->query("SELECT COUNT(*) as c FROM bt_votes WHERE nomination_id = $nomination_id AND (stage = '$stageEscaped' OR stage IS NULL)");
            $voteCount = ($voteCountRes && $row = $voteCountRes->fetch_assoc()) ? (int) $row['c'] : 0;
        } else {
            $voteCount = (int) getTotRecords('*', 'bt_votes', "WHERE nomination_id=$nomination_id");
        }

        // Get remaining votes for today
        $votesToday = getVotesToday($nomination_id, $uid, $ip_address);
        $votesRemaining = 1 - $votesToday;

        echo json_encode([
            'success' => true,
            'message' => 'Vote cast successfully!',
            'vote_count' => $voteCount,
            'votes_remaining' => $votesRemaining,
            'votes_today' => $votesToday
        ]);
    } else {
        $error = $stmt->error;
        $errno = $stmt->errno;
        error_log("Vote execute failed: " . $error . " (Error #" . $errno . ")");
        $stmt->close();

        // Provide more specific error messages
        if ($errno == 1146) {
            echo json_encode([
                'success' => false,
                'message' => 'Votes table does not exist. Please contact administrator.',
                'debug' => $error
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to cast vote',
                'debug' => $error
            ]);
        }
    }
}
?>