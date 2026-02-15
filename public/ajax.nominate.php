<?php
session_start();
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

// Load Config
Config::load();

// Init DB connection
$db = Database::getInstance();
$GLOBALS['mysqli'] = $db->getConnection();

require_once '../app/helpers/functions.php';

// Require authentication
if (!isAuthenticated()) {
    print "Please login";
    exit;
}

// Set charset
if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli']) {
    $GLOBALS['mysqli']->set_charset("utf8");
}

// Get and validate input
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$category = isset($_POST['category']) ? (int) $_POST['category'] : 0;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$country = isset($_POST['country']) ? trim($_POST['country']) : '';
$province = isset($_POST['province']) ? trim($_POST['province']) : '';
$aname = isset($_POST['aname']) ? trim($_POST['aname']) : '';
$nomineeEmail = isset($_POST['nominee_email']) ? trim($_POST['nominee_email']) : '';
$nomineePhone = isset($_POST['nominee_phone']) ? trim($_POST['nominee_phone']) : '';
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$age = isset($_POST['age']) ? (int) $_POST['age'] : 0;
$uploadType = isset($_POST['upload_type']) ? $_POST['upload_type'] : 'link';
$vlink = isset($_POST['vlink']) ? trim($_POST['vlink']) : '';
$uid = isset($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;

// Validation
if (empty($title)) {
    print "Please enter a performance title.";
    exit;
}

if (strlen($title) > 255) {
    print "Title is too long (maximum 255 characters).";
    exit;
}

if ($category <= 0) {
    print "Please select a category.";
    exit;
}

// Verify category exists
$catCheck = "SELECT id FROM bt_categories WHERE id = $category AND is_active = 1";
$catResult = $GLOBALS['mysqli']->query($catCheck);
if (!$catResult || $catResult->num_rows == 0) {
    print "Invalid category selected.";
    exit;
}

if (strlen($description) > 1000) {
    print "Description is too long (maximum 1000 characters).";
    exit;
}

if (empty($country)) {
    print "Please select country.";
    exit;
}

if (empty($aname)) {
    print "Please enter artist name.";
    exit;
}

if (strlen($aname) > 255) {
    print "Artist name is too long.";
    exit;
}

$allowedGenders = ['Male', 'Female', 'Non-binary', 'Other', 'Prefer not to say'];
if (empty($gender) || !in_array($gender, $allowedGenders, true)) {
    print "Please select a valid gender.";
    exit;
}

if ($age < 1 || $age > 120) {
    print "Please enter a valid age (1–120 years).";
    exit;
}

if (!empty($nomineeEmail) && !filter_var($nomineeEmail, FILTER_VALIDATE_EMAIL)) {
    print "Please enter a valid email for the nominee.";
    exit;
}

if (!empty($nomineePhone) && strlen($nomineePhone) > 30) {
    print "Phone number is too long.";
    exit;
}

if ($uid <= 0) {
    print "Invalid user session.";
    exit;
}

// Video validation
$videoFile = null;
$videoFilePath = null;
$thumbnailPath = null;
$videoDuration = null;

if ($uploadType === 'file') {
    // Handle file upload
    if (!isset($_FILES['video_file'])) {
        print "No file was uploaded. Please select a video file.";
        exit;
    }

    // Check for upload errors
    $uploadError = $_FILES['video_file']['error'];
    if ($uploadError !== UPLOAD_ERR_OK) {
        switch ($uploadError) {
            case UPLOAD_ERR_INI_SIZE:
                print "File exceeds upload_max_filesize in php.ini (currently " . ini_get('upload_max_filesize') . ")";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                print "File exceeds MAX_FILE_SIZE in HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                print "File was only partially uploaded. Please try again.";
                break;
            case UPLOAD_ERR_NO_FILE:
                print "No file was selected. Please choose a video file.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                print "Server error: Missing temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                print "Server error: Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                print "Server error: File upload stopped by extension";
                break;
            default:
                print "Unknown upload error (code: $uploadError)";
        }
        exit;
    }

    $videoFile = $_FILES['video_file'];

    // Validate file size (100MB)
    if ($videoFile['size'] > 100 * 1024 * 1024) {
        print "File size must be less than 100MB.";
        exit;
    }

    // Validate file type
    $allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-m4v'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $videoFile['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        print "Invalid file type. Please upload an MP4 video file.";
        exit;
    }

    // Create upload directory
    $uploadDir = 'uploads/videos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($videoFile['name'], PATHINFO_EXTENSION);
    $filename = uniqid('video_') . '_' . time() . '.' . $extension;
    $videoFilePath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($videoFile['tmp_name'], $videoFilePath)) {
        print "Failed to upload video file.";
        exit;
    }

    // Get video duration (basic - would need ffmpeg for accurate duration)
    // For now, we'll set it to NULL and let admin set it manually if needed
    $videoDuration = null;

    // Generate thumbnail (simplified - would need ffmpeg for proper thumbnails)
    // For now, we'll use a placeholder or extract first frame later
    $thumbnailPath = null; // TODO: Implement thumbnail generation

} else {
    // Handle video link
    if (empty($vlink)) {
        print "Please enter a video link.";
        exit;
    }

    // Validate URL format
    if (!filter_var($vlink, FILTER_VALIDATE_URL)) {
        print "Please enter a valid video link URL.";
        exit;
    }

    if (strlen($vlink) > 500) {
        print "Video link is too long.";
        exit;
    }
}

