<?php
/**
 * Application Helper Functions
 * Merged from legacy/functions.php and feature-functions.php
 */

// -----------------------------------------------------------------------------
// Legacy Functions (from legacy/functions.php)
// -----------------------------------------------------------------------------

function getActiveSeasonSimple()
{
    if (empty($GLOBALS['mysqli'])) {
        return null;
    }
    $mysqli = $GLOBALS['mysqli'];

    // Ensure bt_seasons has is_active column (migration may not have run)
    $colCheck = $mysqli->query("SHOW COLUMNS FROM bt_seasons LIKE 'is_active'");
    if (!$colCheck || $colCheck->num_rows === 0) {
        $mysqli->query("ALTER TABLE bt_seasons ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 0");
        $mysqli->query("ALTER TABLE bt_seasons ADD INDEX idx_is_active (is_active)");
    }

    // Ensure is_nominations_open and is_voting_open exist
    $colCheck2 = $mysqli->query("SHOW COLUMNS FROM bt_seasons LIKE 'is_nominations_open'");
    if (!$colCheck2 || $colCheck2->num_rows === 0) {
        $mysqli->query("ALTER TABLE bt_seasons ADD COLUMN is_nominations_open TINYINT(1) DEFAULT 1");
    }
    $colCheck3 = $mysqli->query("SHOW COLUMNS FROM bt_seasons LIKE 'is_voting_open'");
    if (!$colCheck3 || $colCheck3->num_rows === 0) {
        $mysqli->query("ALTER TABLE bt_seasons ADD COLUMN is_voting_open TINYINT(1) DEFAULT 1");
    }

    $query = "SELECT * FROM bt_seasons WHERE is_active = 1 LIMIT 1";
    $result = $mysqli->query($query);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    // Fallback: return most recent season when none is active (so user always sees a season)
    $fallback = $mysqli->query("SELECT * FROM bt_seasons ORDER BY start_date DESC LIMIT 1");
    if ($fallback && $fallback->num_rows > 0) {
        return $fallback->fetch_assoc();
    }

    return null;
}

function getStageConstraints($seasonId, $stage)
{
    $join = "";
    $where = "";
    $seasonId = (int) $seasonId;
    $stage = $GLOBALS['mysqli']->real_escape_string($stage);

    if ($stage === 'national') {
        // Only filter by season_id if column exists
        $colCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'season_id'");
        if ($colCheck && $colCheck->num_rows > 0) {
            $where = " AND (n.season_id = $seasonId OR n.season_id IS NULL) ";
        }
    } else if (in_array($stage, ['round_1', 'round_2', 'round_3'])) {
        $join = " INNER JOIN bt_contest_stage_qualifiers q ON n.id = q.nomination_id AND q.stage = '$stage' AND q.season_id = $seasonId ";
    }

    return ['join' => $join, 'where' => $where];
}

function getNumbersFromText($inp)
{
    $result = array();
    $inp = strtolower($inp);
    $keypad = array(
        'a' => '2',
        'b' => '2',
        'c' => '2',
        'd' => '3',
        'e' => '3',
        'f' => '3',
        'g' => '4',
        'h' => '4',
        'i' => '4',
        'j' => '5',
        'k' => '5',
        'l' => '5',
        'm' => '6',
        'n' => '6',
        'o' => '6',
        'p' => '7',
        'q' => '7',
        'r' => '7',
        's' => '7',
        't' => '8',
        'u' => '8',
        'v' => '8',
        'w' => '9',
        'x' => '9',
        'y' => '9',
        'z' => '9',
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9'
    );

    for ($x = 0; $x < strlen($inp); $x++) {
        $letter = $inp[$x];
        if ($letter == '0') {
            $result[] = '0';
        } elseif (isset($keypad[$letter])) {
            $result[] = $keypad[$letter];
        }
    }
    return implode('', $result);
}


function getTotRecords($field, $table, $where)
{
    $select = "SELECT " . $field . " FROM `" . $table . "` " . $where;
    $rows = $GLOBALS['mysqli']->query($select) or die($GLOBALS['mysqli']->error . __LINE__);
    return $rows->num_rows;
}

