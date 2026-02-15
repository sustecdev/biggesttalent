<?php

namespace App\Models;

use App\Core\Database;

class Season
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureTable();
    }

    private function ensureTable()
    {
        $mysqli = $this->db->getConnection();
        $check = $mysqli->query("SHOW TABLES LIKE 'bt_seasons'");
        if (!$check || $check->num_rows == 0) {
            $query = "CREATE TABLE IF NOT EXISTS `bt_seasons` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `season_number` int(11) NOT NULL DEFAULT 0,
              `title` varchar(255) NOT NULL,
              `start_date` date NOT NULL,
              `end_date` date NOT NULL,
              `description` text,
              `image` varchar(255) DEFAULT NULL,
              `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Only one season can be active at a time',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `season_number` (`season_number`),
              KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $mysqli->query($query);
        } else {
            // Check if is_active column exists, if not add it
            $columnCheck = $mysqli->query("SHOW COLUMNS FROM bt_seasons LIKE 'is_active'");
            if (!$columnCheck || $columnCheck->num_rows === 0) {
                $mysqli->query("ALTER TABLE bt_seasons ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Only one season can be active at a time'");
                $mysqli->query("ALTER TABLE bt_seasons ADD INDEX idx_is_active (is_active)");
            }
            // Ensure is_nominations_open and is_voting_open exist
            $colNom = $mysqli->query("SHOW COLUMNS FROM bt_seasons LIKE 'is_nominations_open'");
            if (!$colNom || $colNom->num_rows === 0) {
                $mysqli->query("ALTER TABLE bt_seasons ADD COLUMN is_nominations_open TINYINT(1) DEFAULT 1");
            }
            $colVote = $mysqli->query("SHOW COLUMNS FROM bt_seasons LIKE 'is_voting_open'");
            if (!$colVote || $colVote->num_rows === 0) {
                $mysqli->query("ALTER TABLE bt_seasons ADD COLUMN is_voting_open TINYINT(1) DEFAULT 1");
            }
        }
    }

    public function getAll()
    {
        $mysqli = $this->db->getConnection();
        $query = "SELECT * FROM bt_seasons ORDER BY start_date DESC";
        $result = $mysqli->query($query);
        $seasons = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $seasons[] = $row;
            }
        }

        return $seasons;
    }

    // Get seasons for homepage display with participant counts and winner info
    public function getSeasonsForHomepage($limit = 4)
    {
        $limit = (int) $limit;
        $mysqli = $this->db->getConnection();

        // Check if bt_seasons table exists
        $tableCheck = "SHOW TABLES LIKE 'bt_seasons'";
        $tableResult = $mysqli->query($tableCheck);

        if (!$tableResult || $tableResult->num_rows === 0) {
            return []; // Return empty array if table doesn't exist
        }

        $query = "SELECT * FROM bt_seasons ORDER BY start_date DESC LIMIT ?";
        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $seasons = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $seasonId = (int) $row['id'];
                $startDate = $row['start_date'];
                $endDate = $row['end_date'];

                // Get participant count (nominations within season date range)
                $participantQuery = "SELECT COUNT(DISTINCT id) as count 
                                    FROM bt_nominations 
                                    WHERE DATE(date) BETWEEN ? AND ?";
                $participantStmt = $mysqli->prepare($participantQuery);
                $participantCount = 0;

                if ($participantStmt) {
                    $participantStmt->bind_param("ss", $startDate, $endDate);
                    $participantStmt->execute();
                    $participantResult = $participantStmt->get_result();
                    if ($participantResult && $participantResult->num_rows > 0) {
                        $participantRow = $participantResult->fetch_assoc();
                        $participantCount = (int) $participantRow['count'];
                    }
                    $participantStmt->close();
                }

                // Get winner country (nomination with most votes in this season)
                $winnerQuery = "SELECT n.country, COUNT(v.id) as vote_count
                               FROM bt_nominations n
                               LEFT JOIN bt_votes v ON n.id = v.nomination_id
                               WHERE n.status = 'approved' 
                               AND DATE(n.date) BETWEEN ? AND ?
                               GROUP BY n.id, n.country
                               ORDER BY vote_count DESC, n.id DESC
                               LIMIT 1";
                $winnerStmt = $mysqli->prepare($winnerQuery);
                $winnerCountry = null;

                if ($winnerStmt) {
                    $winnerStmt->bind_param("ss", $startDate, $endDate);
                    $winnerStmt->execute();
                    $winnerResult = $winnerStmt->get_result();
                    if ($winnerResult && $winnerResult->num_rows > 0) {
                        $winnerRow = $winnerResult->fetch_assoc();
                        $winnerCountry = $winnerRow['country'];
                    }
                    $winnerStmt->close();
                }

                // Get season image (if image column exists, otherwise use default)
                $image = $row['image'] ?? null;
                $defaultImages = [
                    'images/colorful-concert-stage-lights.jpg',
                    'images/stage-performance-spotlight.jpg',
                    'images/international-music-festival.jpg',
                    'images/inaugural-talent-competition.jpg'
                ];

                // Use default image if no image set
                if (empty($image)) {
                    $imageIndex = ($seasonId - 1) % count($defaultImages);
                    $image = $defaultImages[$imageIndex] ?? $defaultImages[0];
                }

                // Extract year from start_date
                $year = date('Y', strtotime($startDate));

                $seasons[] = [
                    'id' => $seasonId,
                    'season_number' => (int) $row['season_number'],
                    'title' => $row['title'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'year' => $year,
                    'description' => $row['description'] ?? '',
                    'image' => $image,
                    'participant_count' => $participantCount,
                    'winner_country' => $winnerCountry
                ];
            }
        }

        $stmt->close();
        return $seasons;
    }

    public function getSeasonsStats()
    {
        $stats = [
            'total_seasons' => 0,
            'total_countries' => 0,
            'total_winners' => 0,
            'total_participants' => 0
        ];

        $mysqli = $this->db->getConnection();

        // Check if bt_seasons table exists
        $tableCheck = "SHOW TABLES LIKE 'bt_seasons'";
        $tableResult = $mysqli->query($tableCheck);

        if (!$tableResult || $tableResult->num_rows === 0) {
            return $stats;
        }

        // Total seasons
        $seasonsQuery = "SELECT COUNT(*) as count FROM bt_seasons";
        $seasonsResult = $mysqli->query($seasonsQuery);
        if ($seasonsResult && $seasonsResult->num_rows > 0) {
            $row = $seasonsResult->fetch_assoc();
            $stats['total_seasons'] = (int) $row['count'];
        }

        // Total countries (distinct countries from nominations)
        $countriesQuery = "SELECT COUNT(DISTINCT country) as count FROM bt_nominations WHERE country != ''";
        $countriesResult = $mysqli->query($countriesQuery);
        if ($countriesResult && $countriesResult->num_rows > 0) {
            $row = $countriesResult->fetch_assoc();
            $stats['total_countries'] = (int) $row['count'];
        }

        // Total winners (seasons with winners)
        $winnersQuery = "SELECT COUNT(DISTINCT s.id) as count
                        FROM bt_seasons s
                        INNER JOIN bt_nominations n ON DATE(n.date) BETWEEN s.start_date AND s.end_date
                        WHERE n.status = 'approved'";
        $winnersResult = $mysqli->query($winnersQuery);
        if ($winnersResult && $winnersResult->num_rows > 0) {
            $row = $winnersResult->fetch_assoc();
            $stats['total_winners'] = (int) $row['count'];
        }

        // Total participants (all nominations)
        $participantsQuery = "SELECT COUNT(*) as count FROM bt_nominations";
        $participantsResult = $mysqli->query($participantsQuery);
        if ($participantsResult && $participantsResult->num_rows > 0) {
            $row = $participantsResult->fetch_assoc();
            $stats['total_participants'] = (int) $row['count'];
        }

        return $stats;
    }

    public function hasActiveSeason()
    {
        $mysqli = $this->db->getConnection();

        // Check if bt_seasons table exists
        $tableCheck = "SHOW TABLES LIKE 'bt_seasons'";
        $tableResult = $mysqli->query($tableCheck);

        if (!$tableResult || $tableResult->num_rows === 0) {
            return false;
        }

        $query = "SELECT id FROM bt_seasons WHERE is_active = 1 LIMIT 1";
        $result = $mysqli->query($query);

        return ($result && $result->num_rows > 0);
    }

    public function getActiveSeason()
    {
        $mysqli = $this->db->getConnection();

        // Check if bt_seasons table exists
        $tableCheck = "SHOW TABLES LIKE 'bt_seasons'";
        $tableResult = $mysqli->query($tableCheck);

        if (!$tableResult || $tableResult->num_rows === 0) {
            return null;
        }

        $query = "SELECT * FROM bt_seasons WHERE is_active = 1 LIMIT 1";
        $result = $mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        // Fallback: if no season is active but seasons exist, auto-activate the most recent one
        $fallback = $mysqli->query("SELECT * FROM bt_seasons ORDER BY start_date DESC LIMIT 1");
        if ($fallback && $fallback->num_rows > 0) {
            $season = $fallback->fetch_assoc();
            $res = $this->setActiveSeason((int) $season['id']);
            if ($res['success'] ?? false) {
                $result = $mysqli->query("SELECT * FROM bt_seasons WHERE is_active = 1 LIMIT 1");
                if ($result && $result->num_rows > 0) {
                    return $result->fetch_assoc();
                }
            }
            return $season; // Return season data even if activation failed (for display)
        }

        return null;
    }

    public function setActiveSeason($id)
    {
        $id = (int) $id;
        $mysqli = $this->db->getConnection();

        // Start transaction
        $mysqli->begin_transaction();

        try {
            // Deactivate all seasons
            $mysqli->query("UPDATE bt_seasons SET is_active = 0");

            // Activate the selected season
            $stmt = $mysqli->prepare("UPDATE bt_seasons SET is_active = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception("Season not found");
            }

            $stmt->close();
            $mysqli->commit();

            return ['success' => true, 'message' => 'Season activated successfully'];
        } catch (\Exception $e) {
            $mysqli->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function save($data)
    {
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        $season_number = (int) ($data['season_number'] ?? 0);
        $title = $this->db->escape($data['title'] ?? '');
        $start_date = $this->db->escape($data['start_date'] ?? '');
        $end_date = $this->db->escape($data['end_date'] ?? '');
        $description = $this->db->escape($data['description'] ?? '');

        if (empty($title) || empty($start_date) || empty($end_date)) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }

        try {
            if ($id > 0) {
                $query = "UPDATE bt_seasons SET 
                          season_number=$season_number, 
                          title='$title', 
                          start_date='$start_date', 
                          end_date='$end_date', 
                          description='$description' 
                          WHERE id=$id";
            } else {
                $query = "INSERT INTO bt_seasons (season_number, title, start_date, end_date, description, is_nominations_open, is_voting_open) 
                          VALUES ($season_number, '$title', '$start_date', '$end_date', '$description', 1, 0)";
            }

            if ($this->db->query($query)) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Database error: ' . $this->db->getConnection()->error];
            }
        } catch (\mysqli_sql_exception $e) {
            // Handle duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['success' => false, 'message' => 'Season number already exists. Please use a different number.'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getById($id)
    {
        $id = (int) $id;
        $query = "SELECT * FROM bt_seasons WHERE id=$id";
        $result = $this->db->query($query);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    public function close($id)
    {
        $id = (int) $id;
        // Setting end_date to yesterday closes it effectively, or use a status column if exists.
        // Legacy logic usually just manipulated dates or had a 'status' field?
        // View implies "Voting will be disabled".
        // Let's assume ending it now.
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $query = "UPDATE bt_seasons SET end_date = '$yesterday' WHERE id=$id";
        return $this->db->query($query);
    }

    public function delete($id)
    {
        $id = (int) $id;
        $query = "DELETE FROM bt_seasons WHERE id=$id";
        return $this->db->query($query);
    }


    public function getCurrentStage($seasonId)
    {
        $id = (int) $seasonId;
        $query = "SELECT current_stage FROM bt_seasons WHERE id = $id";
        $result = $this->db->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['current_stage'];
        }
        return 'national';
    }

    public function updateStage($seasonId, $stage)
    {
        $id = (int) $seasonId;
        $stage = $this->db->escape($stage);

        // Validate stage
        $allowedStages = ['national', 'round_1', 'round_2', 'round_3', 'closed'];
        if (!in_array($stage, $allowedStages)) {
            return false;
        }

        $query = "UPDATE bt_seasons SET current_stage = '$stage' WHERE id = $id";
        return $this->db->query($query);
    }

    public function addQualifiers($seasonId, $stage, $nominationIds)
    {
        $seasonId = (int) $seasonId;
        $stage = $this->db->escape($stage);

        if (empty($nominationIds)) {
            return true;
        }

        $mysqli = $this->db->getConnection();
        $stmt = $mysqli->prepare("INSERT IGNORE INTO bt_contest_stage_qualifiers (season_id, nomination_id, stage) VALUES (?, ?, ?)");

        foreach ($nominationIds as $nominationId) {
            $nominationId = (int) $nominationId;
            $stmt->bind_param("iis", $seasonId, $nominationId, $stage);
            $stmt->execute();
        }

        $stmt->close();
        return true;
    }

    public function getQualifiers($seasonId, $stage)
    {
        $seasonId = (int) $seasonId;
        $stage = $this->db->escape($stage);

        $query = "SELECT nomination_id FROM bt_contest_stage_qualifiers WHERE season_id = $seasonId AND stage = '$stage'";
        $result = $this->db->query($query);
        $ids = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ids[] = $row['nomination_id'];
            }
        }
        return $ids;
    }

    public function isQualifier($nominationId, $seasonId, $stage)
    {
        $nominationId = (int) $nominationId;
        $seasonId = (int) $seasonId;
        $stage = $this->db->escape($stage);

        // National stage includes everyone
        if ($stage === 'national') {
            return true;
        }

        $query = "SELECT id FROM bt_contest_stage_qualifiers WHERE season_id = $seasonId AND nomination_id = $nominationId AND stage = '$stage'";
        $result = $this->db->query($query);
        return ($result && $result->num_rows > 0);
    }

    public function setWinner($seasonId, $nominationId)
    {
        $seasonId = (int) $seasonId;
        $nominationId = (int) $nominationId;

        $query = "UPDATE bt_seasons SET winner_id = $nominationId WHERE id = $seasonId";
        return $this->db->query($query);
    }
}
