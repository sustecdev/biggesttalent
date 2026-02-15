<?php
/**
 * SafeZone Signup API Handler
 * Handles user registration via SafeZone API
 * 
 * POST Parameters (JSON or Form):
 * - email: User's email address
 * - password: User's password
 * - invited_by: Referrer's pernum (optional)
 * - apikey: API key (optional, defaults to SAFEZONE_API_KEY)
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

    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');
    $invitedBy = trim($data['invited_by'] ?? '');
    $apiKey = trim($data['apikey'] ?? (defined('SAFEZONE_API_KEY') ? SAFEZONE_API_KEY : '2U3nkfPZHzWFi3LyYcJKItn4HkZeeVU'));

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields: email, password']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    // Call SafeZone signup API
    $signupUrl = 'https://safe.zone/api/signup_api.php';
    
    // Prepare form data
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

    // Check for success - SafeZone signup typically returns pernum on success
    if ($result && (isset($result['pernum']) || isset($result['success']))) {
        $pernum = $result['pernum'] ?? null;
        
        // If pernum is in the response, signup was successful
        if ($pernum) {
            echo json_encode([
                'success' => true,
                'pernum' => $pernum,
                'message' => 'Registration successful',
                'data' => $result
            ]);
            exit;
        }
        
        // Check if success flag is set
        if (isset($result['success']) && $result['success']) {
            echo json_encode([
                'success' => true,
                'pernum' => $result['pernum'] ?? null,
                'message' => $result['message'] ?? 'Registration successful',
                'data' => $result
            ]);
            exit;
        }
    }

    // Try to extract pernum from non-JSON response
    if (preg_match('/pernum[=:]?\s*["\']?(\d+)["\']?/i', $trimmed, $matches)) {
        $pernum = $matches[1];
        echo json_encode([
            'success' => true,
            'pernum' => $pernum,
            'message' => 'Registration successful',
        ]);
        exit;
    }

    // Check for error in response
    if ($result && (isset($result['error']) || isset($result['message']))) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? $result['error'] ?? 'Registration failed',
        ]);
        exit;
    }

    // Check for error messages in text response
    $lowerResponse = strtolower($trimmed);
    if (
        strpos($lowerResponse, 'error') !== false ||
        strpos($lowerResponse, 'invalid') !== false ||
        strpos($lowerResponse, 'fail') !== false ||
        strpos($lowerResponse, 'already') !== false ||
        strpos($lowerResponse, 'exists') !== false
    ) {
        http_response_code(400);
        $errorMsg = 'Registration failed. ' . $trimmed;
        echo json_encode([
            'success' => false,
            'message' => $errorMsg,
        ]);
        exit;
    }

    // Unknown format - return raw response for debugging
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected response format from SafeZone',
        'raw_response' => $trimmed
    ]);
} catch (Exception $e) {
    error_log('SafeZone signup error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Registration request failed',
    ]);
}
?>