function getSingleValue($table, $where, $field)
{
    $select = "SELECT " . $field . " FROM `" . $table . "` " . $where;
    $res = $GLOBALS['mysqli']->query($select) or die($GLOBALS['mysqli']->error . __LINE__);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return $row[$field];
    } else {
        return '';
    }
}

function getRows($table, $where, $field)
{
    $select = "SELECT " . $field . " FROM `" . $table . "` " . $where;
    $res = $GLOBALS['mysqli']->query($select) or die($GLOBALS['mysqli']->error . __LINE__);
    return $res;
}

function userLogout()
{
    @session_start();

    // Unset all of the session variables.
    $_SESSION = array();
    $time = time() - 3600;
    setcookie("uid", '', $time, "/");
    setcookie("password", '', $time, "/");
    setcookie("token", '', $time, "/");

    // Finally, destroy the session.
    $_SESSION = array();
}

function GetIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return $ip;
}


function login($uid, $password, $autologin = 0)
{
    if ($autologin == 1) {
        $uid = $GLOBALS['mysqli']->real_escape_string($uid);
        $pwd = $GLOBALS['mysqli']->real_escape_string($password);

        $select = "SELECT a.url, a.psm, a.psa, a.factor_fixed, a.pin,a.invitedby,a.username,a.password,a.uid,a.email,a.role,p.mname, p.fname,p.lname,p.gender,p.con_id,p.address,p.city,p.state,p.zip,p.dob,p.pic,a.deal_points FROM pi_account a inner join pi_profile p on a.uid=p.uid  WHERE (a.uid='$uid') AND (a.password='$pwd')";
    } else {
        // Manual login logic (if any specific, otherwise fallthrough?)
        // The original legacy function was weirdly truncated/structured with empty else.
        // Assuming $select needs to be defined if not autologin?
        // Ah, original code had issues? Let's assume standard query if not defined.
        $uid = $GLOBALS['mysqli']->real_escape_string($uid);
        $pwd = $GLOBALS['mysqli']->real_escape_string($password);
        $select = "SELECT a.url, a.psm, a.psa, a.factor_fixed, a.pin,a.invitedby,a.username,a.password,a.uid,a.email,a.role,p.mname, p.fname,p.lname,p.gender,p.con_id,p.address,p.city,p.state,p.zip,p.dob,p.pic,a.deal_points FROM pi_account a inner join pi_profile p on a.uid=p.uid  WHERE (a.uid='$uid') AND (a.password='$pwd')";
    }
    $res = $GLOBALS['mysqli']->query($select) or die($GLOBALS['mysqli']->error . __LINE__);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        // Extract row to vars (Legacy style)
        extract($row);

        $_SESSION['uid'] = $uid;
        if (!empty($username)) {
            $_SESSION['pernum'] = $username;
        }
        $_SESSION['role'] = getUserRoleByUid((int) $uid, $username ?? null);

        $_SESSION['url'] = $url ?? '';
        $_SESSION['psm'] = $psm ?? '';
        $_SESSION['psa'] = $psa ?? '';
        $_SESSION['factor'] = $factor_fixed ?? '';

        $_SESSION['invitedby'] = $invitedby ?? '';

        $_SESSION['fname'] = $fname ?? '';
        $_SESSION['lname'] = $lname ?? '';
        $_SESSION['mname'] = $mname ?? '';
        $_SESSION['username'] = $username ?? '';
        $_SESSION['email'] = $email ?? '';
        $_SESSION['gender'] = $gender ?? '';
        $_SESSION['con_id'] = $con_id ?? '';
        $_SESSION['country'] = $con_id ?? '';

        $addressParts = explode('||', $address ?? '');
        $_SESSION['address'] = $addressParts[0] ?? '';
        $_SESSION['address2'] = $addressParts[1] ?? '';
        $_SESSION['city'] = $city ?? '';
        $_SESSION['state'] = $state ?? '';
        $_SESSION['zip'] = $zip ?? '';
        $_SESSION['dob'] = $dob ?? '';

        $remember = 1;
        $time = time() + 3600 * 24 * 365 * 10;

        setcookie("uid", $uid, $time, "/", '.biggesttalent.africa'); // Updated domain

        $token = $uid . "||" . $password . "||" . $_SERVER['REMOTE_ADDR'];
        $cipher_method = 'AES-128-CTR';
        // Need php_uname() or constant? Using fallback
        $enc_key = openssl_digest(php_uname(), '@786ALLAHISTHEGREATEST786@', TRUE);
        $enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
        $crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
        unset($token, $cipher_method, $enc_key, $enc_iv);

        setcookie("token", $crypted_token, $time, "/", '.biggesttalent.africa');
        setcookie("remember", $remember, $time, "/", '.biggesttalent.africa');

        $_SESSION['password'] = $password;
        return true;
    } else {
        userLogout();
        return false;
    }
}

