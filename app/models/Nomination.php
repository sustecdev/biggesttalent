<?php

namespace App\Models;

use App\Core\Database;

class Nomination
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function updateStatus($id, $status, $rejection_reason = '')
    {
        $id = (int)$id;
        $status = $this->db->escape($status);
        $rejection_reason = $this->db->escape($rejection_reason);
        
        if ($id <= 0 || !in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }

        $query = "UPDATE bt_nominations SET status = '$status', rejection_reason = " . ($status === 'rejected' ? "'$rejection_reason'" : 'NULL') . " WHERE id = $id";
        return $this->db->query($query);
    }

    /**
     * Get a single nomination by ID with nominator details (email from pi_account)
     */
    public function getById($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }
        $mysqli = $this->db->getConnection();
        $query = "SELECT n.*, a.username, a.email, p.fname, p.lname, c.name as category_name 
                  FROM bt_nominations n 
                  LEFT JOIN pi_account a ON n.uid = a.uid 
                  LEFT JOIN pi_profile p ON n.uid = p.uid
                  LEFT JOIN bt_categories c ON n.category_id = c.id
                  WHERE n.id = $id";
        $result = $mysqli->query($query);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    public function getAll($status = '', $limit = 500)
    {
        $mysqli = $this->db->getConnection();
        $limit = (int) $limit;

        $query = "SELECT n.*, a.username, a.email, p.fname, p.lname, c.name as category_name 
                  FROM bt_nominations n 
                  LEFT JOIN pi_account a ON n.uid = a.uid 
                  LEFT JOIN pi_profile p ON n.uid = p.uid
                  LEFT JOIN bt_categories c ON n.category_id = c.id";

        if (!empty($status)) {
            $status = $mysqli->real_escape_string($status);
            $query .= " WHERE n.status = '$status'";
        }

        $query .= " ORDER BY n.date DESC LIMIT $limit";

        $result = $mysqli->query($query);
        $nominations = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $nominations[] = $row;
            }
        }

        return $nominations;
    }

    /**
     * Get count of nominations grouped by country (for admin stats)
     */
    public function getCountByCountry()
    {
        $mysqli = $this->db->getConnection();
        $query = "SELECT country, COUNT(*) as nomination_count 
                  FROM bt_nominations 
                  WHERE status IN ('approved', 'pending', 'rejected')
                  AND (country IS NOT NULL AND country != '')
                  GROUP BY country 
                  ORDER BY nomination_count DESC";
        $result = $mysqli->query($query);
        $byCountry = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $byCountry[] = $row;
            }
        }
        return $byCountry;
    }
}
