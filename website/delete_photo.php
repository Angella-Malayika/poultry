<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include 'connection.php';

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id = intval($_GET['id']);

// Fetch the photo to preview it
$stmt = $conn->prepare("SELECT id, image FROM photos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$photo = $result->fetch_assoc();
$stmt->close();

if (!$photo) {
    header('Location: admin.php');
    exit();
}

// Handle confirmed deletion
if (isset($_POST['confirm_delete'])) {
    $file_path = "uploads/" . $photo['image'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    $del = $conn->prepare("DELETE FROM photos WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delete Photo | Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: #f0f2f5; }
    .sidebar {
        background: linear-gradient(135deg, #1b5e20, #388e3c);
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
    .sidebar .nav-link:hover {
        background: rgba(255,255,255,0.15);
        color: #fff;
    }
    .sidebar .nav-link i { margin-right: 10px; font-size: 1.1rem; }
    .brand { padding: 10px 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.15); margin-bottom: 15px; }
    .brand h4 { margin: 0; font-weight: 700; }
    .brand small { opacity: 0.7; }
    .top-bar { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 15px 25px; }
    .confirm-card {
        border: none;
        border-radius: 16px;
        max-width: 480px;
        margin: 60px auto;
    }
    .confirm-card img {
        width: 100%;
        max-height: 280px;
        object-fit: cover;
        border-radius: 12px 12px 0 0;
    }
    .danger-banner {
        background: #fff3f3;
        border-left: 4px solid #dc3545;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
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
            <a class="nav-link" href="upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Upload Photo</a>
            <a class="nav-link" href="view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link text-danger mt-3" href="adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-trash me-2"></i>Delete Photo</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="adlogout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>

        <div class="p-4">
            <div class="confirm-card card shadow">
                <img src="photo.php?id=<?php echo intval($photo['id']); ?>" alt="Photo to delete">
                <div class="card-body p-4">
                    <div class="danger-banner">
                        <strong><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Warning</strong>
                        <p class="mb-0 mt-1 text-danger">This action is permanent and cannot be undone. The photo will be removed from the gallery.</p>
                    </div>
                    <p class="text-muted mb-4">Are you sure you want to delete this photo?</p>
                    <div class="d-flex gap-3">
                        <form method="POST">
                            <button type="submit" name="confirm_delete" class="btn btn-danger px-4">
                                <i class="bi bi-trash me-1"></i> Yes, Delete
                            </button>
                        </form>
                        <a href="admin.php" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
