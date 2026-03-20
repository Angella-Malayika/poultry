-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS Poultry;

-- Use the database
USE Poultry;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    product VARCHAR(50) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    delivery_address TEXT NOT NULL,
    delivery_date DATE NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'pending'
);

-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    subject VARCHAR(100) NOT NULL,
    message_text TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME DEFAULT NULL
);

-- Photos table is created dynamically by upload_photo.php via ensure_photo_schema()

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-box',
    sort_order INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
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
);

-- Insert default categories
INSERT INTO categories (slug, title, description, icon, sort_order) VALUES
('broilers', 'Broiler Products', 'Complete solutions for broiler chicken farming', 'fas fa-egg', 1),
('layers', 'Layer Products', 'Everything you need for egg production', 'fas fa-dna', 2),
('feeds', 'All Feed Products', 'Complete range of nutritional feeds for all livestock', 'fas fa-shopping-bag', 3),
('kenbro-chicks', 'Kenbro Chicks', 'Dual-purpose Kenbro chicks suitable for meat and egg production', 'fas fa-feather-pointed', 4),
('pellets', 'Pellet Feeds', 'Balanced pellet feeds for efficient feeding and reduced wastage', 'fas fa-cubes', 5),
('feed-additives', 'Feed Additives', 'Performance enhancers and health-support additives for better feed results', 'fas fa-flask', 6),
('feed-concentrates', 'Feed Concentrates', 'Nutrient-dense concentrates for custom feed formulation', 'fas fa-vial', 7),
('chicks', 'Day-Old Chicks', 'Healthy, vaccinated chicks for poultry farming', 'fas fa-heart', 8),
('consultancy', 'Expert Consultancy Services', 'Professional agricultural guidance for successful farming', 'fas fa-users', 9)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Remove existing seeded products so this script can be re-run safely.
DELETE FROM products WHERE slug IN (
    'broiler', 'chicks-broiler', 'layer', 'lime', 'grower', 'soya', 'sunflower',
    'pig', 'cattle', 'goat', 'kenbro-day-old', 'kenbro-point-of-lay',
    'broiler-pellet', 'layer-pellet', 'vitamin-premix', 'toxin-binder',
    'broiler-concentrate', 'layer-concentrate', 'chicks', 'chicks-layer',
    'farm-setup', 'nutrition', 'health', 'training'
);
use Poultry;

-- Insert default products with full details
INSERT INTO products (category_id, slug, name, image, description, benefits, usage_info, packaging, storage, sort_order) VALUES
((SELECT id FROM categories WHERE slug = 'broilers'), 'broiler', 'Broiler Feed', './images/broiler.jpeg',
'High-energy feed designed for rapid weight gain in meat-type chickens.',
'Accelerated growth rate|High energy concentration|Excellent feed conversion|Improved meat quality|Contains growth promoters',
'Starter (0-3 weeks), Grower (3-6 weeks), Finisher (6 weeks+). Feed ad-libitum with clean water.',
'50kg bags', 'Keep in cool, dry conditions. Use fresh feed for best results.', 1),

((SELECT id FROM categories WHERE slug = 'broilers'), 'chicks-broiler', 'Broiler Day-Old Chicks', './images/images.jpeg',
'Healthy, vaccinated broiler chicks ready for farming.',
'Vaccinated against Marek''s disease|High survival rate (95%+)|Fast-growing breeds|Good laying potential (layers)|Expert breeding selection',
'Brooder temperature: 32-35°C for first week. Provide chick starter feed and clean water immediately.',
'Minimum order: 50 chicks', 'N/A - Live chicks delivered fresh', 2),

((SELECT id FROM categories WHERE slug = 'layers'), 'layer', 'Layer Mash', './images/layer.jpeg',
'Premium quality feed specially formulated for laying hens to maximize egg production.',
'High calcium for strong eggshells|Optimal protein for consistent laying|Enhanced egg quality and size|Vitamins for bird health|Improves feed efficiency',
'Feed from point of lay onwards. Consumption: 110-130g per bird per day. Ensure constant water supply.',
'50kg bags', 'Store in ventilated area away from moisture. Best used within 6 weeks.', 1),

