<?php
/**
 * Database Setup Script
 * Run this file once to create the categories and products tables
 * and populate them with sample data.
 *
 * Visit: http://localhost/project/setup_products.php
 */

session_start();

// Only allow admin users to run this script
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied. Please login as admin first.');
}

include("connection.php");

$messages = [];
$errors = [];

// Create categories table
$sql_categories = "CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-box',
    sort_order INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql_categories)) {
    $messages[] = "Categories table created successfully.";
} else {
    $errors[] = "Error creating categories table: " . mysqli_error($conn);
}

// Create products table
$sql_products = "CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    description TEXT,
    benefits TEXT,
    usage_info TEXT,
    packaging VARCHAR(100) DEFAULT NULL,
    storage TEXT,
    sort_order INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql_products)) {
    $messages[] = "Products table created successfully.";
} else {
    $errors[] = "Error creating products table: " . mysqli_error($conn);
}

// Insert categories
$categories_data = [
    ['broilers', 'Broiler Products', 'Complete solutions for broiler chicken farming', 'fas fa-egg', 1],
    ['layers', 'Layer Products', 'Everything you need for egg production', 'fas fa-dna', 2],
    ['feeds', 'All Feed Products', 'Complete range of nutritional feeds for all livestock', 'fas fa-shopping-bag', 3],
    ['kenbro-chicks', 'Kenbro Chicks', 'Dual-purpose Kenbro chicks suitable for meat and egg production', 'fas fa-feather-pointed', 4],
    ['pellets', 'Pellet Feeds', 'Balanced pellet feeds for efficient feeding and reduced wastage', 'fas fa-cubes', 5],
    ['feed-additives', 'Feed Additives', 'Performance enhancers and health-support additives for better feed results', 'fas fa-flask', 6],
    ['feed-concentrates', 'Feed Concentrates', 'Nutrient-dense concentrates for custom feed formulation', 'fas fa-vial', 7],
    ['chicks', 'Day-Old Chicks', 'Healthy, vaccinated chicks for poultry farming', 'fas fa-heart', 8],
    ['consultancy', 'Expert Consultancy Services', 'Professional agricultural guidance for successful farming', 'fas fa-users', 9]
];

foreach ($categories_data as $cat) {
    $slug = mysqli_real_escape_string($conn, $cat[0]);
    $title = mysqli_real_escape_string($conn, $cat[1]);
    $description = mysqli_real_escape_string($conn, $cat[2]);
    $icon = mysqli_real_escape_string($conn, $cat[3]);
    $sort_order = (int)$cat[4];

    $sql = "INSERT INTO categories (slug, title, description, icon, sort_order)
            VALUES ('$slug', '$title', '$description', '$icon', $sort_order)
            ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description)";

    if (mysqli_query($conn, $sql)) {
        $messages[] = "Category '$title' added/updated.";
    } else {
        $errors[] = "Error adding category '$title': " . mysqli_error($conn);
    }
}

