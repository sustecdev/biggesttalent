<?php
namespace App\Models;

use App\Core\Database;

class Contest
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    public function getAll()
    {
        $query = "SELECT * FROM bt_contests ORDER BY start_date DESC";
        $result = $this->db->query($query);
        $contests = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $contests[] = $row;
            }
        }
        return $contests;
    }

    public function getById($id)
    {
        $id = (int) $id;
        $query = "SELECT * FROM bt_contests WHERE id = $id";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    public function save($data)
    {
        $id = (int) ($data['id'] ?? 0);
        $name = $this->db->real_escape_string($data['name'] ?? '');
        $start_date = $this->db->real_escape_string($data['start_date'] ?? '');
        $end_date = $this->db->real_escape_string($data['end_date'] ?? '');
        $prize_text = $this->db->real_escape_string($data['prize_text'] ?? '');
        $is_active = isset($data['is_active']) ? 1 : 0;

        if (empty($name) || empty($start_date) || empty($end_date)) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }

        if ($end_date < $start_date) {
            return ['success' => false, 'message' => 'End date must be after start date'];
        }

        if ($id > 0) {
            $query = "UPDATE bt_contests 
                      SET name = '$name', start_date = '$start_date', end_date = '$end_date', 
                          prize_text = '$prize_text', is_active = $is_active 
                      WHERE id = $id";
        } else {
            $query = "INSERT INTO bt_contests (name, start_date, end_date, prize_text, is_active, created_at) 
                      VALUES ('$name', '$start_date', '$end_date', '$prize_text', $is_active, NOW())";
        }

        if ($this->db->query($query)) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Database error: ' . $this->db->error];
    }

    public function delete($id)
    {
        $id = (int) $id;
        $query = "DELETE FROM bt_contests WHERE id = $id";

        if ($this->db->query($query)) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Database error'];
    }

    private function ensureTable()
    {
        $check = $this->db->query("SHOW TABLES LIKE 'bt_contests'");
        if (!$check || $check->num_rows == 0) {
            $query = "CREATE TABLE IF NOT EXISTS `bt_contests` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(200) NOT NULL,
              `start_date` date NOT NULL,
              `end_date` date NOT NULL,
              `prize_text` text,
              `is_active` tinyint(1) DEFAULT '1',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($query);
        }
    }
}