function isAuthenticated(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['uid'])) {
        return false;
    }
    $uid = (int) $_SESSION['uid'];
    return $uid > 0;
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        if (!isset($_SESSION['redirect_after_login'])) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            $_SESSION['redirect_after_login'] = $currentPage;
        }
        header("Location: safezone.php?redirect=" . urlencode($_SESSION['redirect_after_login']));
        exit;
    }
}

/**
 * Get user role from pi_account. Uses pernum (username) as primary lookup when available,
 * since SafeZone may return a different uid than stored (e.g. 1290033 vs 1001290033).
 *
 * @param int $uid User ID from session
 * @param string|null $pernum Optional pernum/username - if provided, used for lookup (stable identifier)
 * @return string 'admin' or 'user'
 */
function getUserRoleByUid(int $uid, ?string $pernum = null): string
{
    // Hardcoded admin - always admin regardless of DB
    if ($uid === 1001290033) {
        return 'admin';
    }

    if (empty($GLOBALS['mysqli'])) {
        return 'user';
    }

    $pernum = $pernum ?? ($_SESSION['pernum'] ?? '');
    $pernum = trim((string) $pernum);
    $role = null;

    // Primary: Lookup by username (pernum) - stable identifier, avoids SafeZone uid vs stored uid mismatch
    if ($pernum !== '') {
        $pernumEsc = $GLOBALS['mysqli']->real_escape_string($pernum);
        $role = getSingleValue('pi_account', "WHERE username='$pernumEsc'", 'role');
    }

    // Fallback: Lookup by uid
    if (($role === '' || $role === null) && $uid > 0) {
        $role = getSingleValue('pi_account', "WHERE uid='$uid'", 'role');
    }

    return ($role === '' || $role === null) ? 'user' : $role;
}

/**
 * Sync $_SESSION['role'] from database. Call once per request when user is logged in.
 * Handles SafeZone uid/pernum mismatch by using getUserRoleByUid.
 */
function syncSessionRole(): void
{
    if (!isset($_SESSION['uid']) || !$_SESSION['uid']) {
        return;
    }
    if (!function_exists('getUserRoleByUid')) {
        return;
    }
    $_SESSION['role'] = getUserRoleByUid(
        (int) $_SESSION['uid'],
        $_SESSION['pernum'] ?? null
    );
}

/**
 * Check if current user has admin or super_admin role. Syncs from DB if needed.
 *
 * @return bool
 */
function isAdmin(): bool
{
    if (!isset($_SESSION['uid']) || !$_SESSION['uid']) {
        return false;
    }
    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, ['admin', 'super_admin'], true) && function_exists('getUserRoleByUid')) {
        syncSessionRole();
        $role = $_SESSION['role'] ?? '';
    }
    return in_array($role, ['admin', 'super_admin'], true);
}

/**
 * Require admin access. Redirects to home if not admin/super_admin.
 * Use in admin controllers and dashboard.
 */
function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: ' . (defined('URLROOT') ? URLROOT : '/'));
        exit;
    }
}

