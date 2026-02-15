<?php

namespace App\Controllers;

use App\Core\Controller;

class VoteController extends Controller
{
    public function __construct()
    {
        // Legacy functions loaded via index.php/helpers
    }
    public function index()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Require authentication
        if (!function_exists('isAuthenticated') || !isAuthenticated()) {
            $_SESSION['redirect_after_login'] = 'vote';
            $this->redirect('auth');
        }

        // Load legacy logic
        $data = [
            'contestants' => [],
            'userIP' => $_SERVER['REMOTE_ADDR'],
            'userID' => $_SESSION['uid'] ?? 0
        ];

        // Populate User Profile Data for Sidebar/Header
        $userProfile = [
            'username' => $_SESSION['username'] ?? 'User',
            'fname' => $_SESSION['fname'] ?? '',
            'lname' => $_SESSION['lname'] ?? '',
            'pernum' => $_SESSION['pernum'] ?? '',
            'uid' => $_SESSION['uid'] ?? 0,
            'pic' => $_SESSION['pic'] ?? null
        ];

        $data['userProfile'] = $userProfile;
        $data['page_title'] = 'Vote';

        // Fetch active season
        $activeSeason = null;
        try {
            $seasonModel = $this->model('Season');
            $activeSeason = $seasonModel->getActiveSeason();
        } catch (\Exception $e) {
            // If season model fails, continue without season info
        }
        $data['activeSeason'] = $activeSeason;

        // Check if Voting is Open (show page with message if closed, instead of redirecting)
        $data['voting_closed'] = ($activeSeason && isset($activeSeason['is_voting_open']) && (int) $activeSeason['is_voting_open'] === 0);

        // Mock getting contestants (should be from Model)
        // For now, we are relying on functional calls or returning empty if functions not available
        if (function_exists('getApprovedContestantsWithVotes')) {
            $data['contestants'] = getApprovedContestantsWithVotes();
        }

        // Hide footer logo on vote page
        $data['hide_footer_logo'] = true;
        // Hide navigation on vote page
        $data['hide_nav'] = true;

        $this->view('layouts/header', $data);
        $this->view('vote/index', $data);
        // Footer removed for Vote page as per user request to remove logo
        // $this->view('layouts/footer', $data);
    }

    private function redirect($url)
    {
        header('Location: ' . URLROOT . '/' . $url);
        exit;
    }
}
