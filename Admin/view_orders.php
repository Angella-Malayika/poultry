<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include 'connection.php';

$orders = [];
$flash_message = '';
$flash_type = 'success';

$allowed_statuses = ['pending', 'approved', 'delivered', 'cancelled'];
$status_aliases = [
    'new' => 'pending',
    'confirmed' => 'approved',
    'processing' => 'approved',
    'completed' => 'delivered',
];

$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
        $order_id = (int) $_POST['order_id'];
        $new_status = strtolower(trim((string) $_POST['new_status']));
        $new_status = $status_aliases[$new_status] ?? $new_status;

        if ($order_id > 0 && in_array($new_status, $allowed_statuses, true)) {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('si', $new_status, $order_id);
                if ($stmt->execute()) {
                    $flash_message = 'Order status updated successfully.';
                } else {
                    $flash_message = 'Failed to update order status.';
                    $flash_type = 'danger';
                }
                $stmt->close();
            } else {
                $flash_message = 'Failed to prepare status update.';
                $flash_type = 'danger';
            }
        } else {
            $flash_message = 'Invalid order update request.';
            $flash_type = 'danger';
        }
    }

    $order_sort_column = 'id';
    $columns = [];
    $columns_result = mysqli_query($conn, 'SHOW COLUMNS FROM orders');
    if ($columns_result) {
        while ($col = mysqli_fetch_assoc($columns_result)) {
            $columns[] = $col['Field'];
        }
    }

    foreach (['order_date', 'created_at', 'date', 'id'] as $candidate) {
        if (in_array($candidate, $columns, true)) {
            $order_sort_column = $candidate;
            break;
        }
    }

    $query = "SELECT o.*, u.username, u.email AS account_email
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.user_id
              ORDER BY o.`" . $order_sort_column . "` DESC, o.id DESC";
    $res = mysqli_query($conn, $query);

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $raw_status = strtolower(trim((string) ($row['status'] ?? 'pending')));
            $normalized_status = $status_aliases[$raw_status] ?? $raw_status;
            if (!in_array($normalized_status, $allowed_statuses, true)) {
                $normalized_status = 'pending';
            }

            $orders[] = [
                'id' => isset($row['id']) ? (int) $row['id'] : 0,
                'customer' => trim($row['full_name'] ?? $row['Customer_Name'] ?? $row['customer_name'] ?? 'Unknown'),
                'username' => trim($row['username'] ?? ''),
                'email' => trim($row['account_email'] ?? $row['Email'] ?? $row['email'] ?? ''),
                'phone' => trim($row['phone'] ?? $row['Phone'] ?? ''),
                'product' => trim($row['product'] ?? 'N/A'),
                'quantity' => isset($row['quantity']) ? (string) $row['quantity'] : 'N/A',
                'delivery_address' => trim($row['delivery_address'] ?? $row['Delivery Address'] ?? ''),
                'delivery_date' => trim($row['delivery_date'] ?? $row['Delivery Date'] ?? ''),
                'order_date' => trim($row['order_date'] ?? $row['created_at'] ?? $row['date'] ?? ''),
                'status' => $normalized_status,
            ];
        }
    }
}

$new_orders = count(array_filter($orders, static function ($order) {
    return ($order['status'] ?? '') === 'pending';
}));

$approved_orders = count(array_filter($orders, static function ($order) {
    return ($order['status'] ?? '') === 'approved';
}));

$delivered_orders = count(array_filter($orders, static function ($order) {
    return ($order['status'] ?? '') === 'delivered';
}));

$status_colors = [
    'pending' => 'warning',
    'approved' => 'primary',
    'delivered' => 'success',
    'cancelled' => 'danger',
];

