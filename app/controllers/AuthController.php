<?php

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    public function index()
    {
        // Redirect legacy /auth to new /safezone
        header("Location: " . URLROOT . "/safezone");
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all session values
        $_SESSION = [];
        session_unset();

        // Destroy the session
        session_destroy();

        // Clear Cookies for various paths and domains
        $cookies = ['uid', 'password', 'remember', 'token', 'PHPSESSID'];
        $domains = [$this->getCookieDomain(), '', 'localhost'];

        foreach ($cookies as $cookie) {
            foreach ($domains as $domain) {
                $this->clearCookie($cookie, $domain);
            }
        }

        $this->view('auth/logout');
    }

    private function getCookieDomain()
    {
        return getenv('SESSION_COOKIE_DOMAIN') ?: '.biggesttalent.world';
    }

    private function clearCookie($name, $domain = '')
    {
        // Clear for root path
        if (empty($domain)) {
            setcookie($name, '', time() - 3600, '/');
        } else {
            setcookie($name, '', time() - 3600, '/', $domain);
            // Also try clearing for www subdomain variant just in case
            setcookie($name, '', time() - 3600, '/', str_replace('.', 'www.', $domain));
        }
    }

    /**
     * Show Registration Step 1
     */
    public function register()
    {
        // Redirect legacy /auth/register to /safezone (which handles signup toggle)
        header("Location: " . URLROOT . "/safezone");
        exit;
    }

    /**
     * Process Registration Step 1: Sign Up API
     */
    public function process_register()
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/auth/register");
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $invitedBy = $_POST['invited_by'] ?? '';

        if (empty($email) || empty($password) || empty($confirmPassword)) {
            header("Location: " . URLROOT . "/auth/register?error=" . urlencode('Email, password and confirm password are required'));
            exit;
        }

        if ($password !== $confirmPassword) {
            header("Location: " . URLROOT . "/auth/register?error=" . urlencode('Passwords do not match'));
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . URLROOT . "/auth/register?error=" . urlencode('Invalid email format'));
            exit;
        }

        // Use API key from config
        $apiKey = defined('SAFEZONE_REGISTRATION_API_KEY') ? SAFEZONE_REGISTRATION_API_KEY : (defined('SAFEZONE_API_KEY') ? SAFEZONE_API_KEY : '2U3nkfPZHzWFi3LyYcJKItn4HkZeeVU');

        // Prepare POST data (match Postman "formdata" exactly)
        // Always send invited_by key (Postman includes it)
        $postData = [
            'apikey' => $apiKey,
            'email' => $email,
            'password' => $password,
            'invited_by' => $invitedBy,
        ];

        // Call Signup API
        $url = 'https://safe.zone/api/signup_api.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        // Array format = multipart/form-data (same as Postman form-data)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // Avoid "Expect: 100-continue" issues on some servers
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect:']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Debug logging
        error_log("Signup API Request URL: " . $url);
        error_log("Signup API Request Data: " . print_r($postData, true));
        error_log("Signup API HTTP Code: " . $httpCode);
        error_log("Signup API Response: " . $response);
        error_log("Signup API cURL Error: " . ($error ?: 'None'));

        if ($error) {
            header("Location: " . URLROOT . "/auth/register?error=" . urlencode('Connection error: ' . $error));
            exit;
        }

        // Try to decode JSON response (some responses include BOM / whitespace)
        $cleanResponse = trim((string) $response);
        $cleanResponse = preg_replace('/^\xEF\xBB\xBF/', '', $cleanResponse); // strip UTF-8 BOM if present
        $result = json_decode($cleanResponse, true);

        // If JSON decode failed, check if response contains error message
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());

            // Fallback: try extracting pernum/uid from raw response
            $uid = '';
            $pernum = '';
            if (preg_match('/"uid"\s*:\s*"?(\\d+)"?/i', $cleanResponse, $m)) {
                $uid = $m[1];
            } elseif (preg_match('/\\buid\\b\\s*[=:]\\s*"?(\\d+)"?/i', $cleanResponse, $m)) {
                $uid = $m[1];
            }
            if (preg_match('/"pernum"\s*:\s*"?(\\d+)"?/i', $cleanResponse, $m)) {
                $pernum = $m[1];
            } elseif (preg_match('/\\bpernum\\b\\s*[=:]\\s*"?(\\d+)"?/i', $cleanResponse, $m)) {
                $pernum = $m[1];
            }

            if ($uid !== '' && $pernum !== '') {
                $_SESSION['register_email'] = $email;
                $_SESSION['register_password'] = $password;
                $_SESSION['register_uid'] = $uid;
                $_SESSION['register_pernum'] = $pernum;
                header("Location: " . URLROOT . "/auth/register_pin");
                exit;
            }

            // Otherwise treat as plain-text error
            $plain = trim($cleanResponse);
            if ($plain !== '') {
                header("Location: " . URLROOT . "/auth/register?error=" . urlencode($plain));
            } else {
                header("Location: " . URLROOT . "/auth/register?error=" . urlencode('Invalid API response. Please try again.'));
            }
            exit;
        }

        if (isset($result['status']) && $result['status'] === 'success') {
            // Success! Store details for step 2
            $_SESSION['register_email'] = $email;
            $_SESSION['register_password'] = $password;
            $_SESSION['register_uid'] = $result['details']['uid'] ?? $result['uid'] ?? '';
            $_SESSION['register_pernum'] = $result['details']['pernum'] ?? $result['pernum'] ?? '';

            if (empty($_SESSION['register_pernum'])) {
                $msg = $result['message'] ?? 'Registration succeeded but PERNUM was not returned.';
                header("Location: " . URLROOT . "/auth/register?error=" . urlencode($msg));
                exit;
            }

            header("Location: " . URLROOT . "/auth/register_pin");
            exit;
        } else {
            $msg = $result['message'] ?? ($result['error'] ?? $cleanResponse ?? 'Registration failed. Please try again.');
            header("Location: " . URLROOT . "/auth/register?error=" . urlencode($msg));
            exit;
        }
    }

    /**
     * Show Registration Step 2: Set PIN
     */
    public function register_pin()
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['register_uid']) || !isset($_SESSION['register_pernum'])) {
            header("Location: " . URLROOT . "/auth/register?error=" . urlencode('Session expired. Please start over.'));
            exit;
        }

        $error = isset($_GET['error']) ? $_GET['error'] : null;
        $data = ['error' => $error];

        $this->view('layouts/header', $data);
        $this->view('auth/register_pin', $data);
        $this->view('layouts/footer');
    }

    /**
     * Process Registration Step 2: Set PIN API
     */
    public function process_pin()
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/auth/register_pin");
            exit;
        }

        if (!isset($_SESSION['register_uid']) || !isset($_SESSION['register_pernum'])) {
            header("Location: " . URLROOT . "/auth/register?error=" . urlencode('Session expired.'));
            exit;
        }

        $pin = $_POST['pin'] ?? '';
        $pernum = $_SESSION['register_pernum'];

        if (strlen($pin) !== 6 || !ctype_digit($pin)) {
            header("Location: " . URLROOT . "/auth/register_pin?error=" . urlencode('PIN must be exactly 6 digits'));
            exit;
        }

        // Match SZ Signup Postman collection exactly: apikey, pernum, pin (form-data)
        $apiKey = defined('SAFEZONE_REGISTRATION_API_KEY')
            ? SAFEZONE_REGISTRATION_API_KEY
            : (defined('SAFEZONE_API_KEY') ? SAFEZONE_API_KEY : '2U3nkfPZHzWFi3LyYcJKItn4HkZeeVU');

        $postData = [
            'apikey' => $apiKey,
            'pernum' => $pernum,
            'pin' => $pin,
        ];

        // Call Set PIN API
        $url = 'https://safe.zone/api/setmp_api.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        // Array = multipart/form-data, same as Postman "formdata" mode
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // No explicit Content-Type header; let cURL set multipart/form-data like Postman
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Debug logging
        error_log("Set PIN API Request URL: " . $url);
        error_log("Set PIN API Request Data: " . print_r($postData, true));
        error_log("Set PIN API HTTP Code: " . $httpCode);
        error_log("Set PIN API Response: " . $response);
        error_log("Set PIN API cURL Error: " . ($error ?: 'None'));

        if ($error) {
            header("Location: " . URLROOT . "/auth/register_pin?error=" . urlencode('Connection error: ' . $error));
            exit;
        }

        $result = json_decode($response, true);

        // If JSON decode failed, check if response contains error message
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            // Check if response is a plain text error
            if (stripos($response, 'error') !== false || stripos($response, 'invalid') !== false || stripos($response, 'failed') !== false) {
                header("Location: " . URLROOT . "/auth/register_pin?error=" . urlencode(trim($response)));
            } else {
                header("Location: " . URLROOT . "/auth/register_pin?error=" . urlencode('Invalid API response. Please try again.'));
            }
            exit;
        }

        if (isset($result['status']) && $result['status'] === 'success') {
            // Success! Send Creds via Email
            $this->sendCredentialsEmail(
                $_SESSION['register_email'],
                $pernum,
                $_SESSION['register_uid'],
                $_SESSION['register_password']
            );

            // Clear registration session
            unset($_SESSION['register_email']);
            unset($_SESSION['register_password']);
            unset($_SESSION['register_uid']);
            unset($_SESSION['register_pernum']);

            // Redirect to success page
            header("Location: " . URLROOT . "/auth/registration_success");
            exit;
        } else {
            $msg = $result['message'] ?? ($result['error'] ?? 'Failed to set PIN. Please try again.');
            header("Location: " . URLROOT . "/auth/register_pin?error=" . urlencode($msg));
            exit;
        }
    }

    /**
     * Send Credentials via Email
     */
    private function sendCredentialsEmail($to, $pernum, $uid, $password)
    {
        try {
            require_once __DIR__ . '/../services/EmailService.php';
            $emailService = new EmailService();
            $emailService->sendSafeZoneCredentials($to, $pernum, $uid, $password);
        } catch (Exception $e) {
            error_log("Failed to send credentials email: " . $e->getMessage());
        }
    }

    /**
     * Show Registration Success Page
     */
    public function registration_success()
    {
        $data = [];
        $this->view('layouts/header', $data);
        $this->view('auth/registration_success', $data);
        $this->view('layouts/footer');
    }
}
