<?php
include 'connection.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(400);
    echo 'Invalid image request.';
    exit();
}

$id = (int) $_GET['id'];
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'photos'");
if (!$table_check || mysqli_num_rows($table_check) === 0) {
    http_response_code(404);
    echo 'Image not found.';
    exit();
}

$columns = [];
$col_res = mysqli_query($conn, "SHOW COLUMNS FROM photos");
if ($col_res) {
    while ($col = mysqli_fetch_assoc($col_res)) {
        $columns[] = $col['Field'];
    }
}

$select_cols = ['id'];
if (in_array('image', $columns, true)) {
    $select_cols[] = 'image';
}
if (in_array('image_data', $columns, true)) {
    $select_cols[] = 'image_data';
}
if (in_array('image_mime', $columns, true)) {
    $select_cols[] = 'image_mime';
}

$stmt = $conn->prepare("SELECT " . implode(', ', $select_cols) . " FROM photos WHERE id = ? LIMIT 1");

if (!$stmt) {
    http_response_code(500);
    echo 'Unable to fetch image.';
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$photo = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$photo) {
    http_response_code(404);
    echo 'Image not found.';
    exit();
}

if (array_key_exists('image_data', $photo) && !empty($photo['image_data'])) {
    $mime_type = (!empty($photo['image_mime']) ? $photo['image_mime'] : 'image/jpeg');
    header('Content-Type: ' . $mime_type);
    header('Cache-Control: public, max-age=604800');
    echo $photo['image_data'];
    exit();
}

if (array_key_exists('image', $photo) && !empty($photo['image'])) {
    $legacy_path = __DIR__ . '/uploads/' . $photo['image'];
    if (is_file($legacy_path)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = $finfo ? finfo_file($finfo, $legacy_path) : 'image/jpeg';
        if ($finfo) {
            finfo_close($finfo);
        }

        header('Content-Type: ' . ($mime_type ?: 'image/jpeg'));
        header('Cache-Control: public, max-age=604800');
        readfile($legacy_path);
        exit();
    }
}

http_response_code(404);
echo 'Image not found.';
