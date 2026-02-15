<?php
namespace App\Models;

use App\Core\Database;

class Judge
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $mysqli = $this->db->getConnection();
        $query = "SELECT * FROM bt_judges ORDER BY id DESC";
        $result = $mysqli->query($query);
        $judges = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $judges[] = $row;
            }
        }

        return $judges;
    }

    public function save($data, $files = [])
    {
        $name = $this->db->escape($data['name'] ?? '');
        $title = $this->db->escape($data['title'] ?? '');
        $bio = $this->db->escape($data['bio'] ?? '');
        $active = isset($data['active']) ? 1 : 0;
        $id = isset($data['id']) ? (int) $data['id'] : 0;

        // Handle image upload logic here or in controller. For now, assuming controller passes filename or handles it.
        // Simplified: Controller handles file upload and passes 'image' in data.
        $image = $this->db->escape($data['image'] ?? '');

        if (empty($name) || empty($title)) {
            return ['success' => false, 'message' => 'Name and Title are required'];
        }

        if ($id > 0) {
            $query = "UPDATE bt_judges SET name='$name', title='$title', bio='$bio', active=$active";
            if (!empty($image)) {
                $query .= ", image='$image'";
            }
            $query .= " WHERE id=$id";
        } else {
            $query = "INSERT INTO bt_judges (name, title, bio, image, active) VALUES ('$name', '$title', '$bio', '$image', $active)";
        }

        if ($this->db->query($query)) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Database error'];
    }

    public function getById($id)
    {
        $id = (int) $id;
        $query = "SELECT * FROM bt_judges WHERE id=$id";
        $result = $this->db->query($query);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    public function delete($id)
    {
        $id = (int) $id;
        $query = "DELETE FROM bt_judges WHERE id=$id";
        return $this->db->query($query);
    }
}
