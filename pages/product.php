<?php
// pages/product.php – Fixed image paths (absolute using BASE_URL)
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/connection.php';

$page_title = 'Products | Kalungu Quality Feeds';
$fallback_image = BASE_URL . '/images/fs.broiler-chicks.avif';

// Helper function to get absolute image URL
function get_absolute_image_url($image_path) {
    global $fallback_image;
    if (empty($image_path)) {
        return $fallback_image;
    }
    // If it already starts with http://, https://, or / (absolute), return as is
    if (preg_match('/^(https?:|\/)/i', $image_path)) {
        return $image_path;
    }
    // Otherwise prepend BASE_URL and a slash
    return BASE_URL . '/' . ltrim($image_path, '/');
}

$categories = [];
$categories_result = mysqli_query($conn, "SELECT id, slug, title, description, icon FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, title ASC");
if ($categories_result) {
    while ($category = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $category;
    }
}

$all_products = [];
$product_query = "
    SELECT p.id, p.slug, p.name, p.image, p.description, p.created_at, c.title AS category_title, c.slug AS category_slug
    FROM products p
    INNER JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 AND c.is_active = 1
    ORDER BY p.created_at DESC, p.sort_order ASC, p.name ASC
";
$product_result = mysqli_query($conn, $product_query);
if ($product_result) {
    while ($product = mysqli_fetch_assoc($product_result)) {
        $all_products[] = $product;
    }
}

$recent_products = array_slice($all_products, 0, 6);
$total_products = count($all_products);
$total_categories = count($categories);
$latest_update = !empty($all_products) ? date('M j, Y', strtotime($all_products[0]['created_at'])) : 'Today';

