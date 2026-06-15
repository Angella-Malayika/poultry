<?php
// Admin/view_complaints.php – Fixed paths using BASE_URL from config.php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth_required.php';
require_once dirname(__DIR__) . '/connection.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit();
}

$complaints = [];
$flash_message = '';
$flash_type = 'success';

$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'complaints'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint_id'], $_POST['complaint_action'])) {
        $complaint_id = (int) $_POST['complaint_id'];
        $action = strtolower(trim((string) $_POST['complaint_action']));
        $new_status = $action === 'resolve' ? 'resolved' : 'new';

        if ($complaint_id > 0 && in_array($action, ['resolve', 'reopen'], true)) {
            $stmt = $conn->prepare('UPDATE complaints SET status = ? WHERE id = ?');
            if ($stmt) {
                $stmt->bind_param('si', $new_status, $complaint_id);
                if ($stmt->execute()) {
                    $flash_message = 'Feedback updated successfully.';
                } else {
                    $flash_message = 'Could not update feedback.';
                    $flash_type = 'danger';
                }
                $stmt->close();
            } else {
                $flash_message = 'Could not update feedback.';
                $flash_type = 'danger';
            }
        }
    }

    $res = mysqli_query($conn, 'SELECT * FROM complaints ORDER BY created_at DESC, id DESC');
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $complaints[] = $row;
        }
    }
}

$new_count = count(array_filter($complaints, static function ($row) {
    return strtolower((string) ($row['status'] ?? '')) === 'new';
}));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaints | Admin Panel</title>
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
    .status-pill { font-size: 0.8rem; }
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
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/view_complaints.php"><i class="bi bi-chat-square-text"></i> Complaints</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/login_activity.php"><i class="bi bi-person-check"></i> Login Activity</a>
            <a class="nav-link text-danger mt-3" href="<?php echo BASE_URL; ?>/adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>Customer Feedback</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">All Feedback
                    <span class="badge bg-success ms-2"><?php echo count($complaints); ?></span>
                    <span class="badge bg-danger ms-2">New: <?php echo $new_count; ?></span>
                </h4>
            </div>

            <?php if ($flash_message !== ''): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (count($complaints) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($complaints as $row): ?>
                                <?php $is_new = strtolower((string) ($row['status'] ?? 'new')) === 'new'; ?>
                                <tr class="<?php echo $is_new ? 'table-warning' : ''; ?>">
                                    <td><?php echo (int) $row['id']; ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars(ucfirst((string) $row['category'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td style="min-width: 220px;">
                                        <?php echo nl2br(htmlspecialchars($row['message_text'])); ?>
                                        <?php if (!empty($row['order_id'])): ?>
                                            <div class="text-muted small">Order ID: <?php echo htmlspecialchars($row['order_id']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?php echo $is_new ? 'danger' : 'success'; ?> status-pill">
                                            <?php echo $is_new ? 'New' : 'Resolved'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="m-0">
                                            <input type="hidden" name="complaint_id" value="<?php echo (int) $row['id']; ?>">
                                            <?php if ($is_new): ?>
                                                <input type="hidden" name="complaint_action" value="resolve">
                                                <button class="btn btn-sm btn-success">Mark Resolved</button>
                                            <?php else: ?>
                                                <input type="hidden" name="complaint_action" value="reopen">
                                                <button class="btn btn-sm btn-outline-danger">Reopen</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-light border">No feedback submitted yet.</div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>