<?php
/**
 * SafeZone Step 1: Login API Handler
 * Handles the first step of SafeZone 2-step authentication
 * Matches betapp implementation
 * 
 * POST Parameters (JSON or Form):
 * - pernum: User's pernum (e.g., "1001294626")
 * - password: User's password
 * - key: API key (optional, defaults to Dmjfk78Ckjksj23KlmdMMszcX)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Adjust paths for Public directory location
require_once '../app/core/Config.php';
Config::load();
require_once '../app/core/Database.php';
$db = Database::getInstance();
$GLOBALS['mysqli'] = $db->getConnection();
require_once '../app/helpers/functions.php';
require_once '../legacy/yemchain.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set response headers for mobile compatibility
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get input (JSON or form data)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // If not JSON, try form data
    if (!$data) {
        $data = $_POST;
    }

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $pernum = trim($data['pernum'] ?? '');
    $password = trim($data['password'] ?? '');
    $apiKey = trim($data['key'] ?? 'Dmjfk78Ckjksj23KlmdMMszcX');

    if (empty($pernum) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields: pernum, password']);
        exit;
    }

    // Call SafeZone login API
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Network error: ' . $curlError]);
        exit;
    }

    $trimmed = trim($response);

    // Clean the response - SafeZone API sometimes wraps JSON in PHP string() format
    $cleanedResponse = $trimmed;

    // Method 1: Find the first opening brace and extract JSON from there
    $firstBrace = strpos($cleanedResponse, '{');
    if ($firstBrace !== false) {
        $jsonStart = $firstBrace;
        $braceCount = 0;
        $jsonEnd = $jsonStart;

        for ($i = $jsonStart; $i < strlen($cleanedResponse); $i++) {
            if ($cleanedResponse[$i] === '{') {
                $braceCount++;
            } elseif ($cleanedResponse[$i] === '}') {
                $braceCount--;
                if ($braceCount === 0) {
                    $jsonEnd = $i + 1;
                    break;
                }
            }
        }

        if ($braceCount === 0) {
            $cleanedResponse = substr($cleanedResponse, $jsonStart, $jsonEnd - $jsonStart);
        }
    }
    // Method 2: Remove PHP string() wrapper if present
    elseif (preg_match('/^string\(\d+\)\s*"(.*)"\s*$/s', $cleanedResponse, $matches)) {
        $cleanedResponse = stripcslashes($matches[1]);
    }

    $cleanedResponse = trim($cleanedResponse);

    // Parse JSON response
    $result = json_decode($cleanedResponse, true);

    if ($result && isset($result['uid'])) {
        // Successful login
        $uid = is_numeric($result['uid']) ? intval($result['uid']) : intval($result['uid']);

        echo json_encode([
            'success' => true,
            'uid' => $uid,
            'key' => $apiKey,
            'pernum' => $pernum,
            'username' => $result['username'] ?? $pernum,
            'email' => $result['email'] ?? '',
            'member_type' => $result['member_type'] ?? 'premium',
            'verification_status' => $result['verification_status'] ?? 'verified',
            'message' => 'Login successful',
        ]);
        exit;
    }

    // Check for error in response
    if ($result && (isset($result['error']) || isset($result['message']))) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? $result['error'] ?? 'Login failed',
        ]);
        exit;
    }

    // Try to extract UID from non-JSON response
    if (preg_match('/uid[=:]?\s*["\']?(\d+)["\']?/i', $trimmed, $matches)) {
        $uid = intval($matches[1]);
        echo json_encode([
            'success' => true,
            'uid' => $uid,
            'key' => $apiKey,
            'pernum' => $pernum,
            'username' => $pernum,
            'message' => 'Login successful',
        ]);
        exit;
    }

    // Check for error messages
    $lowerResponse = strtolower($trimmed);
    if (
        strpos($lowerResponse, 'error') !== false ||
        strpos($lowerResponse, 'invalid') !== false ||
        strpos($lowerResponse, 'fail') !== false
    ) {
        http_response_code(400);
        $errorMsg = 'Invalid pernum or password. Please check your credentials and try again.';
        if (stripos($trimmed, 'Invalid pernum/password') !== false) {
            $errorMsg = 'Invalid pernum or password. Please check your credentials and try again.';
        }
        echo json_encode([
            'success' => false,
            'message' => $errorMsg,
        ]);
        exit;
    }

    // Unknown format
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected response format from SafeZone',
    ]);
} catch (Exception $e) {
    error_log('SafeZone login error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Login request failed',
    ]);
}
?>