$status_labels = [
    'pending' => 'New',
    'approved' => 'Approved',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled',
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
    .orders-table thead th { background: #1b5e20; color: #fff; border: none; }
    .orders-table tbody tr { transition: background 0.15s; }
    .orders-table tbody tr:hover { background: #f1f8f1; }
    .order-row-new { background: #fff8e1; }
    .stats-card {
        border: 0;
        border-radius: 12px;
    }
    .empty-state { text-align: center; padding: 60px 20px; color: #888; }
    .empty-state i { font-size: 4rem; display: block; margin-bottom: 15px; color: #ccc; }
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
            <a class="nav-link" href="admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link" href="upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Upload Photo</a>
            <a class="nav-link active" href="view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link text-danger mt-3" href="adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Order Management</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="adlogout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>

        <div class="p-4">
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="card stats-card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">New Orders</h6>
                            <h3 class="mb-0 text-warning"><?php echo $new_orders; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card stats-card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Approved</h6>
                            <h3 class="mb-0 text-primary"><?php echo $approved_orders; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card stats-card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Delivered</h6>
                            <h3 class="mb-0 text-success"><?php echo $delivered_orders; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">All Orders
                    <span class="badge bg-success ms-2"><?php echo count($orders); ?></span>
                </h4>
            </div>

            <?php if ($flash_message !== ''): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (count($orders) > 0): ?>
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table orders-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Ordered On</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $i => $order):
                                    $status = strtolower($order['status'] ?? 'pending');
                                    $badge = $status_colors[$status] ?? 'secondary';
                                    $status_label = $status_labels[$status] ?? ucfirst($status);
                                    $is_new = ($status === 'pending');

                                    $ordered_on_display = 'N/A';
                                    if (!empty($order['order_date'])) {
                                        $ordered_ts = strtotime($order['order_date']);
                                        $ordered_on_display = $ordered_ts ? date('M d, Y H:i', $ordered_ts) : $order['order_date'];
                                    }

                                    $delivery_on_display = 'N/A';
                                    if (!empty($order['delivery_date'])) {
                                        $delivery_ts = strtotime($order['delivery_date']);
                                        $delivery_on_display = $delivery_ts ? date('M d, Y', $delivery_ts) : $order['delivery_date'];
                                    }
                                ?>
                                <tr class="<?php echo $is_new ? 'order-row-new' : ''; ?>">
                                    <td><?php echo $i + 1; ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($order['customer']); ?></div>
                                        <?php if (!empty($order['username'])): ?>
                                            <small class="text-muted">@<?php echo htmlspecialchars($order['username']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($order['email'])): ?>
                                            <div><a href="mailto:<?php echo htmlspecialchars($order['email']); ?>"><?php echo htmlspecialchars($order['email']); ?></a></div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['phone'])): ?>
                                            <div><a href="tel:<?php echo htmlspecialchars($order['phone']); ?>"><?php echo htmlspecialchars($order['phone']); ?></a></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($order['product']); ?></div>
                                        <?php if (!empty($order['delivery_address'])): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['delivery_address']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($ordered_on_display); ?></td>
                                    <td><?php echo htmlspecialchars($delivery_on_display); ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?php echo $badge; ?>">
                                            <?php echo htmlspecialchars($status_label); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="order_detail.php?id=<?php echo intval($order['id']); ?>" class="btn btn-outline-success btn-sm">Details</a>

                                            <?php if ($status === 'pending'): ?>
                                                <form method="POST" class="m-0">
                                                    <input type="hidden" name="order_id" value="<?php echo intval($order['id']); ?>">
                                                    <input type="hidden" name="new_status" value="approved">
                                                    <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                                </form>
                                            <?php elseif ($status === 'approved'): ?>
                                                <form method="POST" class="m-0">
                                                    <input type="hidden" name="order_id" value="<?php echo intval($order['id']); ?>">
                                                    <input type="hidden" name="new_status" value="delivered">
                                                    <button type="submit" class="btn btn-success btn-sm">Delivered</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if (in_array($status, ['pending', 'approved'], true)): ?>
                                                <form method="POST" class="m-0" onsubmit="return confirm('Cancel this order?');">
                                                    <input type="hidden" name="order_id" value="<?php echo intval($order['id']); ?>">
                                                    <input type="hidden" name="new_status" value="cancelled">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Cancel</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
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
