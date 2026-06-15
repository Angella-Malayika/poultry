<?php
// Admin/login_activity.php – Fixed paths using BASE_URL from config.php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth_required.php';
require_once dirname(__DIR__) . '/connection.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit();
}

$activity_rows = [];
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'login_activity'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    $res = mysqli_query(
        $conn,
        "SELECT id, user_id, username, role, ip_address, user_agent, login_at, logout_at\n"
        . "FROM login_activity\n"
        . "ORDER BY login_at DESC, id DESC\n"
        . "LIMIT 200"
    );
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $activity_rows[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Activity | Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --leaf-900: #0f3c2a;
        --leaf-700: #1f6b3f;
    }
    body { background: #f0f2f5; }
    .sidebar {
        background: linear-gradient(135deg, var(--leaf-900), var(--leaf-700));
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
</style>
</head>
<body>
<div class="container-fluid">
<div class="row">

    <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
        <div class="brand">
            <h4><i class="bi bi-egg-fried"></i> Kalungu Quality Feeds</h4>
            <small>Admin Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Add Product</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/view_complaints.php"><i class="bi bi-chat-square-text"></i> Complaints</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/login_activity.php"><i class="bi bi-person-check"></i> Login Activity</a>
            <a class="nav-link text-danger mt-3" href="<?php echo BASE_URL; ?>/adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Login Activity</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>

        <div class="p-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Recent logins (last 200)</h6>

                    <?php if (count($activity_rows) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-success">
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>IP Address</th>
                                        <th>Login</th>
                                        <th>Logout</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activity_rows as $row): ?>
                                        <tr>
                                            <td><?php echo (int) $row['id']; ?></td>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></div>
                                                <small class="text-muted">#<?php echo (int) $row['user_id']; ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars(ucfirst((string) $row['role'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['ip_address'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['login_at']); ?></td>
                                            <td><?php echo htmlspecialchars($row['logout_at'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border mb-0">No login activity found yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>