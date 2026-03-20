<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include 'connection.php';

$messages = [];
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'messages'");
if (mysqli_num_rows($table_check) > 0) {
    $res = mysqli_query($conn, "SELECT * FROM messages ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        $messages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages | Admin Panel</title>
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
    body { background: #f0f2f5; }
    .sidebar {
        background: linear-gradient(135deg, #0f3c2a, #1f6b3f);
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
    .top-bar { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 15px 25px; }
    .message-card {
        border: none;
        border-radius: 12px;
        border-left: 4px solid #388e3c;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .message-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
    .empty-state { text-align: center; padding: 60px 20px; color: #888; }
    .empty-state i { font-size: 4rem; display: block; margin-bottom: 15px; color: #ccc; }
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
            <a class="nav-link" href="upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Upload Photo</a>
            <a class="nav-link" href="view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link active" href="view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link text-danger mt-3" href="adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-envelope me-2"></i>Messages</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="adlogout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">All Messages
                    <span class="badge bg-success ms-2"><?php echo count($messages); ?></span>
                </h4>
            </div>

            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $msg): ?>
                <div class="card message-card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">
                                    <i class="bi bi-person-circle text-success me-1"></i>
                                    <?php echo htmlspecialchars($msg['name'] ?? 'Unknown'); ?>
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($msg['email'] ?? ''); ?>
                                </small>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                <?php echo htmlspecialchars($msg['created_at'] ?? ''); ?>
                            </small>
                        </div>
                        <hr class="my-2">
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($msg['message'] ?? '')); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card shadow-sm empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No messages yet</h5>
                    <p class="text-muted">When customers send you messages, they'll appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
