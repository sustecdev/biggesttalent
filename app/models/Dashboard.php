<?php
namespace App\Models;

use App\Core\Database;

class Dashboard
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getStats()
    {
        $stats = [];
        $mysqli = $this->db->getConnection();

        // Total Contestants (approved nominations)
        $stats['total_contestants'] = $this->countRecords('bt_nominations', 'status="approved"');

        // Total Votes Cast
        $stats['total_votes'] = $this->countRecords('bt_votes');

        // Total Judges
        $stats['total_judges'] = $this->countRecords('bt_judges', 'active=1');

        // Total Nominations (all)
        $stats['total_nominations'] = $this->countRecords('bt_nominations');

        // Total Seasons
        $stats['total_seasons'] = $this->countRecords('bt_seasons');

        // Users Registered
        if ($this->tableExists('pi_account')) {
            $stats['users_registered'] = $this->countRecords('pi_account');
        } else {
            $stats['users_registered'] = 0;
        }

        // Pending Applications
        $stats['pending_applications'] = $this->countRecords('bt_nominations', 'status="pending"');

        // NEW FEATURES STATS

        // Total Categories
        if ($this->tableExists('bt_categories')) {
            $stats['total_categories'] = $this->countRecords('bt_categories', 'is_active=1');
        } else {
            $stats['total_categories'] = 0;
        }

        // Total Contests
        if ($this->tableExists('bt_contests')) {
            $stats['total_contests'] = $this->countRecords('bt_contests', 'is_active=1');
            $stats['active_contests'] = $this->countRecords('bt_contests', "is_active=1 AND start_date <= CURDATE() AND end_date >= CURDATE()");
        } else {
            $stats['total_contests'] = 0;
            $stats['active_contests'] = 0;
        }

        // Reports
        if ($this->tableExists('bt_reports')) {
            $stats['total_reports'] = $this->countRecords('bt_reports');
            $stats['pending_reports'] = $this->countRecords('bt_reports', "status='pending'");
        } else {
            $stats['total_reports'] = 0;
            $stats['pending_reports'] = 0;
        }

        // Video Uploads
        if ($this->tableExists('bt_video_uploads')) {
            $stats['total_video_uploads'] = $this->countRecords('bt_video_uploads');
        } else {
            $stats['total_video_uploads'] = 0;
        }

        // Nominations with video files
        if ($this->columnExists('bt_nominations', 'video_file')) {
            $stats['nominations_with_video_files'] = $this->countRecords('bt_nominations', "video_file IS NOT NULL AND video_file != ''");
        } else {
            $stats['nominations_with_video_files'] = 0;
        }

        // Nominations with categories
        if ($this->columnExists('bt_nominations', 'category_id')) {
            $stats['nominations_with_categories'] = $this->countRecords('bt_nominations', "category_id IS NOT NULL AND category_id > 0");
        } else {
            $stats['nominations_with_categories'] = 0;
        }

        return $stats;
    }

    // Helper to count records
    private function countRecords($table, $where = '')
    {
        $mysqli = $this->db->getConnection();
        $query = "SELECT COUNT(*) as count FROM $table";
        if (!empty($where)) {
            $query .= " WHERE $where";
        }

        $result = $mysqli->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return (int) $row['count'];
        }
        return 0;
    }

    // Helper to check if table exists
    private function tableExists($table)
    {
        $mysqli = $this->db->getConnection();
        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
        return $result && $result->num_rows > 0;
    }

    // Helper to check if column exists
    private function columnExists($table, $column)
    {
        $mysqli = $this->db->getConnection();
        $result = $mysqli->query("SHOW COLUMNS FROM $table LIKE '$column'");
        return $result && $result->num_rows > 0;
    }
}
