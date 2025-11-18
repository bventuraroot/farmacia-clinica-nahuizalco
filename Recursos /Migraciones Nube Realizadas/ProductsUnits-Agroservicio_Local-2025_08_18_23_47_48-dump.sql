-- MySQL dump 10.13  Distrib 8.0.43, for macos15.4 (arm64)
--
-- Host: 127.0.0.1    Database: agroservicio
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `product_unit_conversions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_unit_conversions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `conversion_factor` decimal(10,4) NOT NULL DEFAULT '1.0000' COMMENT 'Factor de conversión a unidad base',
  `price_multiplier` decimal(10,4) NOT NULL DEFAULT '1.0000' COMMENT 'Multiplicador de precio',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica si es la unidad por defecto',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica si la conversión está activa',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notas adicionales sobre la conversión',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_unit_conversion_unique` (`product_id`,`unit_id`),
  KEY `product_unit_conversions_unit_id_foreign` (`unit_id`),
  KEY `product_unit_conversions_product_id_unit_id_index` (`product_id`,`unit_id`),
  KEY `product_unit_conversions_product_id_is_default_index` (`product_id`,`is_default`),
  KEY `product_unit_conversions_product_id_is_active_index` (`product_id`,`is_active`),
  CONSTRAINT `product_unit_conversions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_unit_conversions_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_unit_conversions`
--

LOCK TABLES `product_unit_conversions` WRITE;
/*!40000 ALTER TABLE `product_unit_conversions` DISABLE KEYS */;
INSERT INTO `product_unit_conversions` VALUES (1,15,28,1.0000,1.0000,1,1,'Unidad por defecto del producto','2025-08-19 05:26:45','2025-08-19 05:26:45'),(2,15,2,1.0000,1.0000,0,1,'Venta por libra','2025-08-19 05:26:45','2025-08-19 05:26:45'),(3,15,1,2.2046,1.0000,0,1,'Venta por kilogramo','2025-08-19 05:26:45','2025-08-19 05:26:45'),(4,20,2,1.0000,1.0000,1,1,'Unidad base - venta por libra','2025-08-19 05:34:15','2025-08-19 05:34:15'),(5,20,28,55.0000,1.0000,0,1,'Saco de 55 libras - venta por saco completo','2025-08-19 05:34:15','2025-08-19 05:34:15'),(6,20,1,2.2046,1.0000,0,1,'Venta por kilogramo','2025-08-19 05:34:15','2025-08-19 05:34:15'),(7,20,34,1.1765,1.0000,0,1,'Venta por valor en dólares','2025-08-19 05:34:15','2025-08-19 05:34:15');
/*!40000 ALTER TABLE `product_unit_conversions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 23:47:48
