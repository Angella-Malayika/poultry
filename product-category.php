<?php
require_once 'auth_required.php';
include("connection.php");

// Get category from URL
$category_slug = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'feeds';

// Fetch category data from database
$cat_sql = "SELECT * FROM categories WHERE slug = '$category_slug' AND is_active = 1";
$cat_result = mysqli_query($conn, $cat_sql);

// If category not found, default to 'feeds'
if (!$cat_result || mysqli_num_rows($cat_result) == 0) {
    $category_slug = 'feeds';
    $cat_sql = "SELECT * FROM categories WHERE slug = '$category_slug' AND is_active = 1";
    $cat_result = mysqli_query($conn, $cat_sql);
}

$category_data = mysqli_fetch_assoc($cat_result);

// Fetch products for this category
$products_sql = "SELECT * FROM products WHERE category_id = '{$category_data['id']}' AND is_active = 1 ORDER BY sort_order ASC";
$products_result = mysqli_query($conn, $products_sql);

$products = [];
if ($products_result) {
    while ($product = mysqli_fetch_assoc($products_result)) {
        $products[] = $product;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category_data['title']; ?> | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        .category-hero {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            color: white;
            padding: 4rem 0;
        }
        .category-hero h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .category-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(46, 125, 50, 0.2);
        }
        .product-card img {
            height: 250px;
            object-fit: cover;
        }
        .product-card .card-body {
            padding: 1.5rem;
        }
        .product-card h5 {
            color: #2e7d32;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .product-card .price {
            color: #2e7d32;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 1rem 0;
        }
        .btn-view {
            background-color: #2e7d32;
            color: white;
            border: none;
            width: 100%;
            margin-top: 0.5rem;
        }
        .btn-view:hover {
            background-color: #1b5e20;
            color: white;
        }
        .breadcrumb {
            background-color: #f9fbe7;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .category-intro {
            background-color: #f9fbe7;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid #2e7d32;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Category Hero Section -->
    <section class="category-hero">
        <div class="container">
            <h1><i class="<?php echo $category_data['icon']; ?> me-2"></i><?php echo $category_data['title']; ?></h1>
            <p><?php echo $category_data['description']; ?></p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="product.php">Products</a></li>
                    <li class="breadcrumb-item active"><?php echo $category_data['title']; ?></li>
                </ol>
            </nav>

            <!-- Category Introduction -->
            <div class="category-intro">
                <h3 style="color: #2e7d32; margin-bottom: 1rem;">About <?php echo $category_data['title']; ?></h3>
                <p style="color: #333; margin-bottom: 0;">
                    Explore our complete range of <?php echo strtolower($category_data['title']); ?>. 
                    Each product is carefully selected and quality-tested to ensure the best results for your farming operation. 
                    Click on any product to view detailed information and to place an order.
                </p>
            </div>

            <!-- Products Grid -->
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card product-card">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars($product['description']); ?></p>
                                <a href="product-details.php?product=<?php echo htmlspecialchars($product['slug']); ?>" class="btn btn-view btn-sm">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Call to Action -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="alert" style="background-color: #f9fbe7; border-left: 4px solid #2e7d32; padding: 2rem;">
                        <h4 style="color: #2e7d32; margin-bottom: 1rem;">Need Help Choosing the Right Product?</h4>
                        <p style="color: #333; margin-bottom: 1rem;">
                            Our expert team is ready to help you select the best products for your specific needs.
                        </p>
                        <a href="contact.php" class="btn" style="background-color: #2e7d32; color: white;">
                            <i class="fas fa-phone me-2"></i>Contact Us for Expert Advice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
