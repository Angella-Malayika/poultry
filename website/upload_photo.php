<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include 'connection.php';

$message = '';
$message_type = 'success';
$photo_columns = [];
$recent_photos = [];

function ensure_photo_schema($conn, &$columns)
{
    if (!mysqli_query($conn, "CREATE TABLE IF NOT EXISTS photos (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, image VARCHAR(255) NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4")) {
        return 'Unable to initialize photos table.';
    }

    $columns = [];
    $col_res = mysqli_query($conn, "SHOW COLUMNS FROM photos");
    if (!$col_res) {
        return 'Unable to read photos table schema.';
    }

    while ($col = mysqli_fetch_assoc($col_res)) {
        $columns[] = $col['Field'];
    }

    if (!in_array('image_data', $columns, true)) {
        if (!mysqli_query($conn, "ALTER TABLE photos ADD COLUMN image_data LONGBLOB NULL")) {
            return 'Unable to add image_data column in photos table.';
        }
        $columns[] = 'image_data';
    }

    if (!in_array('image_mime', $columns, true)) {
        if (!mysqli_query($conn, "ALTER TABLE photos ADD COLUMN image_mime VARCHAR(100) NULL")) {
            return 'Unable to add image_mime column in photos table.';
        }
        $columns[] = 'image_mime';
    }

    if (!in_array('created_at', $columns, true)) {
        if (mysqli_query($conn, "ALTER TABLE photos ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP")) {
            $columns[] = 'created_at';
        }
    }

    return '';
}

$schema_error = ensure_photo_schema($conn, $photo_columns);
if ($schema_error !== '') {
    $message = $schema_error;
    $message_type = 'danger';
}

if (isset($_POST['upload'])) {
    if ($schema_error !== '') {
        $message = $schema_error;
        $message_type = 'danger';
    } elseif (!isset($_FILES['image']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
        $message = 'Please choose a valid image file.';
        $message_type = 'danger';
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime_map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        $tmp_name = $_FILES['image']['tmp_name'];
        $file_size = (int) ($_FILES['image']['size'] ?? 0);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = $finfo ? finfo_file($finfo, $tmp_name) : '';
        if ($finfo) {
            finfo_close($finfo);
        }

        if (!in_array($file_type, $allowed_types, true)) {
            $message = 'Only JPG, PNG, GIF, and WEBP files are allowed.';
            $message_type = 'danger';
        } elseif ($file_size <= 0 || $file_size > 5 * 1024 * 1024) {
            $message = 'File is too large. Max 5MB.';
            $message_type = 'danger';
        } else {
            $image_data = file_get_contents($tmp_name);
            if ($image_data === false) {
                $message = 'Could not read the uploaded image.';
                $message_type = 'danger';
            } else {
                $original_base = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
                $safe_base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_base);
                if ($safe_base === '' || $safe_base === null) {
                    $safe_base = 'photo';
                }

                $extension = $mime_map[$file_type] ?? 'jpg';
                $stored_name = $safe_base . '_' . time() . '.' . $extension;

                $stmt = $conn->prepare("INSERT INTO photos (image, image_data, image_mime) VALUES (?, ?, ?)");
                if (!$stmt) {
                    $message = 'Database insert failed.';
                    $message_type = 'danger';
                } else {
                    $blob_param = null;
                    $stmt->bind_param("sbs", $stored_name, $blob_param, $file_type);
                    $stmt->send_long_data(1, $image_data);

                    if ($stmt->execute()) {
                        $message = 'Photo uploaded successfully and stored in the database.';
                        $message_type = 'success';
                    } else {
                        $message = 'Error uploading file to database.';
                        $message_type = 'danger';
                    }
                    $stmt->close();
                }
            }
        }
    }
}

