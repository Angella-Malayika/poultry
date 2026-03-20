<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include 'connection.php';

$orders = [];
$order_date_column = null;
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
if (mysqli_num_rows($table_check) > 0) {
    $columns = [];
    $col_res = mysqli_query($conn, "SHOW COLUMNS FROM orders");
    while ($col = mysqli_fetch_assoc($col_res)) {
        $columns[] = $col['Field'];
    }

    $sort_column = null;
    foreach (['created_at', 'order_date', 'date', 'id', 'order_id'] as $candidate) {
        if (in_array($candidate, $columns, true)) {
            $sort_column = $candidate;
            break;
        }
    }
    $order_date_column = $sort_column;

    $sql = "SELECT * FROM orders";
    if ($sort_column !== null) {
        $sql .= " ORDER BY `" . $sort_column . "` DESC";
    }

    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $orders[] = $row;
    }
}

$status_colors = [
    'pending'   => 'warning',
    'confirmed' => 'primary',
    'delivered' => 'success',
    'cancelled' => 'danger',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders | Admin Panel</title>
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
    .orders-table thead th { background: #1b5e20; color: #fff; border: none; }
    .orders-table tbody tr { transition: background 0.15s; }
    .orders-table tbody tr:hover { background: #f1f8f1; }
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
            <a class="nav-link active" href="view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link text-danger mt-3" href="adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Orders</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="adlogout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">All Orders
                    <span class="badge bg-success ms-2"><?php echo count($orders); ?></span>
                </h4>
            </div>

            <?php if (count($orders) > 0): ?>
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table orders-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Delivery Date</th>
                                    <th>Delivery Address</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $i => $order):
                                    $status = strtolower($order['status'] ?? 'pending');
                                    $badge  = $status_colors[$status] ?? 'secondary';
                                ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($order['Customer_Name'] ?? ''); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['Email'] ?? ''); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['product'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity'] ?? '—'); ?></td>
                                     <td><?php echo htmlspecialchars($order['Phone'] ?? ''); ?></td>
                                     <td><a href="mailto:<?php echo htmlspecialchars($order['Email'] ?? ''); ?>"><?php echo htmlspecialchars($order['Email'] ?? ''); ?></a></td>
                                     <td><?php echo htmlspecialchars($order['Delivery Address'] ?? ''); ?></td>
                                     <td><?php echo htmlspecialchars($order['Delivery Date'] ?? ''); ?></td>


                                    <td class="fw-bold text-success">
                                        $<?php echo number_format((float)($order['total'] ?? 0), 2); ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?php echo $badge; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $date_value = '';
                                        if ($order_date_column !== null && array_key_exists($order_date_column, $order)) {
                                            $date_value = $order[$order_date_column];
                                        } else {
                                            $date_value = $order['created_at'] ?? $order['order_date'] ?? $order['date'] ?? '';
                                        }
                                        echo htmlspecialchars((string)$date_value);
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="card shadow-sm empty-state">
                    <i class="bi bi-cart-x"></i>
                    <h5>No orders yet</h5>
                    <p class="text-muted">Customer orders will appear here once they start placing them.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
