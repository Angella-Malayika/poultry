<?php
// Admin.php – Fixed paths using BASE_URL from config.php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth_required.php';
require_once dirname(__DIR__) . '/connection.php';

// Ensure only admin can access (auth_required.php already checks login, but we also need role check)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit();
}

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
$delivered_order_count = 0;
$trend_labels = [];
$trend_pending = [];
$trend_delivered = [];
$orders_table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
if ($orders_table_check && mysqli_num_rows($orders_table_check) > 0) {
    $status_column = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'status'");
    if ($status_column && mysqli_num_rows($status_column) === 0) {
        mysqli_query($conn, "ALTER TABLE orders ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
    }

    $order_date_column = 'order_date';
    $columns = [];
    $columns_result = mysqli_query($conn, 'SHOW COLUMNS FROM orders');
    if ($columns_result) {
        while ($col = mysqli_fetch_assoc($columns_result)) {
            $columns[] = $col['Field'];
        }
    }
    foreach (['order_date', 'created_at', 'date'] as $candidate) {
        if (in_array($candidate, $columns, true)) {
            $order_date_column = $candidate;
            break;
        }
    }

    $pending_result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM orders WHERE LOWER(COALESCE(status, 'pending')) IN ('pending', 'new')"
    );
    if ($pending_result) {
        $pending_row = mysqli_fetch_assoc($pending_result);
        $pending_order_count = (int) ($pending_row['total'] ?? 0);
    }

    $delivered_result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM orders WHERE LOWER(COALESCE(status, '')) = 'delivered'"
    );
    if ($delivered_result) {
        $delivered_row = mysqli_fetch_assoc($delivered_result);
        $delivered_order_count = (int) ($delivered_row['total'] ?? 0);
    }

    $month_labels = [];
    $month_keys = [];
    $start = new DateTime('first day of -5 months');
    $period = new DatePeriod($start, new DateInterval('P1M'), 6);
    foreach ($period as $dt) {
        $month_keys[] = $dt->format('Y-m');
        $month_labels[] = $dt->format('M Y');
    }

    $start_date = $start->format('Y-m-01');
    $trend_sql =
        "SELECT DATE_FORMAT(`" . $order_date_column . "`, '%Y-%m') AS ym,\n"
        . "SUM(CASE WHEN LOWER(COALESCE(status, 'pending')) IN ('pending', 'new') THEN 1 ELSE 0 END) AS pending_total,\n"
        . "SUM(CASE WHEN LOWER(COALESCE(status, '')) = 'delivered' THEN 1 ELSE 0 END) AS delivered_total\n"
        . "FROM orders\n"
        . "WHERE `" . $order_date_column . "` >= '" . $start_date . "'\n"
        . "GROUP BY ym";

    $trend_map = [];
    $trend_res = mysqli_query($conn, $trend_sql);
    if ($trend_res) {
        while ($row = mysqli_fetch_assoc($trend_res)) {
            $trend_map[$row['ym']] = [
                'pending' => (int) ($row['pending_total'] ?? 0),
                'delivered' => (int) ($row['delivered_total'] ?? 0),
            ];
        }
    }

    foreach ($month_keys as $index => $key) {
        $trend_labels[] = $month_labels[$index];
        $trend_pending[] = isset($trend_map[$key]) ? $trend_map[$key]['pending'] : 0;
        $trend_delivered[] = isset($trend_map[$key]) ? $trend_map[$key]['delivered'] : 0;
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
            <h4><i class="bi bi-egg-fried"></i> Kalungu Quality Feeds</h4>
            <small>Admin Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/Admin/admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Add Product</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_complaints.php"><i class="bi bi-chat-square-text"></i> Complaints</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/login_activity.php"><i class="bi bi-person-check"></i> Login Activity</a>
            <a class="nav-link mt-auto text-danger" href="<?php echo BASE_URL; ?>/Admin/adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 p-0">

        <!-- Top Bar -->
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Dashboard</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>


            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <a href="<?php echo BASE_URL; ?>/Admin/view_orders.php" class="card stat-card shadow-sm stat-link" aria-label="Open orders page">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="bi bi-truck text-warning fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?php echo $pending_order_count; ?></h3>
                                <small class="text-muted">Pending Deliveries</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="<?php echo BASE_URL; ?>/Admin/view_orders.php" class="card stat-card shadow-sm stat-link" aria-label="Open delivered orders">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="bi bi-check2-circle text-success fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?php echo $delivered_order_count; ?></h3>
                                <small class="text-muted">Delivered</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
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
                <div class="col-sm-6 col-lg-3">
                    <a href="<?php echo BASE_URL; ?>/Admin/view_messages.php" class="card stat-card shadow-sm stat-link" aria-label="Open messages page">
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

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Delivery Trends</h5>
                        <small class="text-muted">Last 6 months</small>
                    </div>
                    <canvas id="deliveryChart" height="120"></canvas>
                </div>
            </div>

            <!-- Photos Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="bi bi-images"></i> Poultry Photos</h4>
            </div>

            <div class="row g-3">
            <?php if ($photo_count > 0): ?>
                <?php foreach ($photos as $row): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card photo-card shadow-sm">
                        <img src="<?php echo BASE_URL; ?>/Admin/photo.php?id=<?php echo intval($row['id']); ?>" alt="Poultry Photo">
                        <div class="card-body text-center py-2">
                            <a href="<?php echo BASE_URL; ?>/Admin/delete_photo.php?id=<?php echo intval($row['id']); ?>"
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
                        <p>Upload your first poultry photo to get started.</p>
                        <a href="<?php echo BASE_URL; ?>/Admin/upload_photo.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Product</a>
                    </div>
                </div>
            <?php endif; ?>
            </div>

        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const deliveryLabels = <?php echo json_encode($trend_labels); ?>;
    const pendingData = <?php echo json_encode($trend_pending); ?>;
    const deliveredData = <?php echo json_encode($trend_delivered); ?>;

    const chartEl = document.getElementById('deliveryChart');
    if (chartEl) {
        new Chart(chartEl, {
            type: 'line',
            data: {
                labels: deliveryLabels,
                datasets: [
                    {
                        label: 'Pending',
                        data: pendingData,
                        borderColor: '#f4a261',
                        backgroundColor: 'rgba(244, 162, 97, 0.2)',
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: 'Delivered',
                        data: deliveredData,
                        borderColor: '#2a9d8f',
                        backgroundColor: 'rgba(42, 157, 143, 0.2)',
                        fill: true,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }
</script>
</body>
</html>