<?php
// Product details data
$products = [
    'soya' => [
        'name' => 'Soya',
        'image' => './images/soya b.jpeg',
        'description' => 'High-quality soya bean meal, rich in protein and essential for optimal poultry growth.',
        'benefits' => [
            'High protein content (44-48%)',
            'Essential amino acids for growth',
            'Improves feed conversion ratio',
            'Enhances egg production in layers',
            'Supports muscle development'
        ],
        'usage' => 'Mix with other feed ingredients as a protein supplement. Recommended inclusion: 15-25% in poultry diets.',
        'packaging' => '25kg, 50kg bags',
        // 'price' => 'UGX 85,000 per 50kg bag',
        'storage' => 'Store in cool, dry place. Use within 3 months of opening.'
    ],
    'grower' => [
        'name' => 'Grower Mash',
        'image' => './images/soya.jpeg',
        'description' => 'Complete feed formulation for growing chickens from 8 weeks to point of lay.',
        'benefits' => [
            'Balanced nutrients for optimal growth',
            'Supports skeletal development',
            'Improves immunity',
            'Prepares birds for laying',
            'Contains vitamins and minerals'
        ],
        'usage' => 'Feed from 8-18 weeks. Provide fresh water at all times. Expected consumption: 60-80g per bird per day.',
        'packaging' => '50kg bags',
        'storage' => 'Keep dry and protected from pests. Use within 2 months.'
    ],
    'layer' => [
        'name' => 'Layer Mash',
        'image' => './images/layer.jpeg',
        'description' => 'Premium quality feed specially formulated for laying hens to maximize egg production.',
        'benefits' => [
            'High calcium for strong eggshells',
            'Optimal protein for consistent laying',
            'Enhanced egg quality and size',
            'Vitamins for bird health',
            'Improves feed efficiency'
        ],
        'usage' => 'Feed from point of lay onwards. Consumption: 110-130g per bird per day. Ensure constant water supply.',
        'packaging' => '50kg bags',
        'storage' => 'Store in ventilated area away from moisture. Best used within 6 weeks.'
    ],
    'broiler' => [
        'name' => 'Broiler Feed',
        'image' => './images/broiler.jpeg',
        'description' => 'High-energy feed designed for rapid weight gain in meat-type chickens.',
        'benefits' => [
            'Accelerated growth rate',
            'High energy concentration',
            'Excellent feed conversion',
            'Improved meat quality',
            'Contains growth promoters'
        ],
        'usage' => 'Starter (0-3 weeks), Grower (3-6 weeks), Finisher (6 weeks+). Feed ad-libitum with clean water.',
        'packaging' => '50kg bags',
        'storage' => 'Keep in cool, dry conditions. Use fresh feed for best results.'
    ],
    'sunflower' => [
        'name' => 'Sunflower',
        'image' => './images/sun.jpeg',
        'description' => 'Sunflower cake - excellent protein and energy source for livestock.',
        'benefits' => [
            'Rich in protein (28-32%)',
            'Good energy source',
            'Improves coat condition',
            'Cost-effective feed ingredient',
            'Highly palatable'
        ],
        'usage' => 'Can be included up to 20% in poultry and livestock rations.',
        'packaging' => '50kg bags',
        'storage' => 'Store in dry conditions to prevent mold growth.'
    ],
    'lime' => [
        'name' => 'Lime (Calcium Supplement)',
        'image' => './images/lime.jpeg',
        'description' => 'Agricultural lime providing essential calcium for strong bones and eggshells.',
        'benefits' => [
            'Prevents calcium deficiency',
            'Strengthens eggshells',
            'Supports bone development',
            'Improves digestive health',
            'Neutralizes soil acidity'
        ],
        'usage' => 'Add to layer feeds or provide as free choice. Also used for soil treatment.',
        'packaging' => '25kg, 50kg bags',
        'storage' => 'Keep dry. Long shelf life when properly stored.'
    ],
    'pig' => [
        'name' => 'Pig Feed',
        'image' => './images/pig.jpeg',
        'description' => 'Complete nutrition for all stages of pig production.',
        'benefits' => [
            'Promotes rapid weight gain',
            'Balanced amino acid profile',
            'Improves meat quality',
            'Supports reproductive health',
            'Contains essential minerals'
        ],
        'usage' => 'Available in starter, grower, and finisher formulations. Feed according to pig age and weight.',
        'packaging' => '50kg bags',
        'storage' => 'Store in clean, dry area. Protect from rodents.'
    ],
    'cattle' => [
        'name' => 'Dairy & Beef Cattle Feed',
        'image' => './images/catle.jpeg',
        'description' => 'High-quality feed for optimal milk production and beef cattle growth.',
        'benefits' => [
            'Increases milk yield',
            'Improves milk quality',
            'Supports weight gain in beef cattle',
            'Rich in energy and protein',
            'Contains minerals for health'
        ],
        'usage' => 'Dairy: 3-5kg per cow per day. Beef: 2-4kg per animal per day. Supplement with roughage.',
        'packaging' => '70kg bags',
        'storage' => 'Keep in well-ventilated storage. Use within 8 weeks.'
    ],
    'goat' => [
        'name' => 'Goat Feed',
        'image' => './images/goat-feed-performance-40kg.jpg',
        'description' => 'Specially formulated feed for goats at all production stages.',
        'benefits' => [
            'Supports rapid growth',
            'Improves milk production',
            'Enhances reproductive performance',
            'Balanced nutrition',
            'Boosts immunity'
        ],
        'usage' => 'Feed 300-500g per goat per day depending on size and production level. Provide browse/hay.',
        'packaging' => '40kg bags',
        'storage' => 'Store in dry, cool place away from direct sunlight.'
    ],
    'chicks' => [
        'name' => 'One-Day-Old Chicks',
        'image' => './images/images.jpeg',
        'description' => 'Healthy, vaccinated day-old chicks from quality breeding stock.',
        'benefits' => [
            'Vaccinated against Marek\'s disease',
            'High survival rate (95%+)',
            'Fast-growing breeds',
            'Good laying potential (layers)',
            'Expert breeding selection'
        ],
        'usage' => 'Brooder temperature: 32-35°C for first week. Provide chick starter feed and clean water immediately.',
        'packaging' => 'Minimum order: 50 chicks',
        'storage' => 'N/A - Live chicks delivered fresh'
    ]
];

// Get product from URL
$product_key = isset($_GET['product']) ? $_GET['product'] : '';
$product = isset($products[$product_key]) ? $products[$product_key] : null;

// Redirect if product not found
if (!$product) {
    header('Location: product.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Product Details | Kalungu Quality Feeds</title>
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
                    <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
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
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-detail-image">
                </div>

                <!-- Product Info -->
                <div class="col-lg-7">
                    <h1 style="color: #2e7d32;"><?php echo $product['name']; ?></h1>
                    <p class="lead" style="color: #333;"><?php echo $product['description']; ?></p>

                    <p style="color: #666;">Packaging: <?php echo $product['packaging']; ?></p>

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
                            <?php foreach ($product['benefits'] as $benefit): ?>
                                <li><?php echo $benefit; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="detail-section">
                        <h3><i class="fas fa-info-circle me-2"></i>Usage Instructions</h3>
                        <p style="color: #333;"><?php echo $product['usage']; ?></p>
                    </div>

                    <div class="detail-section">
                        <h3><i class="fas fa-warehouse me-2"></i>Storage Guidelines</h3>
                        <p style="color: #333;"><?php echo $product['storage']; ?></p>
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

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
