<?php
session_start();
require_once 'auth_required.php';
include("connection.php");

// Fetch current user's orders with their email
$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.user_id = '$user_id'
        ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $sql);
$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="joy.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .orders-container {
            padding: 40px 20px;
        }
        .order-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .order-id {
            font-weight: 700;
            color: #2c5f2d;
            font-size: 1.1rem;
        }
        .order-status {
            font-weight: 600;
        }
        .order-detail-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }
        .detail-item {
            padding: 12px;
            background: #f8f9fa;
            border-left: 4px solid #2c5f2d;
            border-radius: 6px;
        }
        .detail-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 1rem;
            color: #333;
            font-weight: 500;
        }
        .badge-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .no-orders {
            text-align: center;
            padding: 60px 20px;
        }
        .no-orders i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        .back-link {
            margin: 30px 0;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="orders-container">
    <div class="container">
        <div class="back-link">
            <a href="index.php" class="btn btn-outline-success">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>

        <h1 class="mb-4">
            <i class="fas fa-boxes"></i> My Orders
        </h1>

        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo htmlspecialchars($order['id']); ?></div>
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i>
                                Placed: <?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?>
                            </small>
                        </div>
                        <div>
                            <?php
                            $status_class = '';
                            $status_icon = '';
                            if ($order['status'] == 'pending') {
                                $status_class = 'bg-warning text-dark';
                                $status_icon = 'fa-hourglass-half';
                            } elseif ($order['status'] == 'completed') {
                                $status_class = 'bg-success text-white';
                                $status_icon = 'fa-check-circle';
                            } else {
                                $status_class = 'bg-secondary text-white';
                                $status_icon = 'fa-info-circle';
                            }
                            ?>
                            <span class="badge-status <?php echo $status_class; ?>">
                                <i class="fas <?php echo $status_icon; ?>"></i>
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-detail-row">
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-user"></i> Full Name</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['full_name']); ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['email']); ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-phone"></i> Phone</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['phone']); ?></div>
                        </div>
                    </div>

                    <div class="order-detail-row">
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-cube"></i> Product</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['product']); ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-boxes"></i> Quantity</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['quantity']); ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-calendar-alt"></i> Delivery Date</div>
                            <div class="detail-value"><?php echo date('M d, Y', strtotime($order['delivery_date'])); ?></div>
                        </div>
                    </div>

                    <div class="order-detail-row">
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Delivery Address</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['delivery_address']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="order-card no-orders">
                <i class="fas fa-inbox"></i>
                <h3>No Orders Yet</h3>
                <p class="text-muted">You haven't placed any orders yet. Start shopping today!</p>
                <a href="order.php" class="btn btn-success mt-3">
                    <i class="fas fa-shopping-cart"></i> Place an Order
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