/**
 * Pre-add a user as admin by SafeZone username (pernum). They don't need to sign in first.
 * When they first log in via SafeZone, they'll already have admin access.
 *
 * @param string $username SafeZone username (pernum)
 * @return bool Success
 */
function addAdminByUsername(string $username): bool
{
    if (empty($GLOBALS['mysqli'])) {
        return false;
    }
    $mysqli = $GLOBALS['mysqli'];
    $username = trim($username);
    if ($username === '') {
        return false;
    }
    $usernameEsc = $mysqli->real_escape_string($username);

    // Check if user already exists (has logged in)
    $existing = $mysqli->query("SELECT uid, role FROM pi_account WHERE username='$usernameEsc' LIMIT 1");
    if ($existing && $existing->num_rows > 0) {
        $mysqli->query("UPDATE pi_account SET role='admin' WHERE username='$usernameEsc'");
        return true;
    }

    // Pre-create placeholder: use negative uid to avoid collision with SafeZone uids
    $placeholderUid = -abs(crc32($username)) - 1;
    $placeholderPw = bin2hex(random_bytes(16));
    $pwEsc = $mysqli->real_escape_string($placeholderPw);

    $sql = "INSERT INTO pi_account (uid, username, password, email, role) VALUES ($placeholderUid, '$usernameEsc', '$pwEsc', '', 'admin')";
    if (!$mysqli->query($sql)) {
        return false;
    }
    $mysqli->query("INSERT INTO pi_profile (uid, fname, lname) VALUES ($placeholderUid, 'Admin', 'User')");
    return true;
}

/**
 * Ensure user exists in pi_account and pi_profile. Called when SafeZone validates credentials.
 * If a pre-created admin (by addAdminByUsername) exists for this username, merges it and preserves admin role.
 *
 * @param mysqli $mysqli Database connection
 * @param int $uid User ID from SafeZone
 * @param string $pernum Pernum (username)
 * @param string $password Plain password (SafeZone validates; we store for legacy login)
 */
function ensureUserInDb($mysqli, int $uid, string $pernum, string $password): void
{
    if (!$mysqli) {
        error_log('ensureUserInDb: no mysqli connection');
        return;
    }
    $uid = (int) $uid;
    $pernumSafe = $mysqli->real_escape_string($pernum);
    $passwordSafe = $mysqli->real_escape_string($password);

    // Check for pre-created admin (placeholder with negative uid)
    $existing = $mysqli->query("SELECT uid, role FROM pi_account WHERE username='$pernumSafe' LIMIT 1");
    if ($existing && $existing->num_rows > 0) {
        $row = $existing->fetch_assoc();
        $existingUid = (int) $row['uid'];
        $existingRole = $row['role'] ?? 'user';

        if ($existingUid === $uid) {
            $mysqli->query("UPDATE pi_account SET password='$passwordSafe' WHERE uid=$uid");
        } elseif ($existingUid < 0) {
            // Pre-created: replace placeholder with real uid, preserve role
            $mysqli->query("DELETE FROM pi_account WHERE uid=$existingUid");
            $mysqli->query("DELETE FROM pi_profile WHERE uid=$existingUid");
            $role = in_array($existingRole, ['admin', 'super_admin'], true) ? $existingRole : 'user';
            $mysqli->query("INSERT INTO pi_account (uid, username, password, email, role) VALUES ($uid, '$pernumSafe', '$passwordSafe', '', '$role')");
            $mysqli->query("INSERT INTO pi_profile (uid, fname, lname) VALUES ($uid, 'SafeZone', 'User')");
        } else {
            $mysqli->query("UPDATE pi_account SET password='$passwordSafe' WHERE uid=$uid");
        }
        return;
    }

    $sql = "INSERT INTO pi_account (uid, username, password, email, role) VALUES ($uid, '$pernumSafe', '$passwordSafe', '', 'user') ON DUPLICATE KEY UPDATE password='$passwordSafe'";
    if (!$mysqli->query($sql)) {
        error_log('ensureUserInDb INSERT failed: ' . $mysqli->error . ' | uid=' . $uid . ' | pernum=' . $pernum);
        return;
    }
    $checkProfile = $mysqli->query("SELECT uid FROM pi_profile WHERE uid = $uid");
    if (!$checkProfile || $checkProfile->num_rows == 0) {
        $mysqli->query("INSERT INTO pi_profile (uid, fname, lname) VALUES ($uid, 'SafeZone', 'User')");
    }
}

