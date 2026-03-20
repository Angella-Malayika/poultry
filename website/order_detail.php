<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include("connection.php");

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$message_type = 'info';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = '$order_id'";

    if (mysqli_query($conn, $update_sql)) {
        $message = 'Order status updated successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error updating status: ' . mysqli_error($conn);
        $message_type = 'danger';
    }
}

// Fetch order details
$sql = "SELECT o.*, u.email, u.username FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.id = '$order_id'";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: view_orders.php');
    exit();
}

$order = mysqli_fetch_assoc($result);
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
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-card.customer {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .info-card.delivery {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .info-card.product {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .info-card h5 {
            margin-bottom: 20px;
            font-weight: 700;
        }
        .info-row {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            margin-top: 5px;
        }
        .communication-links {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .communication-links a {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
            font-size: 0.9rem;
        }
        .communication-links a:hover {
            background: rgba(255,255,255,0.4);
            color: white;
        }
        .status-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .status-badge {
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .breadcrumb-custom {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="detail-container">
        <div class="breadcrumb-custom">
            <a href="view_orders.php" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <h1 class="mb-4">
            <i class="fas fa-receipt"></i> Order #<?php echo htmlspecialchars($order['id']); ?>
        </h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <!-- Customer Information -->
            <div class="col-lg-6">
                <div class="info-card customer">
                    <h5><i class="fas fa-user me-2"></i>Customer Information</h5>

                    <div class="info-row">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['full_name']); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Username</div>
                        <div class="info-value">@<?php echo htmlspecialchars($order['username']); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['email']); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['phone']); ?></div>
                    </div>

                    <div class="communication-links">
                        <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>" target="_blank">
                            <i class="fas fa-envelope"></i> Send Email
                        </a>
                        <a href="tel:<?php echo htmlspecialchars($order['phone']); ?>">
                            <i class="fas fa-phone"></i> Call
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="col-lg-6">
                <div class="info-card delivery">
                    <h5><i class="fas fa-map-marked-alt me-2"></i>Delivery Information</h5>

                    <div class="info-row">
                        <div class="info-label">Delivery Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['delivery_address']); ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Preferred Delivery Date</div>
                        <div class="info-value">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo date('l, F d, Y', strtotime($order['delivery_date'])); ?>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Order Placed On</div>
                        <div class="info-value">
                            <i class="fas fa-clock"></i>
                            <?php echo date('M d, Y @ H:i', strtotime($order['order_date'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="info-card product mb-4">
            <h5><i class="fas fa-cube me-2"></i>Product Details</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Product Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['product']); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Quantity</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['quantity']); ?> units</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Management -->
        <div class="status-section">
            <h5 class="mb-3"><i class="fas fa-tasks"></i> Order Status</h5>

            <form method="POST" class="d-flex gap-2 align-items-center">
                <?php
                $status_color = '';
                $status_icon = '';
                if ($order['status'] == 'pending') {
                    $status_color = 'warning';
                    $status_icon = 'fa-hourglass-half';
                } elseif ($order['status'] == 'completed') {
                    $status_color = 'success';
                    $status_icon = 'fa-check-circle';
                } elseif ($order['status'] == 'cancelled') {
                    $status_color = 'danger';
                    $status_icon = 'fa-times-circle';
                } else {
                    $status_color = 'secondary';
                    $status_icon = 'fa-info-circle';
                }
                ?>

                <span class="badge bg-<?php echo $status_color; ?> status-badge">
                    <i class="fas <?php echo $status_icon; ?>"></i>
                    <?php echo ucfirst($order['status']); ?>
                </span>

                <select name="status" class="form-select" style="max-width: 200px;">
                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Status
                </button>
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