((SELECT id FROM categories WHERE slug = 'layers'), 'lime', 'Lime (Calcium Supplement)', './images/lime.jpeg',
'Essential calcium for strong eggshells and bone development.',
'Prevents calcium deficiency|Strengthens eggshells|Supports bone development|Improves digestive health|Neutralizes soil acidity',
'Add to layer feeds or provide as free choice. Also used for soil treatment.',
'25kg, 50kg bags', 'Keep dry. Long shelf life when properly stored.', 2),

((SELECT id FROM categories WHERE slug = 'feeds'), 'grower', 'Grower Mash', './images/soya.jpeg',
'Complete feed for growing chickens from 8 weeks to point of lay.',
'Balanced nutrients for optimal growth|Supports skeletal development|Improves immunity|Prepares birds for laying|Contains vitamins and minerals',
'Feed from 8-18 weeks. Provide fresh water at all times. Expected consumption: 60-80g per bird per day.',
'50kg bags', 'Keep dry and protected from pests. Use within 2 months.', 1),

((SELECT id FROM categories WHERE slug = 'feeds'), 'soya', 'Soya', './images/soya b.jpeg',
'High-quality soya bean meal for protein supplementation.',
'High protein content (44-48%)|Essential amino acids for growth|Improves feed conversion ratio|Enhances egg production in layers|Supports muscle development',
'Mix with other feed ingredients as a protein supplement. Recommended inclusion: 15-25% in poultry diets.',
'25kg, 50kg bags', 'Store in cool, dry place. Use within 3 months of opening.', 2),

((SELECT id FROM categories WHERE slug = 'feeds'), 'sunflower', 'Sunflower', './images/sun.jpeg',
'Excellent protein and energy source for livestock.',
'Rich in protein (28-32%)|Good energy source|Improves coat condition|Cost-effective feed ingredient|Highly palatable',
'Can be included up to 20% in poultry and livestock rations.',
'50kg bags', 'Store in dry conditions to prevent mold growth.', 3),

((SELECT id FROM categories WHERE slug = 'feeds'), 'pig', 'Pig Feed', './images/pig.jpeg',
'Complete nutrition for all stages of pig production.',
'Promotes rapid weight gain|Balanced amino acid profile|Improves meat quality|Supports reproductive health|Contains essential minerals',
'Available in starter, grower, and finisher formulations. Feed according to pig age and weight.',
'50kg bags', 'Store in clean, dry area. Protect from rodents.', 4),

((SELECT id FROM categories WHERE slug = 'feeds'), 'cattle', 'Dairy & Beef Cattle Feed', './images/catle.jpeg',
'High-quality feed for milk production and beef growth.',
'Increases milk yield|Improves milk quality|Supports weight gain in beef cattle|Rich in energy and protein|Contains minerals for health',
'Dairy: 3-5kg per cow per day. Beef: 2-4kg per animal per day. Supplement with roughage.',
'70kg bags', 'Keep in well-ventilated storage. Use within 8 weeks.', 5),

((SELECT id FROM categories WHERE slug = 'feeds'), 'goat', 'Goat Feed', './images/goat-feed-performance-40kg.jpg',
'Specially formulated for goats at all production stages.',
'Supports rapid growth|Improves milk production|Enhances reproductive performance|Balanced nutrition|Boosts immunity',
'Feed 300-500g per goat per day depending on size and production level. Provide browse/hay.',
'40kg bags', 'Store in dry, cool place away from direct sunlight.', 6),

((SELECT id FROM categories WHERE slug = 'kenbro-chicks'), 'kenbro-day-old', 'Kenbro Day-Old Chicks', './images/kenbro chick.jpeg',
'Hardy dual-purpose Kenbro chicks ideal for both meat and egg farming.',
'Dual-purpose breed for meat and eggs|Strong early growth and survivability|Good feed conversion under local conditions|Adaptable to free-range and semi-intensive systems|Vaccination support available',
'Brooder temperature: 32-35C for first week, then reduce gradually by 2-3C weekly. Start with chick mash and clean water from day one.',
'Minimum order: 50 chicks', 'N/A - Live chicks delivered fresh', 1),

