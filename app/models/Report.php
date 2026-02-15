<?php
namespace App\Models;

use App\Core\Database;

class Report
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    public function getAll($status = '', $limit = 500)
    {
        // Check if nomination table exists to avoid join errors
        $nominationTableExists = $this->tableExists('bt_nominations');
        $userTableExists = $this->tableExists('pi_account');

        $where = '';
        if (!empty($status)) {
            $status = $this->db->real_escape_string($status);
            $where = "WHERE r.status = '$status'";
        }

        $query = "SELECT r.*";

        if ($nominationTableExists) {
            $query .= ", n.aname, n.title, n.country, n.vlink";
        } else {
            $query .= ", NULL as aname, NULL as title, NULL as country, NULL as vlink";
        }

        if ($userTableExists) {
            $query .= ", u.username, u.email";
        } else {
            $query .= ", NULL as username, NULL as email";
        }

        $query .= " FROM bt_reports r";

        if ($nominationTableExists) {
            $query .= " LEFT JOIN bt_nominations n ON r.nomination_id = n.id";
        }

        if ($userTableExists) {
            $query .= " LEFT JOIN pi_account u ON r.user_id = u.uid";
        }

        $query .= " $where ORDER BY r.created_at DESC LIMIT $limit";

        $result = $this->db->query($query);
        $reports = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }
        }
        return $reports;
    }

    public function updateStatus($id, $status)
    {
        $id = (int) $id;
        $status = $this->db->real_escape_string($status);

        if (!in_array($status, ['pending', 'reviewed', 'resolved', 'dismissed'])) {
            return false;
        }

        $query = "UPDATE bt_reports SET status = '$status', reviewed_at = NOW() WHERE id = $id";
        return $this->db->query($query);
    }

    public function delete($id)
    {
        $id = (int) $id;
        $query = "DELETE FROM bt_reports WHERE id = $id";
        return $this->db->query($query);
    }

    private function ensureTable()
    {
        if (!$this->tableExists('bt_reports')) {
            $query = "CREATE TABLE IF NOT EXISTS `bt_reports` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nomination_id` int(11) DEFAULT NULL,
              `user_id` int(11) DEFAULT NULL,
              `reason` varchar(255) NOT NULL,
              `details` text,
              `status` enum('pending','reviewed','resolved','dismissed') DEFAULT 'pending',
              `ip_address` varchar(45) DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `reviewed_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `nomination_id` (`nomination_id`),
              KEY `user_id` (`user_id`),
              KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($query);
        }
    }

    private function tableExists($table)
    {
        $check = $this->db->query("SHOW TABLES LIKE '$table'");
        return $check && $check->num_rows > 0;
    }
}