// Profile picture (optional)
$profilePhotoPath = null;
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $profileFile = $_FILES['profile_photo'];
    if ($profileFile['size'] > 2 * 1024 * 1024) {
        print "Profile picture must be less than 2MB.";
        exit;
    }
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $profileFile['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mimeType, $allowedImageTypes)) {
        print "Profile picture must be JPG, PNG, WebP or GIF.";
        exit;
    }
    $profileDir = 'uploads/profiles/';
    if (!is_dir($profileDir)) {
        mkdir($profileDir, 0755, true);
    }
    $ext = pathinfo($profileFile['name'], PATHINFO_EXTENSION) ?: 'jpg';
    $profileFilename = uniqid('profile_') . '_' . time() . '.' . $ext;
    $profilePhotoPath = $profileDir . $profileFilename;
    if (!move_uploaded_file($profileFile['tmp_name'], $profilePhotoPath)) {
        print "Failed to upload profile picture.";
        exit;
    }
}

// Ensure profile_photo column exists
$colCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'profile_photo'");
if (!$colCheck || $colCheck->num_rows === 0) {
    $GLOBALS['mysqli']->query("ALTER TABLE bt_nominations ADD COLUMN profile_photo VARCHAR(500) DEFAULT NULL AFTER thumbnail");
}

// Ensure season_id column exists
$seasonColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'season_id'");
if (!$seasonColCheck || $seasonColCheck->num_rows === 0) {
    $GLOBALS['mysqli']->query("ALTER TABLE bt_nominations ADD COLUMN season_id INT NULL AFTER contest_id");
}

// Ensure gender and age columns exist
$genderColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'gender'");
if (!$genderColCheck || $genderColCheck->num_rows === 0) {
    $GLOBALS['mysqli']->query("ALTER TABLE bt_nominations ADD COLUMN gender VARCHAR(50) DEFAULT NULL AFTER aname");
}
$ageColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'age'");
if (!$ageColCheck || $ageColCheck->num_rows === 0) {
    $GLOBALS['mysqli']->query("ALTER TABLE bt_nominations ADD COLUMN age INT NULL DEFAULT NULL AFTER gender");
}
$nomineeEmailColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'nominee_email'");
if (!$nomineeEmailColCheck || $nomineeEmailColCheck->num_rows === 0) {
    $GLOBALS['mysqli']->query("ALTER TABLE bt_nominations ADD COLUMN nominee_email VARCHAR(255) DEFAULT NULL AFTER age");
}
$nomineePhoneColCheck = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM bt_nominations LIKE 'nominee_phone'");
if (!$nomineePhoneColCheck || $nomineePhoneColCheck->num_rows === 0) {
    $GLOBALS['mysqli']->query("ALTER TABLE bt_nominations ADD COLUMN nominee_phone VARCHAR(30) DEFAULT NULL AFTER nominee_email");
}

// Get current contest if available
$currentContest = getCurrentContest();
$contestId = $currentContest ? $currentContest['id'] : null;

