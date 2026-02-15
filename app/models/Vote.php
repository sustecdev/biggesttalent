<?php

namespace App\Models;

use App\Core\Database;

class Vote
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getVotingStats()
    {
        $mysqli = $this->db->getConnection();
        $stats = [];

        $seasonModel = new Season();
        $activeSeason = $seasonModel->getActiveSeason();
        $currentStage = $activeSeason ? ($activeSeason['current_stage'] ?? 'national') : 'national';
        $stageEscaped = $this->db->escape($currentStage);

        // Check if season_id column exists in bt_nominations
        $hasSeasonId = false;
        $colCheck = $mysqli->query("SHOW COLUMNS FROM bt_nominations LIKE 'season_id'");
        if ($colCheck && $colCheck->num_rows > 0) {
            $hasSeasonId = true;
        }

        // Check if stage column exists in bt_votes
        $hasVoteStage = false;
        $stageColCheck = $mysqli->query("SHOW COLUMNS FROM bt_votes LIKE 'stage'");
        if ($stageColCheck && $stageColCheck->num_rows > 0) {
            $hasVoteStage = true;
        }

        $voteStageFilter = $hasVoteStage ? " AND v.stage = '$stageEscaped'" : '';

        // Total votes (all time)
        $queryTotal = "SELECT COUNT(*) as count FROM bt_votes";
        $resTotal = $mysqli->query($queryTotal);
        $stats['total_votes'] = ($resTotal && $row = $resTotal->fetch_assoc()) ? (int) $row['count'] : 0;

        if ($hasSeasonId && $activeSeason) {
            $seasonId = (int) $activeSeason['id'];
            // Top contestants (stage-restricted, active season only)
            $queryTop = "SELECT n.id, n.aname, n.country, n.country AS country_name, COUNT(v.id) AS vote_count
                         FROM bt_nominations n
                         LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteStageFilter
                         LEFT JOIN bt_seasons s ON n.season_id = s.id
                         WHERE n.status = 'approved' AND (s.is_active = 1 OR n.season_id IS NULL)
                         GROUP BY n.id, n.aname, n.country
                         ORDER BY vote_count DESC
                         LIMIT 32";
            $queryCountry = "SELECT n.country, n.country AS country_name, COUNT(v.id) AS vote_count
                             FROM bt_nominations n
                             LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteStageFilter
                             LEFT JOIN bt_seasons s ON n.season_id = s.id
                             WHERE n.status = 'approved' AND (s.is_active = 1 OR n.season_id IS NULL)
                             GROUP BY n.country
                             ORDER BY vote_count DESC
                             LIMIT 10";
        } else {
            // Fallback when season_id column doesn't exist
            $queryTop = "SELECT n.id, n.aname, n.country, n.country AS country_name, COUNT(v.id) AS vote_count
                         FROM bt_nominations n
                         LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteStageFilter
                         WHERE n.status = 'approved'
                         GROUP BY n.id, n.aname, n.country
                         ORDER BY vote_count DESC
                         LIMIT 32";
            $queryCountry = "SELECT n.country, n.country AS country_name, COUNT(v.id) AS vote_count
                             FROM bt_nominations n
                             LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteStageFilter
                             WHERE n.status = 'approved'
                             GROUP BY n.country
                             ORDER BY vote_count DESC
                             LIMIT 10";
        }

        $resTop = $mysqli->query($queryTop);
        $stats['top_contestants'] = [];
        if ($resTop) {
            while ($row = $resTop->fetch_assoc()) {
                $stats['top_contestants'][] = $row;
            }
        }

        $resCountry = $mysqli->query($queryCountry);
        $stats['votes_by_country'] = [];
        if ($resCountry) {
            while ($row = $resCountry->fetch_assoc()) {
                $stats['votes_by_country'][] = $row;
            }
        }

        return $stats;
    }

    public function getStageResults($seasonId, $stage, $limit = 32)
    {
        $seasonId = (int) $seasonId;
        $stage = $this->db->escape($stage);
        $limit = (int) $limit;

        $mysqli = $this->db->getConnection();

        $hasSeasonId = false;
        $colCheck = $mysqli->query("SHOW COLUMNS FROM bt_nominations LIKE 'season_id'");
        if ($colCheck && $colCheck->num_rows > 0) {
            $hasSeasonId = true;
        }

        $hasVoteStage = false;
        $stageColCheck = $mysqli->query("SHOW COLUMNS FROM bt_votes LIKE 'stage'");
        if ($stageColCheck && $stageColCheck->num_rows > 0) {
            $hasVoteStage = true;
        }
        $voteJoinStage = $hasVoteStage ? " AND v.stage = '$stage'" : "";

        if ($stage === 'national') {
            if ($hasSeasonId) {
                $query = "SELECT n.id, n.aname, n.title, n.country, COUNT(v.id) AS vote_count
                          FROM bt_nominations n
                          LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteJoinStage
                          WHERE n.season_id = $seasonId AND n.status = 'approved'
                          GROUP BY n.id, n.aname, n.title, n.country
                          ORDER BY vote_count DESC
                          LIMIT $limit";
            } else {
                $query = "SELECT n.id, n.aname, n.title, n.country, COUNT(v.id) AS vote_count
                          FROM bt_nominations n
                          LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteJoinStage
                          WHERE n.status = 'approved'
                          GROUP BY n.id, n.aname, n.title, n.country
                          ORDER BY vote_count DESC
                          LIMIT $limit";
            }
        } else {
            $query = "SELECT n.id, n.aname, n.title, n.country, COUNT(v.id) AS vote_count
                      FROM bt_contest_stage_qualifiers q
                      JOIN bt_nominations n ON q.nomination_id = n.id
                      LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteJoinStage
                      WHERE q.season_id = $seasonId AND q.stage = '$stage' AND n.status = 'approved'
                      GROUP BY n.id, n.aname, n.title, n.country
                      ORDER BY vote_count DESC
                      LIMIT $limit";
        }

        $result = $mysqli->query($query);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function detectSuspiciousVotes()
    {
        $mysqli = $this->db->getConnection();
        $query = "SELECT ip_address, COUNT(*) AS vote_count, GROUP_CONCAT(DISTINCT nomination_id) AS nominations
                  FROM bt_votes
                  GROUP BY ip_address
                  HAVING vote_count > 5
                  ORDER BY vote_count DESC";

        $result = $mysqli->query($query);
        $suspicious = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $suspicious[] = $row;
            }
        }
        return $suspicious;
    }

    public function resetVotes()
    {
        $query = "TRUNCATE TABLE bt_votes";
        return $this->db->query($query) !== false;
    }

    public function banIp($ip)
    {
        $ip = trim((string) $ip);
        if ($ip === '') {
            return false;
        }

        $check = $this->db->prepare("SELECT id FROM bt_banned_ips WHERE ip_address = ?");
        if (!$check) {
            return false;
        }
        $check->bind_param('s', $ip);
        $check->execute();
        $res = $check->get_result();
        $check->close();

        if ($res && $res->num_rows > 0) {
            return true;
        }

        $stmt = $this->db->prepare("INSERT INTO bt_banned_ips (ip_address) VALUES (?)");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('s', $ip);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getAllVotes()
    {
        $mysqli = $this->db->getConnection();
        $query = "SELECT v.*, n.aname AS contestant_name, n.country
                  FROM bt_votes v
                  LEFT JOIN bt_nominations n ON v.nomination_id = n.id
                  ORDER BY v.date DESC";

        $result = $mysqli->query($query);
        $votes = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $votes[] = $row;
            }
        }
        return $votes;
    }
}
