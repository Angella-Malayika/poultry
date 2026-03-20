<?php
session_start();

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include 'connection.php';

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($order_id <= 0) {
    header('Location: view_orders.php');
    exit();
}

$message = '';
$message_type = 'success';

$allowed_statuses = ['pending', 'approved', 'delivered', 'cancelled'];
$status_aliases = [
    'new' => 'pending',
    'confirmed' => 'approved',
    'processing' => 'approved',
    'completed' => 'delivered',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = strtolower(trim((string) $_POST['status']));
    $new_status = $status_aliases[$new_status] ?? $new_status;

    if (in_array($new_status, $allowed_statuses, true)) {
        $update_stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
        if ($update_stmt) {
            $update_stmt->bind_param('si', $new_status, $order_id);
            if ($update_stmt->execute()) {
                $message = 'Order status updated successfully.';
                $message_type = 'success';
            } else {
                $message = 'Error updating status. Please try again.';
                $message_type = 'danger';
            }
            $update_stmt->close();
        } else {
            $message = 'Could not prepare status update.';
            $message_type = 'danger';
        }
    } else {
        $message = 'Invalid status selected.';
        $message_type = 'danger';
    }
}

$stmt = $conn->prepare('SELECT o.*, u.email AS account_email, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.id = ? LIMIT 1');
if (!$stmt) {
    header('Location: view_orders.php');
    exit();
}

$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    $stmt->close();
    header('Location: view_orders.php');
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

$current_status_raw = strtolower(trim((string) ($order['status'] ?? 'pending')));
$current_status = $status_aliases[$current_status_raw] ?? $current_status_raw;
if (!in_array($current_status, $allowed_statuses, true)) {
    $current_status = 'pending';
}

$status_colors = [
    'pending' => 'warning',
    'approved' => 'primary',
    'delivered' => 'success',
    'cancelled' => 'danger',
];

$status_icons = [
    'pending' => 'fa-hourglass-half',
    'approved' => 'fa-thumbs-up',
    'delivered' => 'fa-check-circle',
    'cancelled' => 'fa-times-circle',
];

$status_labels = [
    'pending' => 'New',
    'approved' => 'Approved',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled',
];

$customer_name = trim($order['full_name'] ?? $order['Customer_Name'] ?? $order['customer_name'] ?? 'Unknown');
$customer_email = trim($order['account_email'] ?? $order['Email'] ?? $order['email'] ?? '');
$customer_phone = trim($order['phone'] ?? $order['Phone'] ?? '');
$delivery_address = trim($order['delivery_address'] ?? $order['Delivery Address'] ?? '');
$order_date = trim($order['order_date'] ?? $order['created_at'] ?? $order['date'] ?? '');
$delivery_date = trim($order['delivery_date'] ?? $order['Delivery Date'] ?? '');

$order_date_display = 'N/A';
if ($order_date !== '') {
    $order_ts = strtotime($order_date);
    $order_date_display = $order_ts ? date('M d, Y H:i', $order_ts) : $order_date;
}

$delivery_date_display = 'N/A';
if ($delivery_date !== '') {
    $delivery_ts = strtotime($delivery_date);
    $delivery_date_display = $delivery_ts ? date('l, M d, Y', $delivery_ts) : $delivery_date;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .detail-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .info-card {
            background: #ffffff;
            border: 1px solid #e8ecef;
            border-left: 4px solid #1f6b3f;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-card h5 {
            margin-bottom: 20px;
            font-weight: 700;
            color: #0f3c2a;
        }
        .info-row {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eef2f4;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .info-value {
            font-size: 1.05rem;
            font-weight: 500;
            margin-top: 4px;
            color: #1d2a22;
        }
        .status-section {
            background: #f8f9fa;
            border: 1px solid #e8ecef;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .status-badge {
            font-size: 1rem;
            padding: 10px 18px;
            border-radius: 999px;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="detail-container">
        <div class="mb-4">
            <a href="view_orders.php" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <h1 class="mb-3">
            <i class="fas fa-receipt"></i> Order #<?php echo htmlspecialchars((string) ($order['id'] ?? '')); ?>
        </h1>

        <?php if ($message !== ''): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="info-card">
                    <h5><i class="fas fa-user me-2"></i>Who Made This Order</h5>
                    <div class="info-row">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($customer_name); ?></div>
                    </div>
                    <?php if (!empty($order['username'])): ?>
                        <div class="info-row">
                            <div class="info-label">Username</div>
                            <div class="info-value">@<?php echo htmlspecialchars($order['username']); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($customer_email !== ''): ?>
                        <div class="info-row">
                            <div class="info-label">Email</div>
                            <div class="info-value"><a href="mailto:<?php echo htmlspecialchars($customer_email); ?>"><?php echo htmlspecialchars($customer_email); ?></a></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($customer_phone !== ''): ?>
                        <div class="info-row">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><a href="tel:<?php echo htmlspecialchars($customer_phone); ?>"><?php echo htmlspecialchars($customer_phone); ?></a></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="info-card">
                    <h5><i class="fas fa-clock me-2"></i>Order Timeline</h5>
                    <div class="info-row">
                        <div class="info-label">Order Made On</div>
                        <div class="info-value"><?php echo htmlspecialchars($order_date_display); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Should Be Delivered On</div>
                        <div class="info-value"><?php echo htmlspecialchars($delivery_date_display); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Delivery Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($delivery_address !== '' ? $delivery_address : 'N/A'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-card mb-4">
            <h5><i class="fas fa-cube me-2"></i>Product Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Product</div>
                        <div class="info-value"><?php echo htmlspecialchars((string) ($order['product'] ?? 'N/A')); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Quantity</div>
                        <div class="info-value"><?php echo htmlspecialchars((string) ($order['quantity'] ?? 'N/A')); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="status-section">
            <h5 class="mb-3"><i class="fas fa-tasks"></i> Order Status</h5>

            <span class="badge bg-<?php echo htmlspecialchars($status_colors[$current_status] ?? 'secondary'); ?> status-badge">
                <i class="fas <?php echo htmlspecialchars($status_icons[$current_status] ?? 'fa-info-circle'); ?>"></i>
                <?php echo htmlspecialchars($status_labels[$current_status] ?? ucfirst($current_status)); ?>
            </span>

            <div class="quick-actions mt-3">
                <?php if ($current_status === 'pending'): ?>
                    <form method="POST" class="m-0">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-thumbs-up me-1"></i>Approve Order</button>
                    </form>
                <?php endif; ?>

                <?php if ($current_status === 'approved'): ?>
                    <form method="POST" class="m-0">
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check-circle me-1"></i>Mark Delivered</button>
                    </form>
                <?php endif; ?>

                <?php if (in_array($current_status, ['pending', 'approved'], true)): ?>
                    <form method="POST" class="m-0" onsubmit="return confirm('Cancel this order?');">
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-times-circle me-1"></i>Cancel</button>
                    </form>
                <?php endif; ?>
            </div>

            <form method="POST" class="row g-2 align-items-center mt-4">
                <div class="col-sm-6 col-md-4">
                    <select name="status" class="form-select">
                        <option value="pending" <?php echo $current_status === 'pending' ? 'selected' : ''; ?>>New</option>
                        <option value="approved" <?php echo $current_status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="delivered" <?php echo $current_status === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $current_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-success">
                        <i class="fas fa-save me-1"></i>Update Status
                    </button>
                </div>
            </form>
        </div>

        <div class="action-buttons">
            <a href="view_orders.php" class="btn btn-primary">
                <i class="fas fa-list"></i> View All Orders
            </a>
            <a href="admin.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