// -----------------------------------------------------------------------------
// Feature Functions (from feature-functions.php)
// -----------------------------------------------------------------------------

function getCategories(): array
{
    // Check if table exists
    $tableCheck = "SHOW TABLES LIKE 'bt_categories'";
    $tableResult = $GLOBALS['mysqli']->query($tableCheck);

    if (!$tableResult || $tableResult->num_rows == 0) {
        return [];
    }

    $query = "SELECT * FROM bt_categories WHERE is_active = 1 ORDER BY name ASC";
    $result = $GLOBALS['mysqli']->query($query);
    $categories = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

function getCategoryById(int $id): ?array
{
    $id = (int) $id;
    $query = "SELECT * FROM bt_categories WHERE id = $id AND is_active = 1";
    $result = $GLOBALS['mysqli']->query($query);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function getContests(): array
{
    // Check if table exists
    $tableCheck = "SHOW TABLES LIKE 'bt_contests'";
    $tableResult = $GLOBALS['mysqli']->query($tableCheck);

    if (!$tableResult || $tableResult->num_rows == 0) {
        return [];
    }

    $query = "SELECT * FROM bt_contests WHERE is_active = 1 ORDER BY start_date DESC";
    $result = $GLOBALS['mysqli']->query($query);
    $contests = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $contests[] = $row;
        }
    }
    return $contests;
}

function getCurrentContest(): ?array
{
    // Check if table exists
    $tableCheck = "SHOW TABLES LIKE 'bt_contests'";
    $tableResult = $GLOBALS['mysqli']->query($tableCheck);

    if (!$tableResult || $tableResult->num_rows == 0) {
        return null;
    }

    $today = date('Y-m-d');
    $query = "SELECT * FROM bt_contests WHERE is_active = 1 AND start_date <= '$today' AND end_date >= '$today' ORDER BY start_date DESC LIMIT 1";
    $result = $GLOBALS['mysqli']->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function getWeeklyLeaderboard(int $limit = 50): array
{
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $weekEnd = date('Y-m-d', strtotime('sunday this week'));

    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";
    $voteStageFilter = "";  // New: Filter votes by stage as well?
    // NOTE: Usually leaderboard shows votes for *that stage*.
    // If we want to show ALL votes regardless of stage, we leave vote filter empty.
    // But typically for "Round 1" we only count votes cast IN Round 1?
    // User requirement: "National Voting: Selecting top 32... Country Battles: ... Finals".
    // Implies votes reset or are specific to stage?
    // "Vote.php" updates suggest we separate votes by stage.
    // So ensuring we only count votes for the current stage is safer/correct.

    if ($season) {
        $constraints = getStageConstraints($season['id'], $season['current_stage']);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];

        // Filter votes by current stage if we are in a specific stage
        $currentStage = $season['current_stage'];
        $voteStageFilter = " AND v.stage = '$currentStage' ";
    }

    $query = "SELECT n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id,
              COUNT(v.id) as vote_count,
              c.name as category_name
              FROM bt_nominations n
              $stageJoin
              LEFT JOIN bt_votes v ON n.id = v.nomination_id AND DATE(v.date) >= '$weekStart' AND DATE(v.date) <= '$weekEnd' $voteStageFilter
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved' $stageWhere
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
              ORDER BY vote_count DESC, n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $leaderboard = [];

    if ($result && $result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $row['rank'] = $rank++;
            $leaderboard[] = $row;
        }
    }
    return $leaderboard;
}

