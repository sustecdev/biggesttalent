<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ProfileController extends Controller
{
    public function index()
    {
        // Require authentication
        if (!function_exists('isAuthenticated') || !isAuthenticated()) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['redirect_after_login'] = 'index.php?url=profile';
            header("Location: index.php?url=auth"); // Redirect to auth controller
            exit();
        }

        $userId = (int) $_SESSION['uid'];

        // Handle POST Update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $updateSuccess = false;
            $updateError = '';

            $this->handleProfileUpdate($userId, $updateSuccess, $updateError);

            if ($updateSuccess) {
                $_SESSION['flash_success'] = "Profile updated successfully!";
                header("Location: " . URLROOT . "/index.php?url=profile#settings");
                exit;
            }

            if ($updateError) {
                $_SESSION['flash_error'] = $updateError;
                header("Location: " . URLROOT . "/index.php?url=profile#settings");
                exit;
            }
        }

        // Get Data
        $userProfile = $this->getUserProfile($userId);
        $userNominations = $this->getUserNominations($userId);

        // Get YemChain balance
        $balanceData = null;
        if (function_exists('yemchain_get_user_balance')) {
            $balanceData = yemchain_get_user_balance('DBV');
        }

        // Check for Flash Messages
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;

        // Clear flash messages
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Prepare Data
        $data = [
            'userProfile' => $userProfile,
            'userNominations' => $userNominations,
            'balanceData' => $balanceData,
            'updateSuccess' => $flashSuccess, // Map flash to view var
            'updateError' => $flashError,     // Map flash to view var
            'page_title' => 'Dashboard',
            'hide_nav' => true // Flag to hide navigation
        ];

        $this->view('layouts/header', $data);
        $this->view('profile/index', $data);
        // Footer removed for Profile page as per user request to remove logo
        // $this->view('layouts/footer');
    }

    private function getUserProfile($userId)
    {
        // Use legacy function if available or re-implement
        if (function_exists('getUserProfile')) {
            $profile = getUserProfile($userId);
            if ($profile)
                return $profile;
        }

        // Fallback / Default structure
        return [
            'uid' => $userId,
            'username' => $_SESSION['username'] ?? 'User',
            'email' => $_SESSION['email'] ?? '',
            'fname' => $_SESSION['fname'] ?? '',
            'lname' => $_SESSION['lname'] ?? '',
            'pic' => null,
            'bio' => null,
            'con_id' => null
        ];
    }

    private function getUserNominations($userId)
    {
        if (function_exists('getUserNominations')) {
            return getUserNominations($userId);
        }
        return [];
    }

    private function handleProfileUpdate($userId, &$updateSuccess, &$updateError)
    {
        $db = Database::getInstance()->getConnection();
        $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
        // $bio = $db->real_escape_string($bio); // Prepare statement handles this

        // Handle profile picture upload
        $picPath = null;
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            $uploadDir = 'uploads/profiles/';
            // Ensure dir exists (relative to public usually, need to check where index.php is)
            // Since we are in public/index.php, 'uploads' should be in public/uploads ?? 
            // Original code likely put it in root/uploads. Let's stick to public/uploads for now or root if that's where legacy expects.
            // But legacy code did 'uploads/profiles/'.

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $file = $_FILES['profile_pic'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $picPath = 'uploads/profiles/' . uniqid('profile_') . '.' . $ext;
                if (!move_uploaded_file($file['tmp_name'], $picPath)) {
                    $updateError = 'Failed to upload profile picture.';
                    return;
                }
            } else {
                $updateError = 'Invalid file type or file too large (max 5MB).';
                return;
            }
        } else {
            // Check if existing pic path needs to remain (logic handled in query construction usually)
            // But here we need to know if we are updating pic or not.
            // We get existing profile to check.
            $currentProfile = $this->getUserProfile($userId);
            $picPath = $currentProfile['pic'];
            // Wait, if no file uploaded, rely on logic below to NOT overwrite if we don't pass it?
            // Actually original code logic:
            // if ($picPath) ... prepare update with pic
            // else ... prepare update without pic
            // Here $picPath is from upload.
        }

        // Check if profile exists
        $checkQuery = "SELECT uid FROM pi_profile WHERE uid = $userId";
        $checkResult = $db->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            // Update
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
                // We have a new pic
                $stmt = $db->prepare("UPDATE pi_profile SET bio = ?, pic = ? WHERE uid = ?");
                $stmt->bind_param("ssi", $bio, $picPath, $userId);
            } else {
                $stmt = $db->prepare("UPDATE pi_profile SET bio = ? WHERE uid = ?");
                $stmt->bind_param("si", $bio, $userId);
            }
        } else {
            // Insert
            $stmt = $db->prepare("INSERT INTO pi_profile (uid, bio, pic) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $bio, $picPath);
        }

        if ($stmt->execute()) {
            $updateSuccess = true;
            // Refresh session info if needed
        } else {
            $updateError = 'Failed to update profile: ' . $stmt->error;
        }
        $stmt->close();
    }
}
