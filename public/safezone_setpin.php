<?php
/**
 * SafeZone Set PIN API Handler
 * Handles PIN setup via SafeZone API
 * 
 * POST Parameters (JSON or Form):
 * - pernum: User's pernum (e.g., "1001295552")
 * - pin: PIN to set (e.g., "111111")
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

    $pernum = trim($data['pernum'] ?? '');
    $pin = trim($data['pin'] ?? '');
    $apiKey = trim($data['apikey'] ?? (defined('SAFEZONE_API_KEY') ? SAFEZONE_API_KEY : '2U3nkfPZHzWFi3LyYcJKItn4HkZeeVU'));

    if (empty($pernum) || empty($pin)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields: pernum, pin']);
        exit;
    }

    // Validate PIN format (typically 4-6 digits)
    if (!preg_match('/^\d{4,6}$/', $pin)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'PIN must be 4-6 digits']);
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
    $lowerResponse = strtolower($trimmed);

    // Valid responses (matching SafeZone API behavior)
    $isValid = $lowerResponse === 'valid' ||
        $lowerResponse === 'success' ||
        $lowerResponse === 'ok' ||
        $trimmed === '1' ||
        $trimmed === 'true' ||
        strpos($lowerResponse, 'pin set') !== false ||
        strpos($lowerResponse, 'successfully') !== false;

    // Invalid responses
    $isInvalid = $lowerResponse === 'invalid' ||
        strpos($lowerResponse, 'error') !== false ||
        strpos($lowerResponse, 'fail') !== false ||
        $trimmed === '0' ||
        $trimmed === 'false';

    if ($httpCode !== 200) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'PIN setup request failed',
        ]);
        exit;
    }

    if ($isInvalid) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $trimmed ?: 'PIN setup failed',
        ]);
        exit;
    }

    if ($isValid) {
        echo json_encode([
            'success' => true,
            'message' => 'PIN set successfully',
            'pernum' => $pernum
        ]);
        exit;
    }

    // Try to parse as JSON
    $result = json_decode($trimmed, true);
    if ($result) {
        if (isset($result['success']) && $result['success']) {
            echo json_encode([
                'success' => true,
                'message' => $result['message'] ?? 'PIN set successfully',
                'pernum' => $pernum
            ]);
            exit;
        } elseif (isset($result['error']) || isset($result['message'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $result['message'] ?? $result['error'] ?? 'PIN setup failed',
            ]);
            exit;
        }
    }

    // Unknown format
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected response format from SafeZone',
        'raw_response' => $trimmed
    ]);
} catch (Exception $e) {
    error_log('SafeZone set PIN error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'PIN setup request failed',
    ]);
}
?>