function getMonthlyLeaderboard(int $limit = 50): array
{
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');

    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";
    $voteStageFilter = "";

    if ($season) {
        $constraints = getStageConstraints($season['id'], $season['current_stage']);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];
        $currentStage = $season['current_stage'];
        $voteStageFilter = " AND v.stage = '$currentStage' ";
    }

    $query = "SELECT n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id,
              COUNT(v.id) as vote_count,
              c.name as category_name
              FROM bt_nominations n
              $stageJoin
              LEFT JOIN bt_votes v ON n.id = v.nomination_id AND DATE(v.date) >= '$monthStart' AND DATE(v.date) <= '$monthEnd' $voteStageFilter
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved' $stageWhere
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
              ORDER BY vote_count DESC, n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $leaderboard = [];

    if ($result && $result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $row['rank'] = $rank++;
            $leaderboard[] = $row;
        }
    }
    return $leaderboard;
}

function getAllTimeLeaderboard(int $limit = 50): array
{
    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";
    $voteStageFilter = "";

    if ($season) {
        $constraints = getStageConstraints($season['id'], $season['current_stage']);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];
        $currentStage = $season['current_stage'];
        $voteStageFilter = " AND v.stage = '$currentStage' ";
    }

    $query = "SELECT n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id,
              COUNT(v.id) as vote_count,
              c.name as category_name
              FROM bt_nominations n
              $stageJoin
              LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteStageFilter
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved' $stageWhere
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
              ORDER BY vote_count DESC, n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $leaderboard = [];

    if ($result && $result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $row['rank'] = $rank++;
            $leaderboard[] = $row;
        }
    }
    return $leaderboard;
}

function searchNominations(string $query, int $limit = 50): array
{
    $searchTerm = $GLOBALS['mysqli']->real_escape_string($query);

    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";

    if ($season) {
        $constraints = getStageConstraints($season['id'], $season['current_stage']);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];
    }

    $sql = "SELECT n.*, 
            COUNT(v.id) as vote_count,
            c.name as category_name
            FROM bt_nominations n
            $stageJoin
            LEFT JOIN bt_votes v ON n.id = v.nomination_id
            LEFT JOIN bt_categories c ON n.category_id = c.id
            WHERE n.status = 'approved' $stageWhere
            AND (
                n.aname LIKE '%$searchTerm%' 
                OR n.title LIKE '%$searchTerm%' 
                OR n.description LIKE '%$searchTerm%'
                OR n.country LIKE '%$searchTerm%'
            )
            GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
            ORDER BY vote_count DESC, n.date DESC
            LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($sql);
    $results = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
    return $results;
}

function getNominationsByCategory(int $categoryId, int $limit = 50): array
{
    $categoryId = (int) $categoryId;

    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";

    if ($season) {
        $constraints = getStageConstraints($season['id'], $season['current_stage']);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];
    }

    $query = "SELECT n.*, 
              COUNT(v.id) as vote_count,
              c.name as category_name
              FROM bt_nominations n
              $stageJoin
              LEFT JOIN bt_votes v ON n.id = v.nomination_id
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved' $stageWhere AND n.category_id = $categoryId
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
              ORDER BY vote_count DESC, n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $nominations = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nominations[] = $row;
        }
    }
    return $nominations;
}

function getNominationsByCountry(string $country, int $limit = 50): array
{
    $country = $GLOBALS['mysqli']->real_escape_string($country);

    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";

    if ($season) {
        $constraints = getStageConstraints($season['id'], $season['current_stage']);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];
    }

    $query = "SELECT n.*, 
              COUNT(v.id) as vote_count,
              c.name as category_name
              FROM bt_nominations n
              $stageJoin
              LEFT JOIN bt_votes v ON n.id = v.nomination_id
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved' $stageWhere AND n.country = '$country'
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
              ORDER BY vote_count DESC, n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $nominations = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nominations[] = $row;
        }
    }
    return $nominations;
}

function getLatestNominations(int $limit = 50): array
{
    $query = "SELECT n.*, 
              COUNT(v.id) as vote_count,
              c.name as category_name
              FROM bt_nominations n
              LEFT JOIN bt_votes v ON n.id = v.nomination_id
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved'
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
              ORDER BY n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $nominations = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nominations[] = $row;
        }
    }
    return $nominations;
}