// Prepare data for insertion
$titleEscaped = $GLOBALS['mysqli']->real_escape_string($title);
$descriptionEscaped = $GLOBALS['mysqli']->real_escape_string($description);
$anameEscaped = $GLOBALS['mysqli']->real_escape_string($aname);
$nomineeEmailEscaped = $nomineeEmail ? $GLOBALS['mysqli']->real_escape_string($nomineeEmail) : '';
$nomineePhoneEscaped = $nomineePhone ? $GLOBALS['mysqli']->real_escape_string($nomineePhone) : '';
$genderEscaped = $GLOBALS['mysqli']->real_escape_string($gender);
// Resolve country: form may send id or name; store name in bt_nominations
$countryName = $country;
if (is_numeric($country)) {
    $res = $GLOBALS['mysqli']->query("SELECT name FROM countries WHERE id = " . (int) $country . " LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $countryName = $res->fetch_assoc()['name'];
    }
}
$countryEscaped = $GLOBALS['mysqli']->real_escape_string($countryName);
$provinceEscaped = $province ? $GLOBALS['mysqli']->real_escape_string($province) : '';
$vlinkEscaped = $uploadType === 'link' ? $GLOBALS['mysqli']->real_escape_string($vlink) : '';
$videoFileEscaped = $uploadType === 'file' ? $GLOBALS['mysqli']->real_escape_string($videoFilePath) : '';
$thumbnailEscaped = $thumbnailPath ? $GLOBALS['mysqli']->real_escape_string($thumbnailPath) : '';
$profilePhotoEscaped = $profilePhotoPath ? $GLOBALS['mysqli']->real_escape_string($profilePhotoPath) : '';
$videoDurationVal = !empty($videoDuration) ? (int) $videoDuration : null;
$contestIdVal = !empty($contestId) ? (int) $contestId : null;

// Get active season
$seasonModel = new App\Models\Season();
$activeSeason = $seasonModel->getActiveSeason();
$seasonIdVal = $activeSeason ? (int) $activeSeason['id'] : null;

// One nomination per account: check if user already has a nomination
$existingCheck = $GLOBALS['mysqli']->prepare("SELECT 1 FROM bt_nominations WHERE uid = ? LIMIT 1");
if ($existingCheck) {
    $existingCheck->bind_param("i", $uid);
    $existingCheck->execute();
    $existingRes = $existingCheck->get_result();
    if ($existingRes && $existingRes->num_rows > 0) {
        $existingCheck->close();
        print "You can only nominate one person per account. You have already submitted a nomination.";
        exit;
    }
    $existingCheck->close();
}

// Insert into database using prepared statement
$stmt = $GLOBALS['mysqli']->prepare("INSERT INTO bt_nominations 
    (uid, title, aname, gender, age, nominee_email, nominee_phone, category_id, description, country, province, vlink, video_file, thumbnail, profile_photo, video_duration, contest_id, season_id, date, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");

if (!$stmt) {
    print "Database error: " . $GLOBALS['mysqli']->error;
    exit;
}

$bindTypes = 'i' . 'sss' . 'i' . 'ss' . 'i' . 'sssssss' . 'iii';
$stmt->bind_param(
    $bindTypes,
    $uid,
    $titleEscaped,
    $anameEscaped,
    $genderEscaped,
    $age,
    $nomineeEmailEscaped,
    $nomineePhoneEscaped,
    $category,
    $descriptionEscaped,
    $countryEscaped,
    $provinceEscaped,
    $vlinkEscaped,
    $videoFileEscaped,
    $thumbnailEscaped,
    $profilePhotoEscaped,
    $videoDurationVal,
    $contestIdVal,
    $seasonIdVal
);

// Execute query
if ($stmt->execute()) {
    $nominationId = $stmt->insert_id;
    $stmt->close();

    // If file upload, also insert into bt_video_uploads table
    if ($uploadType === 'file' && $videoFilePath) {
        $originalFilename = $GLOBALS['mysqli']->real_escape_string($videoFile['name']);
        $storedFilename = $GLOBALS['mysqli']->real_escape_string(basename($videoFilePath));
        $fileSize = (int) $videoFile['size'];
        $mimeTypeEscaped = $GLOBALS['mysqli']->real_escape_string($mimeType);

        $uploadStmt = $GLOBALS['mysqli']->prepare("INSERT INTO bt_video_uploads 
            (nomination_id, original_filename, stored_filename, file_path, file_size, mime_type, duration, uploaded_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

        if ($uploadStmt) {
            $uploadStmt->bind_param(
                "isssisi",
                $nominationId,
                $originalFilename,
                $storedFilename,
                $videoFilePath,
                $fileSize,
                $mimeTypeEscaped,
                $videoDuration
            );
            $uploadStmt->execute();
            $uploadStmt->close();
        }
    }

    print 'success';
    exit;
} else {
    $error = $stmt->error;
    $stmt->close();

    // If file was uploaded but DB insert failed, delete the files
    if ($uploadType === 'file' && $videoFilePath && file_exists($videoFilePath)) {
        unlink($videoFilePath);
    }
    if ($profilePhotoPath && file_exists($profilePhotoPath)) {
        unlink($profilePhotoPath);
    }

    print "Error: " . $error;
    exit;
}
?>