function truncate_text($text, $length = 120) {
    $text = trim((string) $text);
    if ($text === '') return 'Fresh stock available in the store.';
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $length) return $text;
        return rtrim(mb_substr($text, 0, $length - 3)) . '...';
    }
    if (strlen($text) <= $length) return $text;
    return rtrim(substr($text, 0, $length - 3)) . '...';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* (keep all your existing styles – unchanged) */
        body {
            background: radial-gradient(circle at top left, rgba(46,125,50,0.08), transparent 32%),
                        linear-gradient(180deg, #fbfcf7 0%, #ffffff 42%, #f7faf4 100%);
        }
        .store-hero { padding: 4rem 0 2.5rem; }
        .store-kicker { display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.45rem 0.9rem; border-radius: 999px; background: rgba(46,125,50,0.1); color: #245c28; font-weight: 700; }
        .store-hero h1 { color: #16361c; font-weight: 800; line-height: 1.05; max-width: 11ch; margin: 1rem 0; }
        .hero-card { border: 1px solid rgba(46,125,50,0.12); background: rgba(255,255,255,0.82); backdrop-filter: blur(12px); border-radius: 22px; }
        .metric { padding: 1.15rem; border-radius: 18px; background: #fff; border: 1px solid rgba(22,54,28,0.08); height: 100%; }
        .metric .value { display: block; color: #1d5723; font-size: 2rem; font-weight: 800; line-height: 1; }
        .product-card { border: 0; border-radius: 22px; overflow: hidden; background: #fff; box-shadow: 0 16px 40px rgba(20,40,24,0.08); transition: transform 0.24s ease; height: 100%; }
        .product-card:hover { transform: translateY(-6px); }
        .product-image { width: 100%; height: 240px; object-fit: cover; background: #edf3ec; }
        .product-body { padding: 1.25rem; }
        .category-pill, .new-pill { display: inline-flex; align-items: center; gap: 0.35rem; border-radius: 999px; padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 700; }
        .category-pill { background: rgba(46,125,50,0.1); color: #245c28; }
        .new-pill { background: #17361d; color: #fff; }
        .product-title { color: #16361c; font-weight: 800; margin: 0.8rem 0 0.45rem; }
        .product-link { color: #2e7d32; font-weight: 700; text-decoration: none; }
        .product-link:hover { color: #1f5d25; text-decoration: underline; }
        .product-actions { display: flex; flex-wrap: wrap; gap: 0.6rem; justify-content: space-between; align-items: center; }
        .btn-cart-action { background: #2e7d32; color: #fff; border-radius: 999px; padding: 0.55rem 1rem; font-weight: 700; text-decoration: none; }
        .btn-cart-action:hover { background: #1f5d25; color: #fff; }
        .catalog-note { background: linear-gradient(135deg, rgba(46,125,50,0.1), rgba(243,250,242,0.9)); border-radius: 24px; padding: 1.5rem; }
        .section-title { color: #17361d; font-weight: 800; margin-bottom: 0.5rem; }
        .section-copy { color: #5f6d62; margin-bottom: 1.5rem; }
        @media (max-width: 768px) { .store-hero { padding: 2rem 0; } }
    </style>
</head>
<body>
    <?php include dirname(__DIR__) . '/includes/header.php'; ?>

    <main>
        <section class="store-hero">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <span class="store-kicker"><i class="fa-solid fa-store"></i> Live store catalog</span>
                        <h1>Browse products and see new stock as it is added.</h1>
                        <p>Explore our full range of available products and latest additions. We keep this page updated in real time so you always shop our freshest stock. Add products to your cart, then review them before you place an order.</p>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="#new-products" class="btn btn-success btn-lg px-4"><i class="fa-solid fa-sparkles me-2"></i>View New Products</a>
                            <a href="#all-products" class="btn btn-outline-success btn-lg px-4"><i class="fa-solid fa-cube me-2"></i>Browse All Products</a>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="hero-card p-4 p-md-4">
                            <div class="row g-3">
                                <div class="col-6"><div class="metric"><span class="value"><?php echo (int) $total_products; ?></span><div class="label">Active products</div></div></div>
                                <div class="col-6"><div class="metric"><span class="value"><?php echo (int) $total_categories; ?></span><div class="label">Product categories</div></div></div>
                                <div class="col-12"><div class="metric"><span class="value" style="font-size:1.45rem;"><?php echo htmlspecialchars($latest_update); ?></span><div class="label">Latest product added</div></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="pb-2">
            <div class="container">
                <div class="catalog-note">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-8">
                            <h2 class="section-title mb-2">Products update automatically</h2>
                            <p class="mb-0 section-copy">We continuously update our stock so you never miss out on our newest products. Check back often to see the latest additions to our store and find the perfect feed for your poultry.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="<?php echo BASE_URL; ?>/pages/contact.php" class="btn btn-dark btn-lg px-4"><i class="fa-solid fa-phone me-2"></i>Ask About Stock</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="new-products" class="py-5">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div><h2 class="section-title">New products</h2><p class="section-copy mb-0">The latest items added to the store appear here first.</p></div>
                    <a href="#all-products" class="product-link">Jump to the full catalog</a>
                </div>

                <div class="row g-4">
                    <?php if (!empty($recent_products)): ?>
                        <?php foreach ($recent_products as $product): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="product-card">
                                    <!-- Fixed image URL: use get_absolute_image_url helper -->
                                    <img src="<?php echo get_absolute_image_url($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                    <div class="product-body d-flex flex-column">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="category-pill"><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($product['category_title']); ?></span>
                                            <?php if (strtotime($product['created_at']) >= strtotime('-2 months')): ?>
                                                <span class="new-pill"><i class="fa-solid fa-star"></i> New</span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="h5 product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="text-secondary mb-4"><?php echo htmlspecialchars(truncate_text($product['description'], 130)); ?></p>
                                        <div class="product-actions mt-auto">
                                            <a class="product-link" href="<?php echo BASE_URL; ?>/product-details.php?product=<?php echo urlencode($product['slug']); ?>">View details</a>
                                            <a class="btn-cart-action" href="<?php echo BASE_URL; ?>/add_to_cart.php?product=<?php echo urlencode($product['slug']); ?>">
                                                <i class="fa-solid fa-cart-plus me-1"></i>Add to Cart
                                            </a>
                                        </div>
                                        <small class="text-muted mt-3">Added <?php echo htmlspecialchars(date('M j', strtotime($product['created_at']))); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12"><div class="alert alert-light border">No products are available yet.</div></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="all-products" class="py-5">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div><h2 class="section-title">All products in the store</h2><p class="section-copy mb-0">Browse the full active catalog currently stored in the system.</p></div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($categories as $category): ?>
                            <a href="<?php echo BASE_URL; ?>/pages/product-category.php?category=<?php echo urlencode($category['slug']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3"><?php echo htmlspecialchars($category['title']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if (!empty($all_products)): ?>
                        <?php foreach ($all_products as $product): ?>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="product-card">
                                    <!-- Fixed image URL -->
                                    <img src="<?php echo get_absolute_image_url($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                    <div class="product-body d-flex flex-column">
                                        <span class="category-pill"><i class="fa-solid fa-layer-group"></i> <?php echo htmlspecialchars($product['category_title']); ?></span>
                                        <h3 class="h5 product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="text-secondary mb-4"><?php echo htmlspecialchars(truncate_text($product['description'], 110)); ?></p>
                                        <div class="product-actions mt-auto">
                                            <a class="product-link" href="<?php echo BASE_URL; ?>/product-details.php?product=<?php echo urlencode($product['slug']); ?>">Details</a>
                                            <a class="btn-cart-action" href="<?php echo BASE_URL; ?>/add_to_cart.php?product=<?php echo urlencode($product['slug']); ?>">
                                                <i class="fa-solid fa-cart-plus me-1"></i>Add to Cart
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12"><div class="alert alert-warning mb-0">No active products were found in the store.</div></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include dirname(__DIR__) . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>