function getTrendingNominations(int $limit = 50): array
{
    $weekAgo = date('Y-m-d', strtotime('-7 days'));

    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";

    if ($season) {
        $constraints = getStageConstraints($season['id'], $season['current_stage']);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];
    }

    $query = "SELECT n.*, 
              COUNT(v.id) as vote_count,
              COUNT(CASE WHEN v.date >= '$weekAgo' THEN 1 END) as recent_votes,
              c.name as category_name
              FROM bt_nominations n
              $stageJoin
              LEFT JOIN bt_votes v ON n.id = v.nomination_id
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved' $stageWhere
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail, n.category_id, c.name
              HAVING recent_votes > 0
              ORDER BY recent_votes DESC, vote_count DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $nominations = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nominations[] = $row;
        }
    }
    return $nominations;
}

function reportNomination(int $nominationId, string $reason, string $details = '', int $userId = null): bool
{
    $nominationId = (int) $nominationId;
    $reason = $GLOBALS['mysqli']->real_escape_string($reason);
    $details = $GLOBALS['mysqli']->real_escape_string($details);
    $userId = $userId ? (int) $userId : 'NULL';
    $ipAddress = GetIP();

    // Check if table exists
    $tableCheck = "SHOW TABLES LIKE 'bt_reports'";
    $tableResult = $GLOBALS['mysqli']->query($tableCheck);
    if (!$tableResult || $tableResult->num_rows == 0) {
        return false;
    }

    $query = "INSERT INTO bt_reports (nomination_id, user_id, ip_address, reason, details, status) 
              VALUES ($nominationId, $userId, '$ipAddress', '$reason', '$details', 'pending')";

    if ($GLOBALS['mysqli']->query($query)) {
        // Update reports count on nomination
        $updateQuery = "UPDATE bt_nominations SET reports_count = reports_count + 1 WHERE id = $nominationId";
        $GLOBALS['mysqli']->query($updateQuery);

        // Auto-flag if reports threshold reached (5 or more reports)
        $checkQuery = "SELECT reports_count FROM bt_nominations WHERE id = $nominationId";
        $checkResult = $GLOBALS['mysqli']->query($checkQuery);
        if ($checkResult && $checkResult->num_rows > 0) {
            $row = $checkResult->fetch_assoc();
            $reportsCount = (int) ($row['reports_count'] ?? 0);

            // Auto-flag if 5 or more reports
            if ($reportsCount >= 5) {
                // Update nomination status to 'pending' for review (if it was approved)
                $flagQuery = "UPDATE bt_nominations SET status = 'pending', rejection_reason = 'Auto-flagged: Multiple reports received' WHERE id = $nominationId AND status = 'approved'";
                $GLOBALS['mysqli']->query($flagQuery);
            }
        }
        return true;
    }
    return false;
}

function getUserProfile(int $userId): ?array
{
    $userId = (int) $userId;

    $query = "SELECT a.uid, a.username, a.email, a.role, a.created_at,
              p.fname, p.lname, p.mname, p.con_id, p.gender, p.pic, p.bio, p.email_verified
              FROM pi_account a
              LEFT JOIN pi_profile p ON a.uid = p.uid
              WHERE a.uid = $userId";

    $result = $GLOBALS['mysqli']->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function getUserNominations(int $userId, int $limit = 50): array
{
    $userId = (int) $userId;
    $profileGroup = '';
    $colCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'profile_photo'");
    if ($colCheck && $colCheck->num_rows > 0) {
        $profileGroup = ', n.profile_photo';
    }

    $query = "SELECT n.*, 
              COUNT(v.id) as vote_count,
              c.name as category_name
              FROM bt_nominations n
              LEFT JOIN bt_votes v ON n.id = v.nomination_id
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.uid = $userId
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail $profileGroup, n.category_id, c.name
              ORDER BY n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $nominations = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nominations[] = $row;
        }
    }
    return $nominations;
}