// Insert products
$products_data = [
    // [category_slug, slug, name, image, description, benefits, usage_info, packaging, storage, price, sort_order]
    ['broilers', 'broiler', 'Broiler Feed', './images/broiler.jpeg',
     'High-energy feed designed for rapid weight gain in meat-type chickens.',
     'Accelerated growth rate|High energy concentration|Excellent feed conversion|Improved meat quality|Contains growth promoters',
     'Starter (0-3 weeks), Grower (3-6 weeks), Finisher (6 weeks+). Feed ad-libitum with clean water.',
     '50kg bags', 'Keep in cool, dry conditions. Use fresh feed for best results.', NULL, 1],

    ['layers', 'layer', 'Layer Mash', './images/layer.jpeg',
     'Premium quality feed specially formulated for laying hens to maximize egg production.',
     'High calcium for strong eggshells|Optimal protein for consistent laying|Enhanced egg quality and size|Vitamins for bird health|Improves feed efficiency',
     'Feed from point of lay onwards. Consumption: 110-130g per bird per day. Ensure constant water supply.',
     '50kg bags', 'Store in ventilated area away from moisture. Best used within 6 weeks.', NULL, 1],

    ['layers', 'lime', 'Lime (Calcium Supplement)', './images/lime.jpeg',
     'Essential calcium for strong eggshells and bone development.',
     'Prevents calcium deficiency|Strengthens eggshells|Supports bone development|Improves digestive health|Neutralizes soil acidity',
     'Add to layer feeds or provide as free choice. Also used for soil treatment.',
     '25kg, 50kg bags', 'Keep dry. Long shelf life when properly stored.', 'UGX 35,000 per 50kg bag', 2],

    ['feeds', 'grower', 'Grower Mash', './images/soya.jpeg',
     'Complete feed for growing chickens from 8 weeks to point of lay.',
     'Balanced nutrients for optimal growth|Supports skeletal development|Improves immunity|Prepares birds for laying|Contains vitamins and minerals',
     'Feed from 8-18 weeks. Provide fresh water at all times. Expected consumption: 60-80g per bird per day.',
     '50kg bags', 'Keep dry and protected from pests. Use within 2 months.', NULL, 1],

    ['feeds', 'soya', 'Soya', './images/soya b.jpeg',
     'High-quality soya bean meal for protein supplementation.',
     'High protein content (44-48%)|Essential amino acids for growth|Improves feed conversion ratio|Enhances egg production in layers|Supports muscle development',
     'Mix with other feed ingredients as a protein supplement. Recommended inclusion: 15-25% in poultry diets.',
     '25kg, 50kg bags', 'Store in cool, dry place. Use within 3 months of opening.', NULL, 2],

    ['feeds', 'sunflower', 'Sunflower', './images/sun.jpeg',
     'Excellent protein and energy source for livestock.',
     'Rich in protein (28-32%)|Good energy source|Improves coat condition|Cost-effective feed ingredient|Highly palatable',
     'Can be included up to 20% in poultry and livestock rations.',
     '50kg bags', 'Store in dry conditions to prevent mold growth.', 'UGX 70,000 per 50kg bag', 3],

    ['feeds', 'pig', 'Pig Feed', './images/pig.jpeg',
     'Complete nutrition for all stages of pig production.',
     'Promotes rapid weight gain|Balanced amino acid profile|Improves meat quality|Supports reproductive health|Contains essential minerals',
     'Available in starter, grower, and finisher formulations. Feed according to pig age and weight.',
     '50kg bags', 'Store in clean, dry area. Protect from rodents.', 'UGX 98,000 per 50kg bag', 4],

    ['feeds', 'cattle', 'Dairy & Beef Cattle Feed', './images/catle.jpeg',
     'High-quality feed for milk production and beef growth.',
     'Increases milk yield|Improves milk quality|Supports weight gain in beef cattle|Rich in energy and protein|Contains minerals for health',
     'Dairy: 3-5kg per cow per day. Beef: 2-4kg per animal per day. Supplement with roughage.',
     '70kg bags', 'Keep in well-ventilated storage. Use within 8 weeks.', 'UGX 130,000 per 70kg bag', 5],

    ['feeds', 'goat', 'Goat Feed', './images/goat-feed-performance-40kg.jpg',
     'Specially formulated for goats at all production stages.',
     'Supports rapid growth|Improves milk production|Enhances reproductive performance|Balanced nutrition|Boosts immunity',
     'Feed 300-500g per goat per day depending on size and production level. Provide browse/hay.',
     '40kg bags', 'Store in dry, cool place away from direct sunlight.', 'UGX 80,000 per 40kg bag', 6],

    ['kenbro-chicks', 'kenbro-day-old', 'Kenbro Day-Old Chicks', './images/images.jpeg',
     'Hardy dual-purpose Kenbro chicks ideal for both meat and egg farming.',
     'Dual-purpose breed for meat and eggs|Strong early growth and survivability|Good feed conversion under local conditions|Adaptable to free-range and semi-intensive systems|Vaccination support available',
     'Brooder temperature: 32-35C for first week, then reduce gradually by 2-3C weekly. Start with chick mash and clean water from day one.',
     'Minimum order: 50 chicks', 'N/A - Live chicks delivered fresh', 'UGX 3,200 per chick', 1],

    ['kenbro-chicks', 'kenbro-point-of-lay', 'Kenbro Point-of-Lay Pullets', './images/hen-laying-eggs.jpg',
     'Well-raised Kenbro pullets near laying age for quick farm startup.',
     'Shorter time to egg production|Uniform flock development|Hardy and adaptable birds|Lower brooding risk for new farmers|Suitable for semi-intensive systems',
     'Introduce gradually to layer feed and maintain 14-16 hours of light daily for stable laying performance.',
     'Per bird / batch orders', 'N/A - Live birds delivered healthy', 'UGX 18,000 per bird', 2],

    ['pellets', 'broiler-pellet', 'Broiler Pellet Feed', './images/broiler.jpeg',
     'Compressed high-energy pellets for broilers from grower to finisher stage.',
     'Less feed wastage than mash|Uniform nutrient intake|Supports fast weight gain|Improves feed conversion|Easy handling and storage',
     'Feed according to age schedule: Grower (3-5 weeks) and Finisher (5+ weeks). Always provide clean drinking water.',
     '50kg bags', 'Store on raised pallets in a cool, dry store and keep bags sealed.', 'UGX 115,000 per 50kg bag', 1],

    ['pellets', 'layer-pellet', 'Layer Pellet Feed', './images/layer.jpeg',
     'Nutrient-balanced pellet feed designed for consistent egg production.',
     'High calcium for shell strength|Steady laying performance|Reduced selective feeding|Balanced vitamins and minerals|Improved flock uniformity',
     'Feed from point of lay onward at 110-130g per bird per day with clean water available at all times.',
     '50kg bags', 'Keep away from moisture and direct sunlight.', 'UGX 120,000 per 50kg bag', 2],

    ['feed-additives', 'vitamin-premix', 'Vitamin & Mineral Premix', './images/soya.jpeg',
     'Concentrated premix used to fortify homemade or commercial rations.',
     'Supports immunity and stress resistance|Improves growth and egg quality|Helps prevent micronutrient deficiencies|Easy to blend in feed|Suitable for poultry and livestock',
     'Mix as directed by your nutritionist; typical inclusion is 2.5-5kg per tonne of finished feed depending on the ration target.',
     '5kg, 10kg bags', 'Seal tightly after opening and store in a cool, dry place.', 'UGX 75,000 per 5kg bag', 1],

    ['feed-additives', 'toxin-binder', 'Mycotoxin Binder', './images/sun.jpeg',
     'Additive that helps reduce the impact of feed toxins and mold contamination.',
     'Protects gut and liver health|Improves feed utilization|Reduces toxin-related production losses|Supports flock uniformity|Suitable for all production stages',
     'Add 0.5-1kg per tonne of feed or use as advised by the technical team.',
     '1kg, 5kg packs', 'Keep container closed and dry. Avoid direct humidity exposure.', 'UGX 48,000 per 1kg pack', 2],

    ['feed-concentrates', 'broiler-concentrate', 'Broiler Feed Concentrate', './images/soya b.jpeg',
     'High-protein concentrate for formulating quality broiler feed using local grains.',
     'Cuts total feed cost when mixed correctly|Provides balanced amino acids and minerals|Supports rapid growth and muscle build|Consistent results across batches|Ideal for on-farm mixing',
     'Mix with maize bran and energy sources according to recommended ratio (for example 25% concentrate and 75% base ingredients).',
     '25kg bags', 'Store in original bags on raised pallets in a dry, ventilated store.', 'UGX 135,000 per 25kg bag', 1],

    ['feed-concentrates', 'layer-concentrate', 'Layer Feed Concentrate', './images/lime.jpeg',
     'Concentrate formulated for layers to support egg production and shell quality.',
     'High calcium and phosphorus balance|Supports stable egg output|Improves shell thickness|Optimized vitamin package for laying birds|Works well with local feed ingredients',
     'Blend with maize or bran as recommended (typically 20-30% concentrate based on your target ration).',
     '25kg bags', 'Protect from moisture and pests. Close bag after each use.', 'UGX 128,000 per 25kg bag', 2],

    ['chicks', 'chicks', 'Broiler Day-Old Chicks', './images/images.jpeg',
     'Fast-growing broiler chicks, vaccinated against Marek\'s disease.',
     'Vaccinated against Marek\'s disease|High survival rate (95%+)|Fast-growing breeds|Good laying potential (layers)|Expert breeding selection',
     'Brooder temperature: 32-35°C for first week. Provide chick starter feed and clean water immediately.',
     'Minimum order: 50 chicks', 'N/A - Live chicks delivered fresh', 'UGX 2,500 per chick', 1],

    ['chicks', 'chicks-layer', 'Layer Day-Old Chicks', './images/images.jpeg',
     'Quality layer chicks with excellent laying potential.',
     'Vaccinated against Marek\'s disease|High survival rate (95%+)|Excellent laying potential|Strong and healthy breeds|Expert breeding selection',
     'Brooder temperature: 32-35°C for first week. Provide chick starter feed and clean water immediately.',
     'Minimum order: 50 chicks', 'N/A - Live chicks delivered fresh', 'UGX 3,000 per chick', 2],

    ['consultancy', 'farm-setup', 'Farm Setup & Planning', './images/farm-supervisor.jpg',
     'Personalized consultation for setting up your poultry or livestock farm.',
     'Site selection guidance|Building and infrastructure planning|Equipment recommendations|Cost estimation|Business plan development',
     'Contact us to schedule a consultation. We will visit your site and provide customized recommendations.',
     'Per consultation', 'N/A', 'Contact for quote', 1],

    ['consultancy', 'nutrition', 'Nutrition & Feed Planning', './images/layer.jpeg',
     'Expert guidance on optimal feed formulation for your livestock.',
     'Custom feed formulation|Cost optimization|Nutritional analysis|Growth monitoring|Diet adjustment recommendations',
     'Our experts will analyze your current feeding program and provide recommendations for improvement.',
     'Per consultation', 'N/A', 'Contact for quote', 2],

    ['consultancy', 'health', 'Animal Health Management', './images/broiler.jpeg',
     'Disease prevention, vaccination schedules, and health protocols.',
     'Vaccination schedule planning|Disease prevention strategies|Health monitoring protocols|Emergency response guidance|Biosecurity measures',
     'We provide comprehensive health management plans tailored to your farm size and type.',
     'Per consultation', 'N/A', 'Contact for quote', 3],

    ['consultancy', 'training', 'Farmer Training Programs', './images/farm-training.jpg',
     'Monthly workshops on modern farming techniques and best practices.',
     'Hands-on practical training|Modern farming techniques|Record keeping and management|Marketing strategies|Networking opportunities',
     'Join our monthly training sessions. Contact us for the schedule and registration.',
     'Per session', 'N/A', 'Free for customers', 4]
];

