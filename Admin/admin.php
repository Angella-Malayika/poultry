<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include 'connection.php';

$photos = [];
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'photos'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    $sql = "SELECT * FROM photos ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $photos[] = $row;
        }
    }
}
$photo_count = count($photos);

$pending_order_count = 0;
$orders_table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
if ($orders_table_check && mysqli_num_rows($orders_table_check) > 0) {
    $pending_result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM orders WHERE LOWER(COALESCE(status, 'pending')) IN ('pending', 'new')"
    );
    if ($pending_result) {
        $pending_row = mysqli_fetch_assoc($pending_result);
        $pending_order_count = (int) ($pending_row['total'] ?? 0);
    }
}

$new_message_count = 0;
$messages_table_check = mysqli_query($conn, "SHOW TABLES LIKE 'messages'");
if ($messages_table_check && mysqli_num_rows($messages_table_check) > 0) {
    $is_read_column = mysqli_query($conn, "SHOW COLUMNS FROM messages LIKE 'is_read'");
    if ($is_read_column && mysqli_num_rows($is_read_column) > 0) {
        $message_count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM messages WHERE is_read = 0");
    } else {
        $message_count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM messages");
    }

    if (!empty($message_count_result)) {
        $message_row = mysqli_fetch_assoc($message_count_result);
        $new_message_count = (int) ($message_row['total'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Poultry Farm</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: #f0f2f5; }
    .sidebar {
        background: linear-gradient(135deg,  #0f3c2a, #1f6b3f);
        min-height: 100vh;
        color: #fff;
        padding-top: 20px;
    }
    .sidebar .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 12px 20px;
        border-radius: 8px;
        margin: 4px 10px;
        transition: all 0.2s;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        background: rgba(255,255,255,0.15);
        color: #fff;
    }
    .sidebar .nav-link i { margin-right: 10px; font-size: 1.1rem; }
    .brand { padding: 10px 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.15); margin-bottom: 15px; }
    .brand h4 { margin: 0; font-weight: 700; }
    .brand small { opacity: 0.7; }
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .stat-link:focus-visible {
        outline: 2px solid #1f6b3f;
        outline-offset: 2px;
        border-radius: 12px;
    }
    .photo-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .photo-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .photo-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .top-bar {
        background: #fff;
        border-bottom: 1px solid #e0e0e0;
        padding: 15px 25px;
    }
    .btn-delete {
        color: #dc3545;
        border: 1px solid #dc3545;
        background: transparent;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        background: #dc3545;
        color: #fff;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #888;
    }
    .empty-state i { font-size: 4rem; margin-bottom: 15px; display: block; color: #ccc; }
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
        <div class="brand">
            <h4><i class="bi bi-egg-fried"></i>Kalungu Quality Feeds </h4>
            <small>Admin Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link" href="upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Upload Photo</a>
            <a class="nav-link" href="view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link mt-auto text-danger" href="adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 p-0">

        <!-- Top Bar -->
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Dashboard</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="adlogout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>

        <div class="p-4">

            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="bi bi-images text-success fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?php echo $photo_count; ?></h3>
                                <small class="text-muted">Total Photos</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a href="view_orders.php" class="card stat-card shadow-sm stat-link" aria-label="Open orders page">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i class="bi bi-cart3 text-primary fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?php echo $pending_order_count; ?></h3>
                                <small class="text-muted">New Orders</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a href="view_messages.php" class="card stat-card shadow-sm stat-link" aria-label="Open messages page">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="bi bi-envelope text-warning fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?php echo $new_message_count; ?></h3>
                                <small class="text-muted">New Messages</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Photos Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="bi bi-images"></i> Poultry Photos</h4>
                <div class="d-flex gap-2">
                    <a href="gallery.php" class="btn btn-outline-success">
                        <i class="bi bi-display"></i> User Gallery
                    </a>
                    <a href="upload_photo.php" class="btn ">
                        <i class="bi bi-plus-circle"></i> Upload New Photo
                    </a>
                </div>
            </div>

            <div class="row g-3">
            <?php if ($photo_count > 0): ?>
                <?php foreach ($photos as $row): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card photo-card shadow-sm">
                        <img src="photo.php?id=<?php echo intval($row['id']); ?>" alt="Poultry Photo">
                        <div class="card-body text-center py-2">
                            <a href="delete_photo.php?id=<?php echo intval($row['id']); ?>"
                               class="btn btn-delete btn-sm w-100"
                               onclick="return confirm('Are you sure you want to delete this photo?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state card shadow-sm">
                        <i class="bi bi-camera"></i>
                        <h5>No photos yet</h5>
                        <p>Upload your first poultry photo to get started.</p>
                        <a href="upload_photo.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Upload Photo</a>
                    </div>
                </div>
            <?php endif; ?>
            </div>

        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>