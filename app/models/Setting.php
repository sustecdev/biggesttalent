<?php
namespace App\Models;

use App\Core\Database;

class Setting
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    public function getAll()
    {
        $query = "SELECT * FROM bt_settings";
        $result = $this->db->query($query);
        $settings = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['key']] = $row['value'];
            }
        }
        return $settings;
    }

    public function save($data)
    {
        // Allowed keys to prevent junk data
        $allowedKeys = [
            'logo_url',
            'hero_title',
            'hero_subtitle',
            'season_badge',
            'primary_color',
            'primary_hover',
            'accent_color',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'youtube_url',
            'contact_email',
            'contact_phone',
            'contact_address'
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                $this->updateSetting($key, $value);
            }
        }

        return ['success' => true];
    }

    private function updateSetting($key, $value)
    {
        $key = $this->db->real_escape_string($key);
        $value = $this->db->real_escape_string($value);

        // Check if exists
        $check = $this->db->query("SELECT id FROM bt_settings WHERE `key` = '$key'");

        if ($check && $check->num_rows > 0) {
            $query = "UPDATE bt_settings SET `value` = '$value', updated_at = NOW() WHERE `key` = '$key'";
        } else {
            $query = "INSERT INTO bt_settings (`key`, `value`, created_at, updated_at) VALUES ('$key', '$value', NOW(), NOW())";
        }

        return $this->db->query($query);
    }

    private function ensureTable()
    {
        $check = $this->db->query("SHOW TABLES LIKE 'bt_settings'");
        if (!$check || $check->num_rows == 0) {
            $query = "CREATE TABLE IF NOT EXISTS `bt_settings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `key` varchar(100) NOT NULL,
              `value` text,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `key_unique` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($query);
        }
    }
}