if ($schema_error === '') {
    $recent_sql = "SELECT id, image";
    if (in_array('created_at', $photo_columns, true)) {
        $recent_sql .= ", created_at";
    }
    $recent_sql .= " FROM photos ORDER BY id DESC LIMIT 8";
    $recent_result = mysqli_query($conn, $recent_sql);

    if ($recent_result) {
        while ($photo = mysqli_fetch_assoc($recent_result)) {
            $recent_photos[] = $photo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photos | Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --leaf-900: #0f3c2a;
            --leaf-700: #1f6b3f;
            --leaf-600: #2f8952;
            --cream: #f6fbf6;
            --sun: #ffd166;
            --ink: #132019;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 15%, rgba(47, 137, 82, 0.18), transparent 40%),
                radial-gradient(circle at 90% 0%, rgba(255, 209, 102, 0.2), transparent 35%),
                linear-gradient(160deg, #f5f8f3 0%, #eef6ef 45%, #edf7f1 100%);
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(165deg, var(--leaf-900), var(--leaf-700));
            min-height: 100vh;
            color: #fff;
            padding-top: 20px;
            box-shadow: 6px 0 30px rgba(14, 46, 30, 0.2);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.84);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 10px;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.14);
            color: #fff;
            transform: translateX(2px);
        }

        .sidebar .nav-link i { margin-right: 10px; font-size: 1.05rem; }

        .brand {
            padding: 8px 20px 26px;
            border-bottom: 1px solid rgba(255,255,255,0.16);
            margin-bottom: 16px;
        }

        .brand h4 {
            margin: 0;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .top-bar {
            background: rgba(255,255,255,0.86);
            border-bottom: 1px solid rgba(31, 107, 63, 0.14);
            padding: 16px 26px;
            backdrop-filter: blur(6px);
        }

        .hero {
            border: 0;
            border-radius: 20px;
            background: linear-gradient(130deg, rgba(15, 60, 42, 0.98), rgba(47, 137, 82, 0.94));
            color: #fff;
            overflow: hidden;
            position: relative;
            animation: rise 0.45s ease;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 209, 102, 0.2);
            right: -80px;
            top: -110px;
        }

        .hero h2 {
            font-family: 'Sora', sans-serif;
            font-size: 1.6rem;
            letter-spacing: 0.3px;
        }

        .upload-card,
        .recent-card {
            border: 0;
            border-radius: 16px;
            background: rgba(255,255,255,0.95);
            box-shadow: 0 16px 35px rgba(17, 40, 26, 0.09);
            animation: rise 0.45s ease;
        }

        .drop-hint {
            border: 2px dashed rgba(31, 107, 63, 0.35);
            border-radius: 14px;
            padding: 22px;
            background: linear-gradient(180deg, #f8fdf9, #f1f8f2);
        }

        .preview-wrap {
            display: none;
            border-radius: 14px;
            overflow: hidden;
            margin-top: 14px;
            border: 1px solid rgba(31, 107, 63, 0.15);
        }

        .preview-wrap img {
            width: 100%;
            max-height: 260px;
            object-fit: cover;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 209, 102, 0.18);
            color: #fff;
            font-weight: 700;
            font-size: 0.84rem;
        }

        .recent-photo {
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(19, 32, 25, 0.09);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .recent-photo:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(17, 40, 26, 0.12);
        }

        .recent-photo img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }

        .recent-meta {
            padding: 10px;
            font-size: 0.85rem;
            color: #496050;
        }

        .btn-upload {
            background: linear-gradient(140deg, var(--leaf-700), var(--leaf-600));
            border: 0;
            padding: 10px 20px;
            font-weight: 700;
        }

        .btn-upload:hover {
            opacity: 0.95;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 991px) {
            .top-bar {
                padding: 14px 16px;
            }
            .hero h2 {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row">

    <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
        <div class="brand">
            <h4><i class="bi bi-egg-fried"></i> Poultry Farm</h4>
            <small>Admin Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link active" href="upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Upload Photo</a>
            <a class="nav-link" href="view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link" href="gallery.php"><i class="bi bi-display"></i> User Gallery</a>
            <a class="nav-link text-danger mt-3" href="adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cloud-arrow-up me-2"></i>Upload Photos</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="adlogout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>

        <div class="p-4">
            <div class="card hero mb-4">
                <div class="card-body p-4 position-relative">
                    <span class="chip mb-3"><i class="bi bi-database-check"></i> Stored in MySQL Database</span>
                    <h2 class="mb-2">Upload once, show everywhere</h2>
                    <p class="mb-0 text-white-50">Every new image is saved in the photos table and appears in both Admin Dashboard and User Gallery.</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> shadow-sm">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card upload-card">
                        <div class="card-body p-4">
                            <h5 class="mb-3"><i class="bi bi-image me-2"></i>Add New Poultry Photo</h5>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="drop-hint mb-3">
                                    <label for="image" class="form-label fw-semibold">Choose Image</label>
                                    <input id="image" type="file" name="image" class="form-control" accept="image/*" required>
                                    <small class="text-muted d-block mt-2">Allowed: JPG, PNG, GIF, WEBP. Max size: 5MB.</small>
                                    <div id="previewWrap" class="preview-wrap">
                                        <img id="previewImage" src="" alt="Selected preview">
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <button type="submit" name="upload" class="btn btn-success btn-upload">
                                        <i class="bi bi-cloud-upload me-1"></i> Upload to Database
                                    </button>
                                    <a href="gallery.php" class="btn btn-outline-success">
                                        <i class="bi bi-display me-1"></i> View User Gallery
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card recent-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Uploads</h6>
                                <span class="badge text-bg-success"><?php echo count($recent_photos); ?></span>
                            </div>

                            <?php if (count($recent_photos) > 0): ?>
                                <div class="row g-2">
                                    <?php foreach ($recent_photos as $photo): ?>
                                    <div class="col-6">
                                        <div class="recent-photo">
                                            <img src="photo.php?id=<?php echo intval($photo['id']); ?>" alt="Recent upload">
                                            <div class="recent-meta">
                                                #<?php echo intval($photo['id']); ?>
                                                <?php if (!empty($photo['created_at'])): ?>
                                                    <br><?php echo htmlspecialchars((string) $photo['created_at']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-images fs-1 d-block mb-2"></i>
                                    No photos uploaded yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<script>
    const input = document.getElementById('image');
    const previewWrap = document.getElementById('previewWrap');
    const previewImage = document.getElementById('previewImage');

    input.addEventListener('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;
        if (!file) {
            previewWrap.style.display = 'none';
            previewImage.src = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            previewImage.src = event.target.result;
            previewWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
