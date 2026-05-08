-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: artiyasam_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (2,'admin','$2y$10$L0r0YfBzuPP6zOVMoHjjE.qfKS9uyhmGxqjecViJw5FNiGxmBB/RW','2026-02-27 13:19:21');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_title','Artıyaşam Turizm','2026-02-27 13:15:47'),(2,'site_description','Ankara çıkışlı turlar','2026-02-27 13:15:47'),(3,'site_keywords','tur, seyahat, gezi, tatil, doğa, doga','2026-02-27 13:15:47'),(4,'contact_email','web@artiyasam.com','2026-02-27 13:15:47'),(5,'contact_phone','+90 530 489 87 00','2026-02-27 13:15:47'),(6,'address','Ankara, Türkiye','2026-02-27 13:15:47'),(7,'facebook_url','https://facebook.com/artiyasam','2026-02-27 13:15:47'),(8,'twitter_url','https://twitter.com/artiyasam','2026-02-27 13:15:47'),(9,'instagram_url','https://instagram.com/artiyasam','2026-02-27 13:15:47'),(10,'about_text','Artıyaşam Turizm olarak...','2026-02-27 13:15:47'),(11,'site_logo','artiyasamlogo2.png','2026-03-02 13:47:33'),(12,'site_favicon','favicon.ico','2026-03-02 13:47:33'),(13,'site_language','tr','2026-03-02 13:47:33'),(14,'maintenance_mode','0','2026-03-02 13:47:33'),(15,'contact_phone2','0532 123 45 67','2026-03-02 13:47:33'),(16,'contact_fax','','2026-03-02 13:47:33'),(17,'contact_address','Meşrutiyet, Selanik Cd 78/7, 06420 Çankaya/Ankara','2026-03-02 13:47:33'),(18,'contact_map','<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3639.147061344414!2d32.85586118139792!3d39.91575510143188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d34fab191a5765%3A0xf98a5f70247375d8!2zQXJ0xLF5YcWfYW0gVHVyaXpt!5e0!3m2!1str!2str!4v1772292355485!5m2!1str!2str\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>','2026-03-02 13:47:33'),(19,'working_hours','Hafta içi 09:00 - 18:00','2026-03-02 13:47:33'),(20,'whatsapp_number','905304898700','2026-03-02 13:47:33'),(21,'whatsapp_message','Merhaba, tur hakkında bilgi almak istiyorum.','2026-03-02 13:47:33'),(22,'youtube_url','https://youtube.com/artiyasam','2026-03-02 13:47:33'),(23,'linkedin_url','','2026-03-02 13:47:33'),(24,'pinterest_url','','2026-03-02 13:47:33'),(25,'tiktok_url','','2026-03-02 13:47:33'),(26,'footer_about','Ankara çıkışlı butik turlar, doğa ve kültür gezileri. Size özel rotalar, unutulmaz deneyimler.','2026-03-02 13:47:33'),(27,'footer_copyright','© 2026 Artıyaşam Turizm | Tüm Hakları Saklıdır','2026-03-02 13:47:33'),(28,'footer_newsletter_title','Bültenimize Abone Olun','2026-03-02 13:47:33'),(29,'footer_newsletter_text','Yeni turlar ve fırsatlardan ilk siz haberdar olun','2026-03-02 13:47:33'),(30,'meta_author','Artıyaşam Turizm','2026-03-02 13:47:33'),(31,'google_analytics','','2026-03-02 13:47:33'),(32,'google_verification','','2026-03-02 13:47:33'),(33,'yandex_verification','','2026-03-02 13:47:33'),(34,'facebook_pixel','','2026-03-02 13:47:33'),(35,'og_image','','2026-03-02 13:47:33'),(36,'og_title','Artıyaşam Turizm - Unutulmaz Tur Deneyimleri','2026-03-02 13:47:33'),(37,'og_description','Ankara çıkışlı butik turlar, yurtiçi ve yurtdışı gezileri','2026-03-02 13:47:33'),(38,'theme_color','#ff8800','2026-03-02 13:47:33'),(39,'secondary_color','#0f172a','2026-03-02 13:47:33'),(40,'font_family','Poppins','2026-03-02 13:47:33'),(41,'tours_per_page','12','2026-03-02 13:47:33'),(42,'show_featured_tours','1','2026-03-02 13:47:33'),(43,'show_category_counts','1','2026-03-02 13:47:33'),(44,'hero_auto_change','1','2026-03-02 13:47:33'),(45,'hero_change_interval','10','2026-03-02 13:47:33'),(46,'smtp_host','smtp.gmail.com','2026-03-02 13:47:33'),(47,'smtp_port','587','2026-03-02 13:47:33'),(48,'smtp_user','info@artiyasam.com','2026-03-02 13:47:33'),(49,'smtp_pass','','2026-03-02 13:47:33'),(50,'smtp_encryption','tls','2026-03-02 13:47:33'),(51,'notification_email','admin@artiyasam.com','2026-03-02 13:47:33'),(52,'max_login_attempts','5','2026-03-02 13:47:33'),(53,'login_lockout_time','15','2026-03-02 13:47:33'),(54,'session_timeout','30','2026-03-02 13:47:33'),(55,'force_https','0','2026-03-02 13:47:33'),(56,'enable_captcha','1','2026-03-02 13:47:33'),(57,'google_maps_api','','2026-03-02 13:47:33'),(58,'recaptcha_site_key','','2026-03-02 13:47:33'),(59,'recaptcha_secret_key','','2026-03-02 13:47:33'),(60,'currency_api_key','','2026-03-02 13:47:33'),(61,'total_visitors','0','2026-03-02 13:47:33'),(62,'total_bookings','0','2026-03-02 13:47:33'),(63,'last_backup','','2026-03-02 13:47:33');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_calendar`
--

