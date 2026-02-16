<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\MailService;

class SafezoneController extends Controller
{
    public function index()
    {
        // Store redirect if provided
        if (isset($_GET['redirect'])) {
            $_SESSION['redirect_after_login'] = $_GET['redirect'];
        }

        // Check if already authenticated
        if (function_exists('isAuthenticated') && isAuthenticated()) {
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?url=profile';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        }

        $data = [
            'ref_pernum' => $_COOKIE['ref_pernum'] ?? '',
            // Dynamic domain path
            'safezoneDomain' => $_SERVER['HTTP_HOST'] . str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME'])
        ];

        // Clean up domain path if needed (remove 'public' if it's there twice or something)
        // ideally it should point to root of app.
        // For xampp it's usually localhost/path/to/app
        // We will just use a hardcoded fallback if needed or the one from config
        if (defined('URLROOT')) {
            $data['safezone_domain'] = str_replace('http://', '', str_replace('https://', '', URLROOT));
        }

        $this->view('layouts/header', $data);
        $this->view('auth/safezone', $data);
        // $this->view('layouts/footer');
    }

    public function signupForm()
    {
        // Check if already authenticated
        if (function_exists('isAuthenticated') && isAuthenticated()) {
            header("Location: " . URLROOT . "/index.php");
            exit;
        }

        $data = [
            'ref_pernum' => $_COOKIE['ref_pernum'] ?? $_GET['invited_by'] ?? '',
        ];

        $this->view('layouts/header', $data);
        $this->view('auth/signup', $data);
        // $this->view('layouts/footer');
    }

    public function setPinForm()
    {
        // Check if already authenticated
        if (function_exists('isAuthenticated') && isAuthenticated()) {
            header("Location: " . URLROOT . "/index.php");
            exit;
        }

        $pernum = $_GET['pernum'] ?? '';

        if (empty($pernum)) {
            header("Location: " . URLROOT . "/safezone/signupForm");
            exit;
        }

        $data = [
            'pernum' => $pernum,
        ];

        $this->view('layouts/header', $data);
        $this->view('auth/setpin', $data);
        // $this->view('layouts/footer');
    }

    public function login()
    {
        // Headers for JSON response
        header('Content-Type: application/json');

        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input)
            $input = $_POST;

        $pernum = trim($input['pernum'] ?? '');
        $password = trim($input['password'] ?? '');
        $apiKey = trim($input['key'] ?? 'Dmjfk78Ckjksj23KlmdMMszcX');

        if (empty($pernum) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Call SafeZone API
        $loginUrl = 'https://safe.zone/signup/login_api.php';
        $postData = http_build_query([
            'pernum' => $pernum,
            'password' => $password,
            'key' => $apiKey,
        ]);

        $ch = curl_init($loginUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For localhost testing often needed
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo json_encode(['success' => false, 'message' => 'Network error: ' . $curlError]);
            exit;
        }

        // Clean response (legacy logic)
        $cleanedResponse = trim($response);
        $firstBrace = strpos($cleanedResponse, '{');
        if ($firstBrace !== false) {
            $cleanedResponse = substr($cleanedResponse, $firstBrace);
            // Find last brace
            $lastBrace = strrpos($cleanedResponse, '}');
            if ($lastBrace !== false) {
                $cleanedResponse = substr($cleanedResponse, 0, $lastBrace + 1);
            }
        } else if (preg_match('/^string\(\d+\)\s*"(.*)"\s*$/s', $cleanedResponse, $matches)) {
            $cleanedResponse = stripcslashes($matches[1]);
        }

        $result = json_decode($cleanedResponse, true);

        if ($result && isset($result['uid'])) {
            $uid = is_numeric($result['uid']) ? (int) $result['uid'] : (int) $result['uid'];
            if (function_exists('ensureUserInDb') && !empty($GLOBALS['mysqli'])) {
                ensureUserInDb($GLOBALS['mysqli'], $uid, $pernum, $password);
            }
            echo json_encode([
                'success' => true,
                'uid' => $result['uid'],
                'key' => $apiKey,
                'pernum' => $pernum,
                'username' => $result['username'] ?? $pernum
            ]);
        } else {
            // Try to extract UID via regex if JSON failed
            if (preg_match('/uid[=:]?\s*["\']?(\d+)["\']?/i', $response, $matches)) {
                $uid = (int) $matches[1];
                if (function_exists('ensureUserInDb') && !empty($GLOBALS['mysqli'])) {
                    ensureUserInDb($GLOBALS['mysqli'], $uid, $pernum, $password);
                }
                echo json_encode([
                    'success' => true,
                    'uid' => $matches[1],
                    'key' => $apiKey,
                    'pernum' => $pernum,
                    'username' => $pernum
                ]);
            } else {
                $errorMsg = (is_array($result) && isset($result['message'])) ? $result['message'] : 'Login failed';
                echo json_encode(['success' => false, 'message' => $errorMsg]);
            }
        }
    }

    public function verifyPin()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE)
            session_start();

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input)
            $input = $_POST;

        $uid = $input['uid'] ?? '';
        $key = $input['key'] ?? '';
        $pin = $input['pin'] ?? '';
        $pernum = $input['pernum'] ?? '';

        if (empty($uid) || empty($pin) || empty($key)) {
            echo json_encode(['success' => false, 'message' => 'Missing fields']);
            exit;
        }

        // Call SafeZone PIN API
        $pinUrl = 'https://safe.zone/signup/check_pin_api.php';
        $postData = http_build_query([
            'uid' => $uid,
            'pin' => $pin,
            'key' => $key,
        ]);

        $ch = curl_init($pinUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);

        $trimmed = trim($response);
        $lower = strtolower($trimmed);
        $isValid = ($lower === 'valid' || $lower === 'success' || $lower === 'ok' || $trimmed === '1' || $trimmed === 'true');

        if ($isValid) {
            // Set Session (pernum first so role lookup can use stable identifier)
            $_SESSION['uid'] = $uid;
            $_SESSION['authenticated'] = true;
            $_SESSION['auth_time'] = time();
            if (!empty($pernum)) {
                $_SESSION['pernum'] = $pernum;
            }

            // Get Role - pass pernum for reliable lookup (SafeZone uid may differ from stored uid)
            if (function_exists('getUserRoleByUid')) {
                $_SESSION['role'] = getUserRoleByUid((int) $uid, $_SESSION['pernum'] ?? null);
            } else {
                $_SESSION['role'] = 'user';
            }

            // Redirect
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?url=profile';
            unset($_SESSION['redirect_after_login']);

            // Ensure full path if it's just a filename
            if (strpos($redirect, '/') === false) {
                $redirect = URLROOT . '/' . $redirect;
            }

            echo json_encode([
                'success' => true,
                'redirect' => $redirect
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid PIN'
            ]);
        }
    }

    /**
     * Send OTP to email for verification (before signup)
     */
    public function sendOtp()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $email = trim($input['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Valid email required']);
            exit;
        }

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $mail = new MailService();
        if (!$mail->sendOtp($email, $otp)) {
            echo json_encode(['success' => false, 'message' => 'Failed to send verification code. Please try again.']);
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['signup_otp_email'] = $email;
        $_SESSION['signup_otp_code'] = $otp;
        $_SESSION['signup_otp_expiry'] = time() + 600; // 10 min

        echo json_encode(['success' => true, 'message' => 'Verification code sent to your email']);
    }

    /**
     * Verify OTP before allowing signup
     */
    public function verifyOtp()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $email = trim($input['email'] ?? '');
        $otp = trim($input['otp'] ?? '');

        if (empty($email) || empty($otp)) {
            echo json_encode(['success' => false, 'message' => 'Email and OTP required']);
            exit;
        }

        $sessionEmail = $_SESSION['signup_otp_email'] ?? '';
        $sessionOtp = $_SESSION['signup_otp_code'] ?? '';
        $sessionExpiry = $_SESSION['signup_otp_expiry'] ?? 0;

        if ($sessionEmail !== $email || $sessionOtp !== $otp) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired verification code']);
            exit;
        }
        if (time() > $sessionExpiry) {
            unset($_SESSION['signup_otp_email'], $_SESSION['signup_otp_code'], $_SESSION['signup_otp_expiry']);
            echo json_encode(['success' => false, 'message' => 'Verification code expired. Request a new one.']);
            exit;
        }

        $_SESSION['signup_email_verified'] = $email;
        unset($_SESSION['signup_otp_code'], $_SESSION['signup_otp_expiry']);

        echo json_encode(['success' => true, 'message' => 'Email verified']);
    }

    public function signup()
    {
        // Headers for JSON response
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input)
            $input = $_POST;

        $email = trim($input['email'] ?? '');
        $password = trim($input['password'] ?? '');
        $invitedBy = trim($input['invited_by'] ?? '');
        $apiKey = trim($input['apikey'] ?? (defined('SAFEZONE_API_KEY') ? SAFEZONE_API_KEY : '2U3nkfPZHzWFi3LyYcJKItn4HkZeeVU'));

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields: email, password']);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        // Require email verification via OTP
        $verifiedEmail = $_SESSION['signup_email_verified'] ?? '';
        if ($verifiedEmail !== $email) {
            echo json_encode(['success' => false, 'message' => 'Please verify your email first. Enter the code sent to your email.']);
            exit;
        }

        // Call SafeZone signup API
        $signupUrl = 'https://safe.zone/api/signup_api.php';

        $postData = [
            'apikey' => $apiKey,
            'email' => $email,
            'password' => $password,
        ];

        if (!empty($invitedBy)) {
            $postData['invited_by'] = $invitedBy;
        }

        $ch = curl_init($signupUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo json_encode(['success' => false, 'message' => 'Network error: ' . $curlError]);
            exit;
        }

        // Clean response
        $cleanedResponse = trim($response);
        $firstBrace = strpos($cleanedResponse, '{');
        if ($firstBrace !== false) {
            $cleanedResponse = substr($cleanedResponse, $firstBrace);
            $lastBrace = strrpos($cleanedResponse, '}');
            if ($lastBrace !== false) {
                $cleanedResponse = substr($cleanedResponse, 0, $lastBrace + 1);
            }
        } else if (preg_match('/^string\(\d+\)\s*"(.*)"\s*$/s', $cleanedResponse, $matches)) {
            $cleanedResponse = stripcslashes($matches[1]);
        }

        $result = json_decode($cleanedResponse, true);

        if ($result && isset($result['status']) && $result['status'] === 'success' && isset($result['details']['pernum'])) {
            $pernum = $result['details']['pernum'];
            $_SESSION['signup_email'] = $email;
            $_SESSION['signup_password'] = $password;
            echo json_encode([
                'success' => true,
                'pernum' => $pernum,
                'message' => $result['message'] ?? 'Registration successful',
                'data' => $result
            ]);
        } else if ($result && isset($result['pernum']) && !empty($result['pernum'])) {
            $pernum = $result['pernum'];
            $_SESSION['signup_email'] = $email;
            $_SESSION['signup_password'] = $password;
            echo json_encode([
                'success' => true,
                'pernum' => $pernum,
                'message' => $result['message'] ?? 'Registration successful',
                'data' => $result
            ]);
        } else if (preg_match('/pernum[=:]?\s*["\']?(\d+)["\']?/i', $response, $matches)) {
            $_SESSION['signup_email'] = $email;
            $_SESSION['signup_password'] = $password;
            echo json_encode([
                'success' => true,
                'pernum' => $matches[1],
                'message' => 'Registration successful'
            ]);
        } else {
            $errorMsg = (is_array($result) && (isset($result['message']) || isset($result['error']))) 
                        ? ($result['message'] ?? $result['error']) 
                        : 'Registration failed';
            echo json_encode([
                'success' => false,
                'message' => $errorMsg
            ]);
        }
    }

    public function setPin()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input)
            $input = $_POST;

        $pernum = trim($input['pernum'] ?? '');
        $pin = trim($input['pin'] ?? '');
        $apiKey = trim($input['apikey'] ?? (defined('SAFEZONE_API_KEY') ? SAFEZONE_API_KEY : '2U3nkfPZHzWFi3LyYcJKItn4HkZeeVU'));

        if (empty($pernum) || empty($pin)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields: pernum, pin']);
            exit;
        }

        // Validate PIN format - must be exactly 6 digits
        if (!preg_match('/^\d{6}$/', $pin)) {
            echo json_encode(['success' => false, 'message' => 'Master PIN must be exactly 6 digits']);
            exit;
        }

        // Call SafeZone set PIN API
        $setPinUrl = 'https://safe.zone/api/setmp_api.php';
        $postData = http_build_query([
            'apikey' => $apiKey,
            'pernum' => $pernum,
            'pin' => $pin,
        ]);

        $ch = curl_init($setPinUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo json_encode(['success' => false, 'message' => 'Network error: ' . $curlError]);
            exit;
        }

        // Clean and parse response
        $cleanedResponse = trim($response);
        $firstBrace = strpos($cleanedResponse, '{');
        if ($firstBrace !== false) {
            $cleanedResponse = substr($cleanedResponse, $firstBrace);
            $lastBrace = strrpos($cleanedResponse, '}');
            if ($lastBrace !== false) {
                $cleanedResponse = substr($cleanedResponse, 0, $lastBrace + 1);
            }
        }

        $result = json_decode($cleanedResponse, true);

        $pinSetSuccess = false;
        // Check for new API format: {"status": "success", "details": {...}}
        if ($result && isset($result['status']) && $result['status'] === 'success') {
            $pinSetSuccess = true;
        }
        // Check for old success formats
        else if ($result && isset($result['success']) && $result['success']) {
            $pinSetSuccess = true;
        }
        // Legacy text-based success check
        else {
            $lower = strtolower($cleanedResponse);
            $isValid = ($lower === 'valid' || $lower === 'success' || $lower === 'ok' ||
                $cleanedResponse === '1' || $cleanedResponse === 'true' ||
                strpos($lower, 'pin set') !== false ||
                strpos($lower, 'successfully') !== false);
            if ($isValid) {
                $pinSetSuccess = true;
            }
        }

        if ($pinSetSuccess) {
            // Send credentials email (pernum, master pin, password)
            $email = $_SESSION['signup_email'] ?? '';
            $password = $_SESSION['signup_password'] ?? '';
            if (!empty($email) && !empty($password)) {
                $mail = new MailService();
                $mail->sendCredentials($email, $pernum, $pin, $password);
                unset($_SESSION['signup_email'], $_SESSION['signup_password']);
            }
            unset($_SESSION['signup_email_verified'], $_SESSION['signup_otp_email']);

            $msg = $result['message'] ?? 'Master PIN set successfully';
            if (is_array($result)) {
                $msg = $result['message'] ?? 'Master PIN set successfully';
            }
            echo json_encode([
                'success' => true,
                'message' => $msg,
                'pernum' => $pernum
            ]);
        } else {
            $errorMsg = (is_array($result) && (isset($result['message']) || isset($result['error'])))
                ? ($result['message'] ?? $result['error'])
                : 'PIN setup failed';
            echo json_encode([
                'success' => false,
                'message' => $errorMsg
            ]);
        }
    }
}
