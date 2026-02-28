<?php
// Category data with products
$categories = [
    'broilers' => [
        'title' => 'Broiler Products',
        'description' => 'Complete solutions for broiler chicken farming',
        'icon' => 'fas fa-egg',
        'products' => [
            'broiler' => [
                'name' => 'Broiler Feed',
                'image' => './images/broiler.jpeg',
                'description' => 'High-energy feed designed for rapid weight gain in meat-type chickens.',
                // 'price' => 'UGX 105,000 per 50kg bag',
                'link' => 'product-details.php?product=broiler'
            ],
            'chicks' => [
                'name' => 'Broiler Day-Old Chicks',
                'image' => './images/images.jpeg',
                'description' => 'Healthy, vaccinated broiler chicks ready for farming.',
                // 'price' => 'UGX 2,500 per chick',
                'link' => 'product-details.php?product=chicks'
            ]
        ]
    ],
    'layers' => [
        'title' => 'Layer Products',
        'description' => 'Everything you need for egg production',
        'icon' => 'fas fa-dna',
        'products' => [
            'layer' => [
                'name' => 'Layer Mash',
                'image' => './images/layer.jpeg',
                'description' => 'Premium quality feed specially formulated for laying hens to maximize egg production.',
                // 'price' => 'UGX 100,000 per 50kg bag',
                'link' => 'product-details.php?product=layer'
            ],
            'lime' => [
                'name' => 'Lime (Calcium Supplement)',
                'image' => './images/lime.jpeg',
                'description' => 'Essential calcium for strong eggshells and bone development.',
                // 'price' => 'UGX 35,000 per 50kg bag',
                'link' => 'product-details.php?product=lime'
            ]
        ]
    ],
    'feeds' => [
        'title' => 'All Feed Products',
        'description' => 'Complete range of nutritional feeds for all livestock',
        'icon' => 'fas fa-bags-shopping',
        'products' => [
            'broiler' => [
                'name' => 'Broiler Feed',
                'image' => './images/broiler.jpeg',
                'description' => 'High-energy feed for rapid weight gain.',
                // 'price' => 'UGX 105,000 per 50kg bag',
                'link' => 'product-details.php?product=broiler'
            ],
            'grower' => [
                'name' => 'Grower Mash',
                'image' => './images/soya.jpeg',
                'description' => 'Complete feed for growing chickens from 8 weeks to point of lay.',
                // 'price' => 'UGX 95,000 per 50kg bag',
                'link' => 'product-details.php?product=grower'
            ],
            'layer' => [
                'name' => 'Layer Mash',
                'image' => './images/layer.jpeg',
                'description' => 'Premium feed for laying hens to maximize egg production.',
                // 'price' => 'UGX 100,000 per 50kg bag',
                'link' => 'product-details.php?product=layer'
            ],
            'soya' => [
                'name' => 'Soya',
                'image' => './images/soya b.jpeg',
                'description' => 'High-quality soya bean meal for protein supplementation.',
                // 'price' => 'UGX 85,000 per 50kg bag',
                'link' => 'product-details.php?product=soya'
            ],
            'sunflower' => [
                'name' => 'Sunflower',
                'image' => './images/sun.jpeg',
                'description' => 'Excellent protein and energy source for livestock.',
                'price' => 'UGX 70,000 per 50kg bag',
                'link' => 'product-details.php?product=sunflower'
            ],
            'pig' => [
                'name' => 'Pig Feed',
                'image' => './images/pig.jpeg',
                'description' => 'Complete nutrition for all stages of pig production.',
                'price' => 'UGX 98,000 per 50kg bag',
                'link' => 'product-details.php?product=pig'
            ],
            'cattle' => [
                'name' => 'Dairy & Beef Cattle Feed',
                'image' => './images/catle.jpeg',
                'description' => 'High-quality feed for milk production and beef growth.',
                'price' => 'UGX 130,000 per 70kg bag',
                'link' => 'product-details.php?product=cattle'
            ],
            'goat' => [
                'name' => 'Goat Feed',
                'image' => './images/goat-feed-performance-40kg.jpg',
                'description' => 'Specially formulated for goats at all production stages.',
                'price' => 'UGX 80,000 per 40kg bag',
                'link' => 'product-details.php?product=goat'
            ]
        ]
    ],
    'chicks' => [
        'title' => 'Day-Old Chicks',
        'description' => 'Healthy, vaccinated chicks for poultry farming',
        'icon' => 'fas fa-heart',
        'products' => [
            'chicks-broiler' => [
                'name' => 'Broiler Day-Old Chicks',
                'image' => './images/images.jpeg',
                'description' => 'Fast-growing broiler chicks, vaccinated against Marek\'s disease.',
                'price' => 'UGX 2,500 per chick',
                'link' => 'product-details.php?product=chicks'
            ],
            'chicks-layer' => [
                'name' => 'Layer Day-Old Chicks',
                'image' => './images/images.jpeg',
                'description' => 'Quality layer chicks with excellent laying potential.',
                'price' => 'UGX 3,000 per chick',
                'link' => 'product-details.php?product=chicks'
            ]
        ]
    ],
    'consultancy' => [
        'title' => 'Expert Consultancy Services',
        'description' => 'Professional agricultural guidance for successful farming',
        'icon' => 'fas fa-users',
        'products' => [
            'farm-setup' => [
                'name' => 'Farm Setup & Planning',
                'image' => './images/farm-supervisor.jpg',
                'description' => 'Personalized consultation for setting up your poultry or livestock farm.',
                'price' => 'Contact for quote',
                'link' => 'contact.php'
            ],
            'nutrition' => [
                'name' => 'Nutrition & Feed Planning',
                'image' => './images/layer.jpeg',
                'description' => 'Expert guidance on optimal feed formulation for your livestock.',
                'price' => 'Contact for quote',
                'link' => 'contact.php'
            ],
            'health' => [
                'name' => 'Animal Health Management',
                'image' => './images/broiler.jpeg',
                'description' => 'Disease prevention, vaccination schedules, and health protocols.',
                'price' => 'Contact for quote',
                'link' => 'contact.php'
            ],
            'training' => [
                'name' => 'Farmer Training Programs',
                'image' => './images/farm-training.jpg',
                'description' => 'Monthly workshops on modern farming techniques and best practices.',
                'price' => 'Free for customers',
                'link' => 'contact.php'
            ]
        ]
    ]
];

// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : 'feeds';
$category_data = isset($categories[$category]) ? $categories[$category] : $categories['feeds'];
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
                <?php foreach ($category_data['products'] as $key => $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card product-card">
                            <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text text-muted small flex-grow-1"><?php echo $product['description']; ?></p>
                                <a href="<?php echo $product['link']; ?>" class="btn btn-view btn-sm">
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
