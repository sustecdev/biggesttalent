<?php
namespace App\Models;

use App\Core\Database;

class Contestant
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $mysqli = $this->db->getConnection();

        // Logic from getNominationsList('approved', 1000)
        // Fetches approved nominations which are treated as contestants
        $query = "SELECT n.*, a.username, a.email, p.fname, p.lname 
                  FROM bt_nominations n 
                  LEFT JOIN pi_account a ON n.uid = a.uid 
                  LEFT JOIN pi_profile p ON n.uid = p.uid 
                  WHERE n.status='approved' 
                  ORDER BY n.date DESC";

        $result = $mysqli->query($query);
        $contestants = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Get vote count for each contestant
                $voteQuery = "SELECT COUNT(*) as count FROM bt_votes WHERE nomination_id = " . (int) $row['id'];
                $voteRes = $mysqli->query($voteQuery);
                $votes = 0;
                if ($voteRes && $voteRes->num_rows > 0) {
                    $votes = (int) $voteRes->fetch_assoc()['count'];
                }
                $row['vote_count'] = $votes;

                $contestants[] = $row;
            }
        }

        return $contestants;
    }

    public function getById($id)
    {
        $id = (int)$id;
        $query = "SELECT * FROM bt_nominations WHERE id=$id";
        $result = $this->db->query($query);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    public function save($data)
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        // Basic fields that can be edited: stage_name, possibly category_id, bio/description (if exists)
        // Let's assume stage_name and status update.
        // Also video_url.
        $stage_name = $this->db->escape($data['stage_name'] ?? '');
        $video_url = $this->db->escape($data['video_url'] ?? '');
        $category_id = (int)($data['category_id'] ?? 0);
        $status = $this->db->escape($data['status'] ?? 'approved');

        if ($id > 0) {
            $query = "UPDATE bt_nominations SET 
                      stage_name='$stage_name', 
                      video_url='$video_url', 
                      category_id=$category_id,
                      status='$status'
                      WHERE id=$id";
            if ($this->db->query($query)) {
                return ['success' => true];
            }
        }
        return ['success' => false, 'message' => 'Update failed or invalid ID'];
    }

    public function delete($id)
    {
        $id = (int)$id;
        $query = "DELETE FROM bt_nominations WHERE id=$id";
        return $this->db->query($query);
    }
}
