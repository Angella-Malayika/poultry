<?php
// Serve photo from database by ID
header('Cache-Control: public, max-age=3600');
header('Pragma: public');

// Get photo ID from GET parameter
$photo_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($photo_id <= 0) {
    header('HTTP/1.0 404 Not Found');
    exit('No photo ID provided');
}

// Connect to database
require_once __DIR__ . '/../connection.php';

// Check if photos table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'photos'");
if (!$table_check || mysqli_num_rows($table_check) === 0) {
    header('HTTP/1.0 404 Not Found');
    exit('Photos table does not exist');
}

// Check for required columns
$columns = [];
$columns_result = mysqli_query($conn, "SHOW COLUMNS FROM photos");
if ($columns_result) {
    while ($col = mysqli_fetch_assoc($columns_result)) {
        $columns[] = $col['Field'];
    }
}

$has_image_data = in_array('image_data', $columns, true);
$has_image_mime = in_array('image_mime', $columns, true);

// Fetch photo from database
$photo = null;
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
} else {
    // Fallback: try image column (file path)
    if (in_array('image', $columns, true)) {
        $stmt = $conn->prepare("SELECT image FROM photos WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("i", $photo_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $image_path = $row['image'] ?? '';
                // Serve from file system if image path exists
                if (!empty($image_path) && file_exists('../' . $image_path)) {
                    header('Content-Type: ' . mime_content_type('../' . $image_path));
                    readfile('../' . $image_path);
                    exit();
                }
            }
            $stmt->close();
        }
    }
}

// If photo data exists, serve it
if ($photo && !empty($photo['image_data'])) {
    $mime = $photo['image_mime'] ?? 'image/jpeg';
    header('Content-Type: ' . htmlspecialchars($mime));
    echo $photo['image_data'];
    exit();
}

// No photo found
header('HTTP/1.0 404 Not Found');
echo 'Photo not found';
?>
