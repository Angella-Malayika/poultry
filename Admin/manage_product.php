<?php
// Admin/manage_products.php – Product management list
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth_required.php';
require_once dirname(__DIR__) . '/connection.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit();
}

$message = '';
$message_type = 'success';

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_action']) && $_POST['product_action'] === 'delete') {
    $product_id = (int) ($_POST['product_id'] ?? 0);
    if ($product_id > 0) {
        $stmt = $conn->prepare('SELECT image FROM products WHERE id = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            $delete_stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
            if ($delete_stmt) {
                $delete_stmt->bind_param('i', $product_id);
                if ($delete_stmt->execute()) {
                    $image_path = (string) ($row['image'] ?? '');
                    if ($image_path !== '') {
                        $file_path = dirname(__DIR__) . '/' . ltrim($image_path, '/');
                        if (is_file($file_path)) {
                            @unlink($file_path);
                        }
                    }
                    $message = 'Product deleted successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to delete the product.';
                    $message_type = 'danger';
                }
                $delete_stmt->close();
            }
        }
    } else {
        $message = 'Invalid delete request.';
        $message_type = 'danger';
    }
}

// Fetch all products
$products = [];
$products_result = mysqli_query(
    $conn,
    'SELECT p.id, p.name, p.slug, p.image, p.is_active, p.created_at, c.title AS category_title '
    . 'FROM products p '
    . 'LEFT JOIN categories c ON p.category_id = c.id '
    . 'ORDER BY p.created_at DESC, p.id DESC '
    . 'LIMIT 50'
);
if ($products_result) {
    while ($row = mysqli_fetch_assoc($products_result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --leaf-900: #0f3c2a;
            --leaf-700: #1f6b3f;
            --leaf-100: #edf8f1;
            --ink: #173026;
        }
        body {
            background: radial-gradient(circle at top left, rgba(31, 107, 63, 0.12), transparent 34%),
                        linear-gradient(180deg, #f7fbf8 0%, #eef5ef 100%);
            color: var(--ink);
        }
        .sidebar {
            background: linear-gradient(135deg, var(--leaf-900), var(--leaf-700));
            min-height: 100vh;
            color: #fff;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 10px;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.14);
            color: #fff;
        }
        .sidebar .nav-link i { margin-right: 10px; font-size: 1.05rem; }
        .brand {
            padding: 10px 20px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            margin-bottom: 15px;
        }
        .brand h4 { margin: 0; font-weight: 700; }
        .brand small { opacity: 0.7; }
        .top-bar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(15, 60, 42, 0.08);
            padding: 15px 25px;
        }
        .table th { background: #1b5e20; color: #fff; }
        .table td { vertical-align: middle; }
        .product-img { max-height: 60px; width: auto; }
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
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Add Product</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/Admin/manage_products.php"><i class="bi bi-list-ul"></i> Manage Products</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_complaints.php"><i class="bi bi-chat-square-text"></i> Complaints</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/login_activity.php"><i class="bi bi-person-check"></i> Login Activity</a>
            <a class="nav-link text-danger mt-3" href="<?php echo BASE_URL; ?>/Admin/adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Manage Products</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>

        <div class="p-4">
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>All Products (<?php echo count($products); ?>)</h4>
                <a href="<?php echo BASE_URL; ?>/Admin/upload_photo.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add New Product</a>
            </div>

            <?php if (!empty($products)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>/<?php echo ltrim(!empty($product['image']) ? $product['image'] : 'images/fs.broiler-chicks.avif', '/'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img rounded">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($product['slug']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category_title'] ?? 'Uncategorized'); ?></td>
                                    <td>
                                        <?php if ((int)($product['is_active'] ?? 0) === 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Hidden</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime((string) $product['created_at']))); ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="<?php echo BASE_URL; ?>/Admin/upload_photo.php?edit=<?php echo (int) $product['id']; ?>" class="btn btn-outline-success btn-sm">Edit</a>
                                            <form method="POST" class="m-0" onsubmit="return confirm('Delete this product?');">
                                                <input type="hidden" name="product_action" value="delete">
                                                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-light border">No products available yet.</div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>