((SELECT id FROM categories WHERE slug = 'kenbro-chicks'), 'kenbro-point-of-lay', 'Kenbro Point-of-Lay Pullets', './images/kenbro.jpeg',
'Well-raised Kenbro pullets near laying age for quick farm startup.',
'Shorter time to egg production|Uniform flock development|Hardy and adaptable birds|Lower brooding risk for new farmers|Suitable for semi-intensive systems',
'Introduce gradually to layer feed and maintain 14-16 hours of light daily for stable laying performance.',
'Per bird / batch orders', 'N/A - Live birds delivered healthy', 2),

((SELECT id FROM categories WHERE slug = 'pellets'), 'broiler-pellet', 'Broiler Pellet Feed', './images/pellets.jpeg',
'Compressed high-energy pellets for broilers from grower to finisher stage.',
'Less feed wastage than mash|Uniform nutrient intake|Supports fast weight gain|Improves feed conversion|Easy handling and storage',
'Feed according to age schedule: Grower (3-5 weeks) and Finisher (5+ weeks). Always provide clean drinking water.',
'50kg bags', 'Store on raised pallets in a cool, dry store and keep bags sealed.', 1),

((SELECT id FROM categories WHERE slug = 'pellets'), 'layer-pellet', 'Layer Pellet Feed', './images/pellet.jpeg',
'Nutrient-balanced pellet feed designed for consistent egg production.',
'High calcium for shell strength|Steady laying performance|Reduced selective feeding|Balanced vitamins and minerals|Improved flock uniformity',
'Feed from point of lay onward at 110-130g per bird per day with clean water available at all times.',
'50kg bags', 'Keep away from moisture and direct sunlight.', 2),

((SELECT id FROM categories WHERE slug = 'feed-additives'), 'vitamin-premix', 'Vitamin & Mineral Premix', './images/FB_additives.jpg',
'Concentrated premix used to fortify homemade or commercial rations.',
'Supports immunity and stress resistance|Improves growth and egg quality|Helps prevent micronutrient deficiencies|Easy to blend in feed|Suitable for poultry and livestock',
'Mix as directed by your nutritionist; typical inclusion is 2.5-5kg per tonne of finished feed depending on the ration target.',
'5kg, 10kg bags', 'Seal tightly after opening and store in a cool, dry place.', 1),

((SELECT id FROM categories WHERE slug = 'feed-additives'), 'toxin-binder', 'Mycotoxin Binder', './images/aditives.jpeg',
'Additive that helps reduce the impact of feed toxins and mold contamination.',
'Protects gut and liver health|Improves feed utilization|Reduces toxin-related production losses|Supports flock uniformity|Suitable for all production stages',
'Add 0.5-1kg per tonne of feed or use as advised by the technical team.',
'1kg, 5kg packs', 'Keep container closed and dry. Avoid direct humidity exposure.', 2),

((SELECT id FROM categories WHERE slug = 'feed-concentrates'), 'broiler-concentrate', 'Broiler Feed Concentrate', './images/concetrates.jpeg',
'High-protein concentrate for formulating quality broiler feed using local grains.',
'Cuts total feed cost when mixed correctly|Provides balanced amino acids and minerals|Supports rapid growth and muscle build|Consistent results across batches|Ideal for on-farm mixing',
'Mix with maize bran and energy sources according to recommended ratio (for example 25% concentrate and 75% base ingredients).',
'25kg bags', 'Store in original bags on raised pallets in a dry, ventilated store.', 1),

