<?php
// website/photo.php – Serve photo from database by ID or file path

require_once dirname(__DIR__) . '/config.php'; // defines BASE_URL, starts session if needed
require_once dirname(__DIR__) . '/connection.php';

header('Cache-Control: public, max-age=3600');
header('Pragma: public');

$photo_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($photo_id <= 0) {
    http_response_code(404);
    exit('No photo ID provided');
}

// Check if photos table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'photos'");
if (!$table_check || mysqli_num_rows($table_check) === 0) {
    http_response_code(404);
    exit('Photos table does not exist');
}

// Get column list
$columns = [];
$columns_result = mysqli_query($conn, "SHOW COLUMNS FROM photos");
if ($columns_result) {
    while ($col = mysqli_fetch_assoc($columns_result)) {
        $columns[] = $col['Field'];
    }
}

$has_image_data = in_array('image_data', $columns, true);
$has_image_mime = in_array('image_mime', $columns, true);
$has_image_path = in_array('image', $columns, true);

$photo = null;

// Try BLOB storage first
if ($has_image_data && $has_image_mime) {
    $stmt = $conn->prepare("SELECT image_data, image_mime FROM photos WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $photo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $photo = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

// If BLOB found, serve it
if ($photo && !empty($photo['image_data'])) {
    $mime = $photo['image_mime'] ?? 'image/jpeg';
    header('Content-Type: ' . $mime);
    echo $photo['image_data'];
    exit();
}

// Fallback: try file path storage
if ($has_image_path) {
    $stmt = $conn->prepare("SELECT image FROM photos WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $photo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_path = $row['image'] ?? '';
            if (!empty($image_path)) {
                // Image path is relative to project root (e.g., images/photo.jpg)
                $full_path = dirname(__DIR__) . '/' . ltrim($image_path, '/');
                if (file_exists($full_path)) {
                    $mime = mime_content_type($full_path) ?: 'image/jpeg';
                    header('Content-Type: ' . $mime);
                    readfile($full_path);
                    exit();
                }
            }
        }
        $stmt->close();
    }
}

// No photo found
http_response_code(404);
echo 'Photo not found';
?>