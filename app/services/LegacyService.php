<?php

namespace App\Services;

use App\Core\Database;

class LegacyService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function handleLegacyAutoLogin()
    {
        $pernum = $_GET['pernum'] ?? '';
        $password = $_GET['password'] ?? '';
        $key = $_GET['key'] ?? '';

        if (!$pernum || !$password) {
            return;
        }

        $log = "Attempt: $pernum | Key: $key\n";
        file_put_contents('../debug_login.txt', $log, FILE_APPEND);

        // Validate Key (Basic check)
        if ($key !== 'Dmjfk78Ckjksj23KlmdMMszcX') {
            file_put_contents('../debug_login.txt', "Invalid Key\n", FILE_APPEND);
            return;
        }

        // Sanitize
        $pernumSafe = $this->db->real_escape_string($pernum);

        // Find User by Username (Pernum)
        $sql = "SELECT uid, password FROM pi_account WHERE username = '$pernumSafe' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            file_put_contents('../debug_login.txt', "User Found: {$user['uid']} | DB Pass: {$user['password']} | Input Pass: $password\n", FILE_APPEND);

            if ($password === $user['password']) {
                $user['pernum'] = $pernum;
                $this->performLogin($user);
            } else {
                file_put_contents('../debug_login.txt', "Password Mismatch\n", FILE_APPEND);
            }
        } else {
            file_put_contents('../debug_login.txt', "User Not Found locally. Attempting API validation...\n", FILE_APPEND);

            // Validate against SafeZone API
            $apiUid = $this->validateWithSafeZone($pernum, $password);

            if ($apiUid) {
                file_put_contents('../debug_login.txt', "API Validation Success. Creating User: $apiUid\n", FILE_APPEND);

                // Create user in DB
                if ($this->createUserInDb($apiUid, $pernum, $password)) {
                    // Login with new user
                    $user = ['uid' => $apiUid, 'password' => $password, 'pernum' => $pernum];
                    $this->performLogin($user);
                } else {
                    file_put_contents('../debug_login.txt', "Failed to create user in DB\n", FILE_APPEND);
                }
            } else {
                file_put_contents('../debug_login.txt', "API Validation Failed\n", FILE_APPEND);
            }
        }
    }

    public function handleReferralTracking()
    {
        $pernumRaw = $_GET['pernum'] ?? '';
        if ($pernumRaw === '' || $this->isAssetRequest($pernumRaw)) {
            return;
        }

        $cookieLifetime = time() + (3600 * 24 * 365 * 10);
        setcookie('ref_pernum', $pernumRaw, $cookieLifetime, '/');

        $pernum = str_pad($pernumRaw, 10, '0', STR_PAD_LEFT);

        $userId = 0; // Initialize userId
        $sql = "SELECT uid FROM pi_account WHERE username = '" . $this->db->real_escape_string($pernum) . "' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['uid'];
        } else {
            $userId = 0;
        }

        if ((int) $userId > 0) {
            setcookie('ref_uid', $userId, $cookieLifetime, '/');
            return;
        }

        $fallback = ltrim($pernumRaw, '1');
        $fallback = ltrim($fallback, '0');
        setcookie('ref_uid', $fallback, $cookieLifetime, '/');
    }

    private function performLogin($user)
    {
        file_put_contents('../debug_login.txt', "Logging in user: {$user['uid']}\n", FILE_APPEND);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['uid'] = $user['uid'];
        $_SESSION['authenticated'] = true;
        if (!empty($user['pernum'])) {
            $_SESSION['pernum'] = $user['pernum'];
        }
        $_SESSION['role'] = function_exists('getUserRoleByUid') ? getUserRoleByUid((int) $user['uid'], $user['pernum'] ?? null) : 'user';

        // Get Profile Data
        $this->updateSessionProfile($user['uid']);

        file_put_contents('../debug_login.txt', "Session Set. Redirecting...\n", FILE_APPEND);
        header("Location: index.php");
        exit;
    }

    private function updateSessionProfile($uid)
    {
        $profileSql = "SELECT * FROM pi_profile WHERE uid = $uid LIMIT 1";
        $profileRes = $this->db->query($profileSql);
        if ($profileRes && $profileRes->num_rows > 0) {
            $profile = $profileRes->fetch_assoc();
            $_SESSION['fname'] = $profile['fname'];
            $_SESSION['lname'] = $profile['lname'];
            $_SESSION['email'] = $this->getUserEmail($uid) ?? '';
        }
    }

    private function validateWithSafeZone($pernum, $password)
    {
        $loginUrl = 'https://safe.zone/signup/login_api.php';
        $postData = http_build_query([
            'pernum' => $pernum,
            'password' => $password,
            'key' => 'Dmjfk78Ckjksj23KlmdMMszcX',
        ]);

        $ch = curl_init($loginUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For dev env
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response)
            return false;

        // Clean response similar to safezone_login.php
        $cleanedResponse = trim($response);
        if (preg_match('/^string\(\d+\)\s*"(.*)"\s*$/s', $cleanedResponse, $matches)) {
            $cleanedResponse = stripcslashes($matches[1]);
        }

        // Parse JSON
        $firstBrace = strpos($cleanedResponse, '{');
        if ($firstBrace !== false) {
            $cleanedResponse = substr($cleanedResponse, $firstBrace);
            $lastBrace = strrpos($cleanedResponse, '}');
            if ($lastBrace !== false) {
                $cleanedResponse = substr($cleanedResponse, 0, $lastBrace + 1);
            }
        }

        $result = json_decode($cleanedResponse, true);

        if ($result && isset($result['uid'])) {
            return $result['uid'];
        }

        // Try fallback regex
        if (preg_match('/uid[=:]?\s*["\']?(\d+)["\']?/i', $response, $matches)) {
            return intval($matches[1]);
        }

        return false;
    }

    private function createUserInDb($uid, $pernum, $password)
    {
        $uid = (int) $uid;
        $pernumSafe = $this->db->real_escape_string($pernum);
        $passwordSafe = $this->db->real_escape_string($password);

        // Insert into pi_account
        $sql = "INSERT INTO pi_account (uid, username, password, email, role) VALUES ($uid, '$pernumSafe', '$passwordSafe', '', 'user') ON DUPLICATE KEY UPDATE password='$passwordSafe'";

        if ($this->db->query($sql)) {
            // Ensure profile entry exists
            $checkProfile = "SELECT uid FROM pi_profile WHERE uid = $uid";
            if ($this->db->query($checkProfile)->num_rows == 0) {
                $insProfile = "INSERT INTO pi_profile (uid, fname, lname) VALUES ($uid, 'SafeZone', 'User')";
                $this->db->query($insProfile);
            }
            return true;
        }
        return false;
    }

    private function getUserEmail($uid)
    {
        // Helper to get email since it wasn't in the initial query
        $sql = "SELECT email FROM pi_account WHERE uid = $uid";
        $res = $this->db->query($sql);
        if ($res && $res->num_rows > 0)
            return $res->fetch_assoc()['email'];
        return '';
    }

    private function isAssetRequest($value)
    {
        $blockedExtensions = ['.shtml', '.ico', '.php', '.png', '.jpg', '.css', '.js'];

        foreach ($blockedExtensions as $extension) {
            if (stripos($value, $extension) !== false) {
                return true;
            }
        }

        return false;
    }
}
