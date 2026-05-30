-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: poultry
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT 'fas fa-box',
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'broilers','Broiler Products','Complete solutions for broiler chicken farming','fas fa-egg',1,1,'2026-05-21 12:45:02'),(2,'layers','Layer Products','Everything you need for egg production','fas fa-dna',2,1,'2026-05-21 12:45:02'),(3,'feeds','All Feed Products','Complete range of nutritional feeds for all livestock','fas fa-shopping-bag',3,1,'2026-05-21 12:45:02'),(4,'kenbro-chicks','Kenbro Chicks','Dual-purpose Kenbro chicks suitable for meat and egg production','fas fa-feather-pointed',4,1,'2026-05-21 12:45:02'),(5,'pellets','Pellet Feeds','Balanced pellet feeds for efficient feeding and reduced wastage','fas fa-cubes',5,1,'2026-05-21 12:45:02'),(6,'feed-additives','Feed Additives','Performance enhancers and health-support additives for better feed results','fas fa-flask',6,1,'2026-05-21 12:45:02'),(7,'feed-concentrates','Feed Concentrates','Nutrient-dense concentrates for custom feed formulation','fas fa-vial',7,1,'2026-05-21 12:45:02'),(8,'chicks','Day-Old Chicks','Healthy, vaccinated chicks for poultry farming','fas fa-heart',8,1,'2026-05-21 12:45:02'),(9,'consultancy','Expert Consultancy Services','Professional agricultural guidance for successful farming','fas fa-users',9,1,'2026-05-21 12:45:02');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,'nakanwagi angella','josbert@gmail.com','4995489634','support','hello','2026-05-25 16:26:17',1,'2026-05-25 19:28:18');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `product` varchar(50) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `delivery_address` text NOT NULL,
  `delivery_date` date NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,'Nakanwagi Angella','4995489634','Soya x 1 Kg',1.00,'249irofjkfg','2026-06-02','2026-05-25 16:12:48','delivered'),(2,3,'Nakanwagi Angella','0799877769','Layer Mash x 1 Kg',1.00,'jj','2026-05-28','2026-05-26 18:22:34','pending');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `image_data` longblob,
  `image_mime` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `product_section` varchar(40) NOT NULL DEFAULT 'broilers',
  `product_slug` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photos`
--

LOCK TABLES `photos` WRITE;
/*!40000 ALTER TABLE `photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `benefits` text,
  `usage_info` text,
  `packaging` varchar(100) DEFAULT NULL,
  `storage` text,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'broiler','Broiler Feed','./images/broiler.jpeg','High-energy feed designed for rapid weight gain in meat-type chickens.','Accelerated growth rate|High energy concentration|Excellent feed conversion|Improved meat quality|Contains growth promoters','Starter (0-3 weeks), Grower (3-6 weeks), Finisher (6 weeks+). Feed ad-libitum with clean water.','50kg bags','Keep in cool, dry conditions. Use fresh feed for best results.',1,1,'2026-05-21 12:45:33'),(2,1,'chicks-broiler','Broiler Day-Old Chicks','./images/images.jpeg','Healthy, vaccinated broiler chicks ready for farming.','Vaccinated against Marek\'s disease|High survival rate (95%+)|Fast-growing breeds|Good laying potential (layers)|Expert breeding selection','Brooder temperature: 32-35┬░C for first week. Provide chick starter feed and clean water immediately.','Minimum order: 50 chicks','N/A - Live chicks delivered fresh',2,1,'2026-05-21 12:45:33'),(3,2,'layer','Layer Mash','./images/layer.jpeg','Premium quality feed specially formulated for laying hens to maximize egg production.','High calcium for strong eggshells|Optimal protein for consistent laying|Enhanced egg quality and size|Vitamins for bird health|Improves feed efficiency','Feed from point of lay onwards. Consumption: 110-130g per bird per day. Ensure constant water supply.','50kg bags','Store in ventilated area away from moisture. Best used within 6 weeks.',1,1,'2026-05-21 12:45:33'),(4,2,'lime','Lime (Calcium Supplement)','./images/lime.jpeg','Essential calcium for strong eggshells and bone development.','Prevents calcium deficiency|Strengthens eggshells|Supports bone development|Improves digestive health|Neutralizes soil acidity','Add to layer feeds or provide as free choice. Also used for soil treatment.','25kg, 50kg bags','Keep dry. Long shelf life when properly stored.',2,1,'2026-05-21 12:45:33'),(5,3,'grower','Grower Mash','./images/soya.jpeg','Complete feed for growing chickens from 8 weeks to point of lay.','Balanced nutrients for optimal growth|Supports skeletal development|Improves immunity|Prepares birds for laying|Contains vitamins and minerals','Feed from 8-18 weeks. Provide fresh water at all times. Expected consumption: 60-80g per bird per day.','50kg bags','Keep dry and protected from pests. Use within 2 months.',1,1,'2026-05-21 12:45:33'),(6,3,'soya','Soya','./images/soya b.jpeg','High-quality soya bean meal for protein supplementation.','High protein content (44-48%)|Essential amino acids for growth|Improves feed conversion ratio|Enhances egg production in layers|Supports muscle development','Mix with other feed ingredients as a protein supplement. Recommended inclusion: 15-25% in poultry diets.','25kg, 50kg bags','Store in cool, dry place. Use within 3 months of opening.',2,1,'2026-05-21 12:45:33'),(7,3,'sunflower','Sunflower','./images/sun.jpeg','Excellent protein and energy source for livestock.','Rich in protein (28-32%)|Good energy source|Improves coat condition|Cost-effective feed ingredient|Highly palatable','Can be included up to 20% in poultry and livestock rations.','50kg bags','Store in dry conditions to prevent mold growth.',3,1,'2026-05-21 12:45:33'),(8,3,'pig','Pig Feed','./images/pig.jpeg','Complete nutrition for all stages of pig production.','Promotes rapid weight gain|Balanced amino acid profile|Improves meat quality|Supports reproductive health|Contains essential minerals','Available in starter, grower, and finisher formulations. Feed according to pig age and weight.','50kg bags','Store in clean, dry area. Protect from rodents.',4,1,'2026-05-21 12:45:33'),(9,3,'cattle','Dairy & Beef Cattle Feed','./images/catle.jpeg','High-quality feed for milk production and beef growth.','Increases milk yield|Improves milk quality|Supports weight gain in beef cattle|Rich in energy and protein|Contains minerals for health','Dairy: 3-5kg per cow per day. Beef: 2-4kg per animal per day. Supplement with roughage.','70kg bags','Keep in well-ventilated storage. Use within 8 weeks.',5,1,'2026-05-21 12:45:33'),(10,3,'goat','Goat Feed','./images/goat-feed-performance-40kg.jpg','Specially formulated for goats at all production stages.','Supports rapid growth|Improves milk production|Enhances reproductive performance|Balanced nutrition|Boosts immunity','Feed 300-500g per goat per day depending on size and production level. Provide browse/hay.','40kg bags','Store in dry, cool place away from direct sunlight.',6,1,'2026-05-21 12:45:33'),(11,4,'kenbro-day-old','Kenbro Day-Old Chicks','./images/kenbro chick.jpeg','Hardy dual-purpose Kenbro chicks ideal for both meat and egg farming.','Dual-purpose breed for meat and eggs|Strong early growth and survivability|Good feed conversion under local conditions|Adaptable to free-range and semi-intensive systems|Vaccination support available','Brooder temperature: 32-35C for first week, then reduce gradually by 2-3C weekly. Start with chick mash and clean water from day one.','Minimum order: 50 chicks','N/A - Live chicks delivered fresh',1,1,'2026-05-21 12:45:33'),(12,4,'kenbro-point-of-lay','Kenbro Point-of-Lay Pullets','./images/kenbro.jpeg','Well-raised Kenbro pullets near laying age for quick farm startup.','Shorter time to egg production|Uniform flock development|Hardy and adaptable birds|Lower brooding risk for new farmers|Suitable for semi-intensive systems','Introduce gradually to layer feed and maintain 14-16 hours of light daily for stable laying performance.','Per bird / batch orders','N/A - Live birds delivered healthy',2,1,'2026-05-21 12:45:33'),(13,5,'broiler-pellet','Broiler Pellet Feed','./images/pellets.jpeg','Compressed high-energy pellets for broilers from grower to finisher stage.','Less feed wastage than mash|Uniform nutrient intake|Supports fast weight gain|Improves feed conversion|Easy handling and storage','Feed according to age schedule: Grower (3-5 weeks) and Finisher (5+ weeks). Always provide clean drinking water.','50kg bags','Store on raised pallets in a cool, dry store and keep bags sealed.',1,1,'2026-05-21 12:45:33'),(14,5,'layer-pellet','Layer Pellet Feed','./images/pellet.jpeg','Nutrient-balanced pellet feed designed for consistent egg production.','High calcium for shell strength|Steady laying performance|Reduced selective feeding|Balanced vitamins and minerals|Improved flock uniformity','Feed from point of lay onward at 110-130g per bird per day with clean water available at all times.','50kg bags','Keep away from moisture and direct sunlight.',2,1,'2026-05-21 12:45:33'),(15,6,'vitamin-premix','Vitamin & Mineral Premix','./images/FB_additives.jpg','Concentrated premix used to fortify homemade or commercial rations.','Supports immunity and stress resistance|Improves growth and egg quality|Helps prevent micronutrient deficiencies|Easy to blend in feed|Suitable for poultry and livestock','Mix as directed by your nutritionist; typical inclusion is 2.5-5kg per tonne of finished feed depending on the ration target.','5kg, 10kg bags','Seal tightly after opening and store in a cool, dry place.',1,1,'2026-05-21 12:45:33'),(16,6,'toxin-binder','Mycotoxin Binder','./images/aditives.jpeg','Additive that helps reduce the impact of feed toxins and mold contamination.','Protects gut and liver health|Improves feed utilization|Reduces toxin-related production losses|Supports flock uniformity|Suitable for all production stages','Add 0.5-1kg per tonne of feed or use as advised by the technical team.','1kg, 5kg packs','Keep container closed and dry. Avoid direct humidity exposure.',2,1,'2026-05-21 12:45:33'),(17,7,'broiler-concentrate','Broiler Feed Concentrate','./images/concetrates.jpeg','High-protein concentrate for formulating quality broiler feed using local grains.','Cuts total feed cost when mixed correctly|Provides balanced amino acids and minerals|Supports rapid growth and muscle build|Consistent results across batches|Ideal for on-farm mixing','Mix with maize bran and energy sources according to recommended ratio (for example 25% concentrate and 75% base ingredients).','25kg bags','Store in original bags on raised pallets in a dry, ventilated store.',1,1,'2026-05-21 12:45:33'),(18,7,'layer-concentrate','Layer Feed Concentrate','./images/concentrate.jpeg','Concentrate formulated for layers to support egg production and shell quality.','High calcium and phosphorus balance|Supports stable egg output|Improves shell thickness|Optimized vitamin package for laying birds|Works well with local feed ingredients','Blend with maize or bran as recommended (typically 20-30% concentrate based on your target ration).','25kg bags','Protect from moisture and pests. Close bag after each use.',2,1,'2026-05-21 12:45:33'),(19,8,'chicks','Broiler Day-Old Chicks','./images/images.jpeg','Fast-growing broiler chicks, vaccinated against Marek\'s disease.','Vaccinated against Marek\'s disease|High survival rate (95%+)|Fast-growing breeds|Good laying potential (layers)|Expert breeding selection','Brooder temperature: 32-35┬░C for first week. Provide chick starter feed and clean water immediately.','Minimum order: 50 chicks','N/A - Live chicks delivered fresh',1,1,'2026-05-21 12:45:33'),(20,8,'chicks-layer','Layer Day-Old Chicks','./images/images.jpeg','Quality layer chicks with excellent laying potential.','Vaccinated against Marek\'s disease|High survival rate (95%+)|Excellent laying potential|Strong and healthy breeds|Expert breeding selection','Brooder temperature: 32-35┬░C for first week. Provide chick starter feed and clean water immediately.','Minimum order: 50 chicks','N/A - Live chicks delivered fresh',2,1,'2026-05-21 12:45:33'),(21,9,'farm-setup','Farm Setup & Planning','./images/farm-supervisor.jpg','Personalized consultation for setting up your poultry or livestock farm.','Site selection guidance|Building and infrastructure planning|Equipment recommendations|Cost estimation|Business plan development','Contact us to schedule a consultation. We will visit your site and provide customized recommendations.','Per consultation','N/A',1,1,'2026-05-21 12:45:33'),(22,9,'nutrition','Nutrition & Feed Planning','./images/layer.jpeg','Expert guidance on optimal feed formulation for your livestock.','Custom feed formulation|Cost optimization|Nutritional analysis|Growth monitoring|Diet adjustment recommendations','Our experts will analyze your current feeding program and provide recommendations for improvement.','Per consultation','N/A',2,1,'2026-05-21 12:45:33'),(23,9,'health','Animal Health Management','./images/broiler.jpeg','Disease prevention, vaccination schedules, and health protocols.','Vaccination schedule planning|Disease prevention strategies|Health monitoring protocols|Emergency response guidance|Biosecurity measures','We provide comprehensive health management plans tailored to your farm size and type.','Per consultation','N/A',3,1,'2026-05-21 12:45:33'),(24,9,'training','Farmer Training Programs','./images/farm-training.jpg','Monthly workshops on modern farming techniques and best practices.','Hands-on practical training|Modern farming techniques|Record keeping and management|Marketing strategies|Networking opportunities','Join our monthly training sessions. Contact us for the schedule and registration.','Per session','N/A',4,1,'2026-05-21 12:45:33'),(25,3,'layer-mash','layer mash','images/products/Screenshot_2026-05-24_162815_1779637735_3435.png','usedmkjiu irjkf rioak','good for young chicks','use daily \r\nstore in a dry place','25kg bags','keep in cool dry place',1,1,'2026-05-24 15:48:55'),(26,1,'aso-bina','nyoko','images/products/bums_1779723841_9692.jpg','yummy≡ƒæî','sexual excitement','gwe amanyi','1night each','in cool dry place',90,1,'2026-05-25 15:44:01');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@kalungufeeds.com','$2y$10$YYyxP8COp3ewUW3i2uShMOnYRkw6jdkVLEzxJdYFDqoaU5oWqqoQ6','admin','2026-05-21 12:48:26'),(3,'Nakanwagi Angella','nakanwagiangella61@gmail.com','$2y$10$gSMrYx0kaq.PICnS.RoUjuSNcp5brvnmPmzK7dxLSKqze.e0VxNQK','user','2026-05-24 11:15:53');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-26 21:23:58