((SELECT id FROM categories WHERE slug = 'feed-concentrates'), 'layer-concentrate', 'Layer Feed Concentrate', './images/concentrate.jpeg',
'Concentrate formulated for layers to support egg production and shell quality.',
'High calcium and phosphorus balance|Supports stable egg output|Improves shell thickness|Optimized vitamin package for laying birds|Works well with local feed ingredients',
'Blend with maize or bran as recommended (typically 20-30% concentrate based on your target ration).',
'25kg bags', 'Protect from moisture and pests. Close bag after each use.', 2),

((SELECT id FROM categories WHERE slug = 'chicks'), 'chicks', 'Broiler Day-Old Chicks', './images/images.jpeg',
'Fast-growing broiler chicks, vaccinated against Marek''s disease.',
'Vaccinated against Marek''s disease|High survival rate (95%+)|Fast-growing breeds|Good laying potential (layers)|Expert breeding selection',
'Brooder temperature: 32-35°C for first week. Provide chick starter feed and clean water immediately.',
'Minimum order: 50 chicks', 'N/A - Live chicks delivered fresh', 1),

((SELECT id FROM categories WHERE slug = 'chicks'), 'chicks-layer', 'Layer Day-Old Chicks', './images/images.jpeg',
'Quality layer chicks with excellent laying potential.',
'Vaccinated against Marek''s disease|High survival rate (95%+)|Excellent laying potential|Strong and healthy breeds|Expert breeding selection',
'Brooder temperature: 32-35°C for first week. Provide chick starter feed and clean water immediately.',
'Minimum order: 50 chicks', 'N/A - Live chicks delivered fresh', 2),

((SELECT id FROM categories WHERE slug = 'consultancy'), 'farm-setup', 'Farm Setup & Planning', './images/farm-supervisor.jpg',
'Personalized consultation for setting up your poultry or livestock farm.',
'Site selection guidance|Building and infrastructure planning|Equipment recommendations|Cost estimation|Business plan development',
'Contact us to schedule a consultation. We will visit your site and provide customized recommendations.',
'Per consultation', 'N/A', 1),

((SELECT id FROM categories WHERE slug = 'consultancy'), 'nutrition', 'Nutrition & Feed Planning', './images/layer.jpeg',
'Expert guidance on optimal feed formulation for your livestock.',
'Custom feed formulation|Cost optimization|Nutritional analysis|Growth monitoring|Diet adjustment recommendations',
'Our experts will analyze your current feeding program and provide recommendations for improvement.',
'Per consultation', 'N/A', 2),

((SELECT id FROM categories WHERE slug = 'consultancy'), 'health', 'Animal Health Management', './images/broiler.jpeg',
'Disease prevention, vaccination schedules, and health protocols.',
'Vaccination schedule planning|Disease prevention strategies|Health monitoring protocols|Emergency response guidance|Biosecurity measures',
'We provide comprehensive health management plans tailored to your farm size and type.',
'Per consultation', 'N/A', 3),

((SELECT id FROM categories WHERE slug = 'consultancy'), 'training', 'Farmer Training Programs', './images/farm-training.jpg',
'Monthly workshops on modern farming techniques and best practices.',
'Hands-on practical training|Modern farming techniques|Record keeping and management|Marketing strategies|Networking opportunities',
'Join our monthly training sessions. Contact us for the schedule and registration.',
'Per session', 'N/A', 4)
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    name = VALUES(name),
    image = VALUES(image),
    description = VALUES(description),
    benefits = VALUES(benefits),
    usage_info = VALUES(usage_info),
    packaging = VALUES(packaging),
    storage = VALUES(storage),
    sort_order = VALUES(sort_order);

-- ============================================================
-- Insert default admin user
-- Email: admin@kalungufeeds.com | Password: Admin@123
-- The password hash below was generated with password_hash('Admin@123', PASSWORD_DEFAULT)
-- To create your own admin, register a normal account via signup.php,
-- then run:  UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
-- ============================================================
INSERT INTO users (username, email, password, role) VALUES
('Admin', 'admin@kalungufeeds.com', '$2y$12$8/qAxFOSdd0mFKCZb4wU.Oqbi7qdXP/yyO13dEabibd4QMvLhzzY2', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';
