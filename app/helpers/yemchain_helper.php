<?php
/**
 * YemChain API Helper
 * Migrated from legacy/yemchain.php
 */

/**
 * Log error to file, creating directory if needed
 * 
 * @param string $message Error message
 * @return void
 */
function yemchain_log(string $message): void {
    $logDir = dirname(dirname(__DIR__)) . '/logs'; // Point to root/logs or similar
    if (!is_dir($logDir)) {
        if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            error_log("YemChain Error: Failed to create log directory. Original error: $message");
            return;
        }
    }
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL, 3, $logDir . '/yemchain.log');
}

/**
 * Get user balance from YemChain API
 * 
 * @param string $uid User ID (without leading 100 if present)
 * @param string $asset Asset type (default: 'DBV')
 * @return array|null Returns balance data array or null on error
 */
function yemchain_get_balance(string $uid, string $asset = 'DBV'): ?array {
    // Get API key from config (Constants should be defined by App\Core\Config)
    if (!defined('YEMCHAIN_API_KEY')) {
        error_log('YEMCHAIN_API_KEY not defined.');
        return null;
    }
    
    $url = 'http://91.98.180.218/api/get-balance.php';
    $apikey = YEMCHAIN_API_KEY;
    
    // Prepare POST data
    $postData = http_build_query([
        'apikey' => $apikey,
        'uid'    => $uid,
        'asset'  => $asset,
    ]);
    
    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_TIMEOUT        => 3,
        CURLOPT_CONNECTTIMEOUT => 2,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Log errors
    if ($response === false || !empty($curlError)) {
        $errorMsg = 'YemChain API cURL error: ' . ($curlError ?: 'Unknown error');
        yemchain_log($errorMsg);
        return null;
    }
    
    if ($httpCode !== 200) {
        $errorMsg = "YemChain API HTTP error: {$httpCode}";
        yemchain_log($errorMsg);
        return null;
    }
    
    // Parse response - API returns format: "success:102" where 102 is the balance
    $response = trim($response);
    
    // Check if response starts with "success:"
    if (strpos($response, 'success:') === 0) {
        // Extract balance number after "success:"
        $balanceStr = substr($response, 8); // Remove "success:" prefix
        $balance = trim($balanceStr);
        
        // Validate that balance is numeric
        if (is_numeric($balance)) {
            return [
                'success' => true,
                'balance' => (float)$balance,
                'raw_response' => $response
            ];
        } else {
            $errorMsg = 'YemChain API: Invalid balance format in response: ' . $response;
            yemchain_log($errorMsg);
            return [
                'success' => false,
                'error' => 'Invalid balance format',
                'raw_response' => $response
            ];
        }
    }
    
    // If response doesn't match expected format, try JSON as fallback
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        return $data;
    }
    
    // If neither format works, log error
    $errorMsg = 'YemChain API: Unexpected response format: ' . substr($response, 0, 100);
    yemchain_log($errorMsg);
    
    return [
        'success' => false,
        'error' => 'Unexpected response format',
        'raw_response' => $response
    ];
}

/**
 * Convert pernum to uid format for YemChain API
 * Strips leading "100" if present (e.g., 1001294626 -> 1294626)
 * 
 * @param string|int $pernum Personal number
 * @return string UID for API
 */
function yemchain_convert_pernum_to_uid($pernum): string {
    $pernumStr = (string)$pernum;
    
    // If pernum starts with "100", remove it
    if (strpos($pernumStr, '100') === 0 && strlen($pernumStr) > 3) {
        return substr($pernumStr, 3);
    }
    
    return $pernumStr;
}

/**
 * Get balance for current logged-in user
 * Automatically uses session uid or pernum
 * 
 * @param string $asset Asset type (default: 'DBV')
 * @return array|null Returns balance data array or null on error
 */
function yemchain_get_user_balance(string $asset = 'DBV'): ?array {
    // Check if user is logged in
    if (!isset($_SESSION['uid']) && !isset($_SESSION['pernum'])) {
        return null;
    }
    
    // Try to get uid from session first
    $uid = $_SESSION['uid'] ?? null;
    
    // If no uid, try to convert pernum to uid
    if (!$uid && isset($_SESSION['pernum'])) {
        $uid = yemchain_convert_pernum_to_uid($_SESSION['pernum']);
    }
    
    if (!$uid) {
        return null;
    }
    
    // Convert uid to string and ensure it's numeric
    $uidStr = (string)$uid;
    
    return yemchain_get_balance($uidStr, $asset);
}