DROP TABLE IF EXISTS `tour_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price_adult` decimal(10,2) DEFAULT NULL,
  `price_child` decimal(10,2) DEFAULT NULL,
  `price_infant` decimal(10,2) DEFAULT NULL,
  `price_single_supplement` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'TL',
  `available_quota` int(11) DEFAULT 0,
  `status` enum('available','limited','sold_out','cancelled') DEFAULT 'available',
  `special_note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_calendar_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_calendar`
--

LOCK TABLES `tour_calendar` WRITE;
/*!40000 ALTER TABLE `tour_calendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_calendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_details`
--

DROP TABLE IF EXISTS `tour_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `transportation` varchar(255) DEFAULT NULL,
  `accommodation` varchar(255) DEFAULT NULL,
  `meals` varchar(255) DEFAULT NULL,
  `guide` varchar(100) DEFAULT NULL,
  `insurance` varchar(100) DEFAULT NULL,
  `min_participant` int(11) DEFAULT 1,
  `max_participant` int(11) DEFAULT 50,
  `difficulty` enum('kolay','orta','zor') DEFAULT 'orta',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_details_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_details`
--

LOCK TABLES `tour_details` WRITE;
/*!40000 ALTER TABLE `tour_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_excluded`
--

DROP TABLE IF EXISTS `tour_excluded`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_excluded` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `item` text NOT NULL,
  `icon` varchar(50) DEFAULT '❌',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_excluded_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_excluded`
--

LOCK TABLES `tour_excluded` WRITE;
/*!40000 ALTER TABLE `tour_excluded` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_excluded` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_extras`
--

DROP TABLE IF EXISTS `tour_extras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'TL',
  `is_per_person` tinyint(1) DEFAULT 1,
  `icon` varchar(50) DEFAULT '➕',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_extras_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_extras`
--

LOCK TABLES `tour_extras` WRITE;
/*!40000 ALTER TABLE `tour_extras` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_extras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_images`
--

DROP TABLE IF EXISTS `tour_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_cover` tinyint(1) DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_images_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_images`
--

LOCK TABLES `tour_images` WRITE;
/*!40000 ALTER TABLE `tour_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_important_info`
--

DROP TABLE IF EXISTS `tour_important_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_important_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `icon` varchar(50) DEFAULT 'ℹ️',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_important_info_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_important_info`
--

LOCK TABLES `tour_important_info` WRITE;
/*!40000 ALTER TABLE `tour_important_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_important_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_included`
--

DROP TABLE IF EXISTS `tour_included`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_included` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `item` text NOT NULL,
  `icon` varchar(50) DEFAULT '✅',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_included_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_included`
--

LOCK TABLES `tour_included` WRITE;
/*!40000 ALTER TABLE `tour_included` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_included` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_itinerary`
--

DROP TABLE IF EXISTS `tour_itinerary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_itinerary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `accommodation` varchar(255) DEFAULT NULL,
  `meals` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_itinerary_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_itinerary`
--

LOCK TABLES `tour_itinerary` WRITE;
/*!40000 ALTER TABLE `tour_itinerary` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_itinerary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_practical_info`
--

DROP TABLE IF EXISTS `tour_practical_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_practical_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `info_key` varchar(100) NOT NULL,
  `info_value` text NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_practical_info_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_practical_info`
--

LOCK TABLES `tour_practical_info` WRITE;
/*!40000 ALTER TABLE `tour_practical_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_practical_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_themes`
--

DROP TABLE IF EXISTS `tour_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tour_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `background_color` varchar(50) DEFAULT '#0f172a',
  `background_image` varchar(255) DEFAULT NULL,
  `text_color` varchar(50) DEFAULT '#ffffff',
  `accent_color` varchar(50) DEFAULT '#ff7b00',
  `custom_css` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_themes_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_themes`
--

LOCK TABLES `tour_themes` WRITE;
/*!40000 ALTER TABLE `tour_themes` DISABLE KEYS */;
/*!40000 ALTER TABLE `tour_themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tours`
--

DROP TABLE IF EXISTS `tours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'yurtici',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `tour_date` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('active','passive') DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tours`
--

LOCK TABLES `tours` WRITE;
/*!40000 ALTER TABLE `tours` DISABLE KEYS */;
/*!40000 ALTER TABLE `tours` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-02 16:50:26
