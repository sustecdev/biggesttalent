<?php
// public/debug-upload.php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<style>body{font-family:sans-serif; max-width:800px; margin:20px auto; padding:20px; line-height:1.6} h2{border-bottom:2px solid #333; padding-bottom:10px} .success{color:green; font-weight:bold} .error{color:red; font-weight:bold} .info{color:blue} pre{background:#f4f4f4; padding:10px; border:1px solid #ccc}</style>";

echo "<h2>File Upload Debugger</h2>";

// 1. Check PHP Configuration
echo "<h3>1. PHP Configuration</h3>";
$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');
$memoryLimit = ini_get('memory_limit');

echo "<ul>";
echo "<li><strong>upload_max_filesize:</strong> $uploadMax</li>";
echo "<li><strong>post_max_size:</strong> $postMax</li>";
echo "<li><strong>memory_limit:</strong> $memoryLimit</li>";
echo "<li><strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "</li>";
echo "</ul>";

// Convert shortcuts (K, M, G) to bytes for comparison
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    $val = (int) $val;
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

if (return_bytes($postMax) < return_bytes($uploadMax)) {
    echo "<p class='error'>WARNING: post_max_size ($postMax) is smaller than upload_max_filesize ($uploadMax). Large uploads may fail silently.</p>";
}

// 2. Check Directory Permissions
echo "<h3>2. Directory Permissions</h3>";
$uploadDir = 'uploads/videos/';
$absPath = realpath($uploadDir);

echo "<ul>";
echo "<li><strong>Target Directory:</strong> " . ($absPath ? $absPath : $uploadDir . " (Not found)") . "</li>";

if (!is_dir($uploadDir)) {
    echo "<li class='error'>Directory does not exist. Trying to create...</li>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "<li class='success'>Created directory successfully.</li>";
    } else {
        echo "<li class='error'>Failed to create directory. Check parent folder permissions.</li>";
    }
} else {
    echo "<li class='success'>Directory exists.</li>";
}

if (is_writable($uploadDir)) {
    echo "<li class='success'>Directory is writable.</li>";

    // Try writing a test file
    $testFile = $uploadDir . 'test_write_' . time() . '.txt';
    if (file_put_contents($testFile, 'Test write permission')) {
        echo "<li class='success'>Successfully wrote test file.</li>";
        unlink($testFile); // Clean up
    } else {
        echo "<li class='error'>Failed to write test file despite is_writable() being true.</li>";
    }
} else {
    echo "<li class='error'>Directory is NOT writable. Web server user cannot save files here.</li>";
    echo "<li class='info'>Current User: " . get_current_user() . " (UID: " . getmyuid() . ")</li>";
}
echo "</ul>";

// 3. Handle Form Submission
echo "<h3>3. Upload Test</h3>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_FILES) && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        echo "<p class='error'>CRITICAL: POST data is empty but Content-Length > 0. This usually means the uploaded file exceeds post_max_size ($postMax).</p>";
    } elseif (isset($_FILES['video_file'])) {
        $file = $_FILES['video_file'];
        echo "<strong>File Details:</strong><br>";
        echo "Name: " . htmlspecialchars($file['name']) . "<br>";
        echo "Type: " . $file['type'] . "<br>";
        echo "Size: " . $file['size'] . " bytes (" . round($file['size'] / 1024 / 1024, 2) . " MB)<br>";
        echo "Error Code: " . $file['error'] . "<br>";

        if ($file['error'] === UPLOAD_ERR_OK) {
            $dest = $uploadDir . 'debug_' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                echo "<p class='success'>File successfully uploaded to: $dest</p>";
                echo "<p>You should verify if you can access this file via browser.</p>";
                // Clean up debug file? Maybe keep for verification.
            } else {
                echo "<p class='error'>Failed to move uploaded file. Check folder permissions again.</p>";
                $lastError = error_get_last();
                if ($lastError) {
                    echo "System Error: " . $lastError['message'];
                }
            }
        } else {
            $uploadErrors = [
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk.',
                8 => 'A PHP extension stopped the file upload.',
            ];
            echo "<p class='error'>Upload Error: " . ($uploadErrors[$file['error']] ?? 'Unknown Error') . "</p>";
        }
    } else {
        echo "<p class='error'>No file received. Did you select a file?</p>";
    }
}
?>

<form action="" method="post" enctype="multipart/form-data" style="background:#eee; padding:20px; border-radius:5px;">
    <strong>Try uploading a video file:</strong><br><br>
    <input type="file" name="video_file" accept="video/*"><br><br>
    <input type="submit" value="Test Upload"
        style="padding:10px 20px; background:#007bff; color:#fff; border:none; cursor:pointer;">
</form>