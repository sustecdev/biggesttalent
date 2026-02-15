<?php
namespace App\Models;

use App\Core\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByPernum($pernum)
    {
        // Simple mock for now as actual implementation involves complex safezone api calls
        return false;
    }

    /**
     * Get all users with profile info
     */
    public function getAll($limit = 100)
    {
        $limit = (int) $limit;

        // Check if pi_account table exists
        $check = $this->db->query("SHOW TABLES LIKE 'pi_account'");
        if (!$check || $check->num_rows == 0) {
            return [];
        }

        $query = "SELECT a.uid, a.username, a.email, a.role, p.fname, p.lname, p.con_id as country 
                  FROM pi_account a 
                  LEFT JOIN pi_profile p ON a.uid = p.uid 
                  ORDER BY a.uid DESC 
                  LIMIT $limit";

        $result = $this->db->query($query);
        $users = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        return $users;
    }

    /**
     * Update user role
     */
    public function updateRole($uid, $role)
    {
        $uid = (int) $uid;
        $role = $this->db->real_escape_string($role);

        // Ensure role column exists
        $this->ensureRoleColumn();

        $query = "UPDATE pi_account SET role = '$role' WHERE uid = $uid";
        return $this->db->query($query);
    }

    /**
     * Ban a user
     */
    public function ban($uid)
    {
        return $this->updateRole($uid, 'banned');
    }

    /**
     * Delete user
     */
    public function delete($uid)
    {
        $uid = (int) $uid;
        // Delete from account and profile tables
        $query = "DELETE FROM pi_account WHERE uid = $uid";
        return $this->db->query($query);
    }

    /**
     * Find user by ID
     */
    public function findById($uid)
    {
        $uid = (int) $uid;
        $query = "SELECT * FROM pi_account WHERE uid = $uid";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Helper to ensure role column exists in pi_account
     */
    private function ensureRoleColumn()
    {
        $check = $this->db->query("SHOW COLUMNS FROM pi_account LIKE 'role'");
        if (!$check || $check->num_rows == 0) {
            $this->db->query("ALTER TABLE pi_account ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
        }
    }
}
