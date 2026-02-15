<?php
namespace App\Models;

use App\Core\Database;

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    public function getAll()
    {
        $query = "SELECT * FROM bt_categories ORDER BY name ASC";
        $result = $this->db->query($query);
        $categories = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }

    public function getById($id)
    {
        $id = (int) $id;
        $query = "SELECT * FROM bt_categories WHERE id = $id";
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
        $description = $this->db->real_escape_string($data['description'] ?? '');
        $is_active = isset($data['is_active']) ? 1 : 0;

        if (empty($name)) {
            return false;
        }

        if ($id > 0) {
            $query = "UPDATE bt_categories 
                      SET name = '$name', description = '$description', is_active = $is_active 
                      WHERE id = $id";
        } else {
            $query = "INSERT INTO bt_categories (name, description, is_active, created_at) 
                      VALUES ('$name', '$description', $is_active, NOW())";
        }

        return $this->db->query($query);
    }

    public function delete($id)
    {
        $id = (int) $id;

        // Check if category is used
        $checkQuery = "SELECT COUNT(*) as count FROM bt_nominations WHERE category_id = $id";
        $checkResult = $this->db->query($checkQuery);
        if ($checkResult) {
            $row = $checkResult->fetch_assoc();
            if ($row['count'] > 0) {
                return ['success' => false, 'message' => "Cannot delete: Used by {$row['count']} nominations"];
            }
        }

        $query = "DELETE FROM bt_categories WHERE id = $id";
        if ($this->db->query($query)) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Database error'];
    }

    private function ensureTable()
    {
        $check = $this->db->query("SHOW TABLES LIKE 'bt_categories'");
        if (!$check || $check->num_rows == 0) {
            $query = "CREATE TABLE IF NOT EXISTS `bt_categories` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `description` text,
              `is_active` tinyint(1) DEFAULT '1',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($query);
        }
    }
}
