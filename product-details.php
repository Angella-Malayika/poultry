
<?php
require_once 'auth_required.php';
include("connection.php");

// Get product from URL
$product_slug = isset($_GET['product']) ? mysqli_real_escape_string($conn, $_GET['product']) : '';

// Fetch product from database
$sql = "SELECT p.*, c.title as category_title, c.slug as category_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.slug = '$product_slug' AND p.is_active = 1";

$result = mysqli_query($conn, $sql);

// Redirect if product not found
if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: product.php');
    exit;
}

$product = mysqli_fetch_assoc($result);

// Convert pipe-delimited benefits to array
$benefits = !empty($product['benefits']) ? explode('|', $product['benefits']) : [];
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        .product-detail-hero {
            background: linear-gradient(135deg, #f9fbe7 0%, #ffffff 100%);
            padding: 3rem 0;
        }
        .product-detail-image {
            max-width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .detail-section {
            margin-bottom: 2rem;
        }
        .detail-section h3 {
            color: #2e7d32;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .benefit-list {
            list-style: none;
            padding: 0;
        }
        .benefit-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .benefit-list li:before {
            content: "✓";
            color: #2e7d32;
            font-weight: bold;
            margin-right: 10px;
        }
        .cta-buttons {
            margin-top: 2rem;
        }
        .cta-buttons .btn {
            margin-right: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Product Hero Section -->
    <section class="product-detail-hero">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="product.php">Products</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Product Details -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Product Image -->
                <div class="col-lg-5 mb-4">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-detail-image">
                </div>

                <!-- Product Info -->
                <div class="col-lg-7">
                    <h1 style="color: #2e7d32;"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="lead" style="color: #333;"><?php echo htmlspecialchars($product['description']); ?></p>

                    <p style="color: #666;">Packaging: <?php echo htmlspecialchars($product['packaging']); ?></p>

                    <!-- Call to Action -->
                    <div class="cta-buttons">
                        <a href="order.php" class="btn btn-lg" style="background-color: #2e7d32; color: white; border: none;">
                            <i class="fas fa-shopping-cart me-2"></i>Order Now
                        </a>
                        <a href="contact.php" class="btn btn-lg" style="background-color: white; color: #2e7d32; border: 2px solid #2e7d32;">
                            <i class="fas fa-phone me-2"></i>Contact Us
                        </a>
                        <a href="product.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Back to Products
                        </a>
                    </div>
                </div>
            </div>

            <!-- Detailed Information Tabs -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="detail-section">
                        <h3><i class="fas fa-star me-2"></i>Key Benefits</h3>
                        <ul class="benefit-list">
                            <?php foreach ($benefits as $benefit): ?>
                                <li><?php echo htmlspecialchars($benefit); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="detail-section">
                        <h3><i class="fas fa-info-circle me-2"></i>Usage Instructions</h3>
                        <p style="color: #333;"><?php echo htmlspecialchars($product['usage_info']); ?></p>
                    </div>

                    <div class="detail-section">
                        <h3><i class="fas fa-warehouse me-2"></i>Storage Guidelines</h3>
                        <p style="color: #333;"><?php echo htmlspecialchars($product['storage']); ?></p>
                    </div>

                    <!-- Additional Info -->
                    <div class="alert" style="background-color: #f9fbe7; border-left: 4px solid #2e7d32;">
                        <h5 style="color: #2e7d32;"><i class="fas fa-truck me-2"></i>Delivery Information</h5>
                        <p class="mb-0" style="color: #333;">
                            We offer fast delivery across Kalungu and surrounding areas. Orders are typically delivered within 24 hours. 
                            Minimum order quantities may apply for certain products. Contact us for bulk order and delivery details.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include  'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
