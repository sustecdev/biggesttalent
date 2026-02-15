<?php
/**
 * SafeZone Step 2: PIN Verification API Handler
 * Handles the second step of SafeZone 2-step authentication
 * Matches betapp implementation
 * 
 * POST Parameters (JSON or Form):
 * - uid: User ID from Step 1
 * - key: Key (3 digits from 1-6 representing positions)
 * - pin: PIN digits (3 digits from those positions)
 * - pernum: Pernum (optional, for session)
 */

require_once '../app/core/Config.php';
Config::load();
require_once '../app/core/Database.php';
$db = Database::getInstance();
$GLOBALS['mysqli'] = $db->getConnection();
require_once '../legacy/functions.php';
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

    $uid = $data['uid'] ?? '';
    $key = $data['key'] ?? '';
    $pin = $data['pin'] ?? '';
    $pernum = $data['pernum'] ?? '';

    if (empty($uid) || empty($pin) || empty($key)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields: uid, pin, key']);
        exit;
    }

    // Validate PIN is exactly 3 digits
    if (strlen($pin) !== 3 || !ctype_digit($pin)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'PIN must be exactly 3 digits']);
        exit;
    }

    // Validate key is exactly 3 digits (position numbers 1-6)
    if (strlen($key) !== 3 || !preg_match('/^[1-6]{3}$/', $key)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Key must be 3 digits from 1-6 representing positions']);
        exit;
    }

    $uidNum = intval($uid);
    if ($uidNum <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid UID']);
        exit;
    }

    // Call SafeZone PIN verification API (using check_pin_api.php like betapp)
    $pinUrl = 'https://safe.zone/signup/check_pin_api.php';
    $postData = http_build_query([
        'uid' => $uidNum,
        'pin' => $pin,
        'key' => $key,
    ]);

    $ch = curl_init($pinUrl);
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

    // Valid responses (matching betapp logic)
    $isValid = $lowerResponse === 'valid' ||
        $lowerResponse === 'success' ||
        $lowerResponse === 'ok' ||
        $trimmed === '1' ||
        $trimmed === 'true';

    // Invalid responses
    $isInvalid = $lowerResponse === 'invalid' ||
        strpos($lowerResponse, 'pin does not match') !== false ||
        strpos($lowerResponse, 'wrong pin') !== false ||
        $lowerResponse === 'incorrect' ||
        $trimmed === '0' ||
        $trimmed === 'false';

    if ($httpCode !== 200) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'PIN verification request failed',
        ]);
        exit;
    }

    if ($isInvalid || (!$isValid && !empty($trimmed))) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $trimmed ?: 'PIN verification failed',
        ]);
        exit;
    }

    if (!$isValid) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Unexpected response format from SafeZone',
        ]);
        exit;
    }

    // PIN verified successfully - create session
    $_SESSION['uid'] = $uidNum;
    $_SESSION['authenticated'] = true;
    $_SESSION['auth_time'] = time();

    // Resolve pernum before role lookup (username in pi_account is the stable identifier)
    if (!empty($pernum)) {
        $_SESSION['pernum'] = $pernum;
    } else {
        $pernumQuery = "SELECT username FROM pi_account WHERE uid = ? LIMIT 1";
        $pernumStmt = $GLOBALS['mysqli']->prepare($pernumQuery);
        if ($pernumStmt) {
            $pernumStmt->bind_param("i", $uidNum);
            $pernumStmt->execute();
            $pernumResult = $pernumStmt->get_result();
            if ($pernumResult && $pernumResult->num_rows > 0) {
                $row = $pernumResult->fetch_assoc();
                $_SESSION['pernum'] = $row['username'] ?? '';
            }
            $pernumStmt->close();
        }
    }

    $_SESSION['role'] = getUserRoleByUid($uidNum, $_SESSION['pernum'] ?? null);

    // Get user details from database
    $userQuery = "SELECT a.username, a.email, p.fname, p.lname 
                 FROM pi_account a 
                 LEFT JOIN pi_profile p ON a.uid = p.uid 
                 WHERE a.uid = ? 
                 LIMIT 1";

    $stmt = $GLOBALS['mysqli']->prepare($userQuery);
    if ($stmt) {
        $stmt->bind_param("i", $uidNum);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['username'] = $user['username'] ?? '';
            $_SESSION['email'] = $user['email'] ?? '';
            $_SESSION['fname'] = $user['fname'] ?? '';
            $_SESSION['lname'] = $user['lname'] ?? '';
        }
        $stmt->close();
    }

    echo json_encode([
        'success' => true,
        'message' => 'PIN verified successfully',
        'uid' => $uidNum,
        'redirect' => $_SESSION['redirect_after_login'] ?? 'index.php'
    ]);
} catch (Exception $e) {
    error_log('SafeZone PIN verification error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'PIN verification request failed',
    ]);
}
?>