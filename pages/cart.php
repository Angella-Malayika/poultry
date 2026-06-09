<?php
require_once '../auth_required.php';
include '../connection.php';
require_once __DIR__ . '/../includes/cart_helpers.php';

if (isset($_GET['remove'])) {
    poultry_cart_remove_item((string) $_GET['remove']);
    $_SESSION['cart_success'] = 'Item removed from your cart.';
    header('Location: cart.php');
    exit();
}

if (isset($_GET['clear'])) {
    poultry_cart_clear();
    $_SESSION['cart_success'] = 'Your cart has been cleared.';
    header('Location: cart.php');
    exit();
}

$cart_items = poultry_cart_get_items();
$cart_rows = [];

if (!empty($cart_items)) {
    $escaped_slugs = [];
    foreach (array_keys($cart_items) as $slug) {
        $escaped_slugs[] = "'" . mysqli_real_escape_string($conn, (string) $slug) . "'";
    }

    if (!empty($escaped_slugs)) {
        $cart_query = "SELECT slug, name, image, description FROM products WHERE is_active = 1 AND slug IN (" . implode(',', $escaped_slugs) . ") ORDER BY name ASC";
        $cart_result = mysqli_query($conn, $cart_query);
        if ($cart_result) {
            while ($row = mysqli_fetch_assoc($cart_result)) {
                $row['quantity'] = max(1, (int) ($cart_items[$row['slug']] ?? 1));
                $cart_rows[] = $row;
            }
        }
    }
}

$total_items = 0;
foreach ($cart_rows as $row) {
    $total_items += (int) ($row['quantity'] ?? 0);
}

$distinct_items = count($cart_rows);
$fallback_image = 'images/fs.broiler-chicks.avif';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/head.css">
    <link rel="stylesheet" href="./assets/foot.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(180deg, #f7faf4 0%, #ffffff 100%);
        }

        .cart-hero {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: #fff;
            padding: 4rem 0 2.5rem;
        }

        .cart-card {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(18, 32, 23, 0.08);
        }

        .cart-item {
            border-bottom: 1px solid #e8eee6;
            padding: 1.1rem 0;
        }

        .cart-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .cart-thumb {
            width: 92px;
            height: 92px;
            object-fit: cover;
            border-radius: 16px;
            background: #edf3ec;
        }

        .cart-summary {
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(243, 250, 242, 0.9));
            border: 1px solid rgba(46, 125, 50, 0.15);
            border-radius: 22px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <section class="cart-hero">
        <div class="container">
            <h1 class="mb-2"><i class="fa-solid fa-cart-shopping me-2"></i>Your Cart</h1>
            <p class="mb-0">Review the products you selected and continue to order when you are ready.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if (!empty($_SESSION['cart_success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['cart_success']); ?></div>
                <?php unset($_SESSION['cart_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['cart_error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['cart_error']); ?></div>
                <?php unset($_SESSION['cart_error']); ?>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card cart-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                                <div>
                                    <h2 class="h4 mb-1">Selected Products</h2>
                                    <p class="text-muted mb-0"><?php echo (int) $distinct_items; ?> distinct item(s) in cart</p>
                                </div>
                                <?php if ($distinct_items > 0): ?>
                                    <a class="btn btn-outline-danger" href="cart.php?clear=1"><i class="fa-solid fa-trash me-2"></i>Clear Cart</a>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($cart_rows)): ?>
                                <?php foreach ($cart_rows as $row): ?>
                                    <div class="cart-item d-flex flex-column flex-md-row gap-3 align-items-md-center">
                                        <img src="<?php echo htmlspecialchars(!empty($row['image']) ? $row['image'] : $fallback_image); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="cart-thumb">
                                        <div class="flex-grow-1">
                                            <h3 class="h5 mb-1"><?php echo htmlspecialchars($row['name']); ?></h3>
                                            <p class="text-muted mb-0 small"><?php echo htmlspecialchars($row['description'] ?? ''); ?></p>
                                        </div>
                                        <div class="text-md-end">
                                            <div class="fw-bold text-success">Qty: <?php echo (int) $row['quantity']; ?></div>
                                            <a class="btn btn-sm btn-outline-danger mt-2" href="cart.php?remove=<?php echo urlencode($row['slug']); ?>">
                                                <i class="fa-solid fa-xmark me-1"></i>Remove
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-light border mb-0">
                                    Your cart is empty. Browse products and add the ones you want.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cart-summary p-4 p-md-4">
                        <h3 class="h4 mb-3">Cart Summary</h3>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Distinct products</span>
                            <strong><?php echo (int) $distinct_items; ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total items chosen</span>
                            <strong><?php echo (int) $total_items; ?></strong>
                        </div>
                        <a href="order.php" class="btn btn-success w-100 mb-2 <?php echo $distinct_items === 0 ? 'disabled' : ''; ?>">
                            <i class="fa-solid fa-receipt me-2"></i>Proceed to Order
                        </a>
                        <a href="product.php" class="btn btn-outline-success w-100">
                            <i class="fa-solid fa-bag-shopping me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