foreach ($products_data as $prod) {
    $cat_slug = mysqli_real_escape_string($conn, $prod[0]);

    // Get category ID
    $cat_result = mysqli_query($conn, "SELECT id FROM categories WHERE slug = '$cat_slug'");
    if (!$cat_result || mysqli_num_rows($cat_result) == 0) {
        $errors[] = "Category '$cat_slug' not found for product '{$prod[2]}'";
        continue;
    }
    $cat_row = mysqli_fetch_assoc($cat_result);
    $category_id = $cat_row['id'];

    $slug = mysqli_real_escape_string($conn, $prod[1]);
    $name = mysqli_real_escape_string($conn, $prod[2]);
    $image = mysqli_real_escape_string($conn, $prod[3]);
    $description = mysqli_real_escape_string($conn, $prod[4]);
    $benefits = mysqli_real_escape_string($conn, $prod[5]);
    $usage_info = mysqli_real_escape_string($conn, $prod[6]);
    $packaging = mysqli_real_escape_string($conn, $prod[7]);
    $storage = mysqli_real_escape_string($conn, $prod[8]);
    $sort_order = (int)$prod[10];

    $sql = "INSERT INTO products (category_id, slug, name, image, description, benefits, usage_info, packaging, storage, sort_order)
            VALUES ($category_id, '$slug', '$name', '$image', '$description', '$benefits', '$usage_info', '$packaging', '$storage', $sort_order)
            ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), benefits = VALUES(benefits),
            usage_info = VALUES(usage_info), packaging = VALUES(packaging), storage = VALUES(storage)";

    if (mysqli_query($conn, $sql)) {
        $messages[] = "Product '$name' added/updated.";
    } else {
        $errors[] = "Error adding product '$name': " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container py-5">
        <h1><i class="fas fa-database"></i> Database Setup Complete</h1>
        <hr>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle"></i> Errors:</h5>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($messages)): ?>
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> Success Messages:</h5>
                <ul>
                    <?php foreach ($messages as $msg): ?>
                        <li><?php echo htmlspecialchars($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="product.php" class="btn btn-success"><i class="fas fa-eye"></i> View Products</a>
            <a href="product-category.php?category=feeds" class="btn btn-primary"><i class="fas fa-list"></i> View Feeds Category</a>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Go Home</a>
        </div>

        <div class="alert alert-warning mt-4">
            <i class="fas fa-info-circle"></i> <strong>Note:</strong> You can delete this file after running it once.
            The data is now stored in your database.
        </div>
    </div>
</body>
</html>