// -----------------------------------------------------------------------------
// Missing Functions (Restored)
// -----------------------------------------------------------------------------

function getApprovedContestantsWithVotes(int $limit = 200): array
{
    // Contestants are approved nominations for the current voting stage
    // National = all approved; round_1/2/3 = only qualifiers from bt_contest_stage_qualifiers

    $season = getActiveSeasonSimple();
    $stageWhere = "";
    $stageJoin = "";
    $voteStageFilter = "";
    $currentStage = 'national';

    if ($season) {
        $currentStage = $season['current_stage'] ?? 'national';
        $currentStage = $GLOBALS['mysqli']->real_escape_string($currentStage);
        $constraints = getStageConstraints($season['id'], $currentStage);
        $stageJoin = $constraints['join'];
        $stageWhere = $constraints['where'];
        // Only filter votes by stage if bt_votes has stage column
        $stageColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_votes LIKE 'stage'");
        if ($stageColCheck && $stageColCheck->num_rows > 0) {
            $voteStageFilter = " AND (v.stage = '$currentStage' OR v.stage IS NULL) ";
        }
    }

    // Ensure profile_photo column exists for voting cards
    $profileColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'profile_photo'");
    if (!$profileColCheck || $profileColCheck->num_rows === 0) {
        $GLOBALS['mysqli']->query("ALTER TABLE bt_nominations ADD COLUMN profile_photo VARCHAR(500) DEFAULT NULL AFTER thumbnail");
    }
    $profileColCheck2 = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'profile_photo'");
    $hasProfilePhoto = ($profileColCheck2 && $profileColCheck2->num_rows > 0);
    $profileSelect = $hasProfilePhoto ? ", n.profile_photo" : "";

    $groupByExtra = $hasProfilePhoto ? ", n.profile_photo" : "";
    $descColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'description'");
    $provColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'province'");
    $descSelect = ($descColCheck && $descColCheck->num_rows > 0) ? ", n.description" : "";
    $provSelect = ($provColCheck && $provColCheck->num_rows > 0) ? ", n.province" : "";
    $groupByDesc = ($descColCheck && $descColCheck->num_rows > 0) ? ", n.description" : "";
    $groupByProv = ($provColCheck && $provColCheck->num_rows > 0) ? ", n.province" : "";
    $query = "SELECT n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail $profileSelect $descSelect $provSelect, n.category_id, n.date,
              COUNT(v.id) as vote_count,
              c.name as category_name,
              n.country as country_name
              FROM bt_nominations n
              $stageJoin
              LEFT JOIN bt_votes v ON n.id = v.nomination_id $voteStageFilter
              LEFT JOIN bt_categories c ON n.category_id = c.id
              WHERE n.status = 'approved' $stageWhere
              GROUP BY n.id, n.aname, n.title, n.country, n.vlink, n.video_file, n.thumbnail $groupByExtra $groupByDesc $groupByProv, n.category_id, n.date, c.name
              ORDER BY vote_count DESC, n.date DESC
              LIMIT $limit";

    $result = $GLOBALS['mysqli']->query($query);
    $contestants = [];

    if ($result && $result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $row['rank'] = $rank++;
            $contestants[] = $row;
        }
    }
    return $contestants;
}

function getVotesToday(int $nominationId, int $userId, string $userIp): int
{
    $nominationId = (int) $nominationId;
    $userId = (int) $userId;
    $userIp = $GLOBALS['mysqli']->real_escape_string($userIp);
    $today = date('Y-m-d');

    $whereClause = "";
    if ($userId > 0) {
        $whereClause = "(uid = $userId OR ip_address = '$userIp')";
    } else {
        $whereClause = "(ip_address = '$userIp')";
    }

    $query = "SELECT COUNT(*) as cnt FROM bt_votes 
              WHERE nomination_id = $nominationId 
              AND DATE(date) = '$today' 
              AND $whereClause";

    $result = $GLOBALS['mysqli']->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return (int) $row['cnt'];
    }
    return 0;
}
