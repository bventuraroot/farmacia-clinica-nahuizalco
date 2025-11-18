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
-- Table structure for table `salesdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salesdetails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `unit_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre de la unidad para referencia',
  `conversion_factor` decimal(10,4) NOT NULL DEFAULT '1.0000' COMMENT 'Factor de conversión usado',
  `base_quantity_used` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'Cantidad descontada del inventario en unidad base',
  `amountp` int NOT NULL,
  `pricesale` decimal(10,2) NOT NULL,
  `priceunit` decimal(10,2) NOT NULL,
  `nosujeta` decimal(10,2) NOT NULL,
  `exempt` decimal(10,2) NOT NULL,
  `detained` decimal(10,2) DEFAULT NULL,
  `detained13` decimal(10,2) NOT NULL,
  `renta` decimal(10,2) NOT NULL,
  `fee` decimal(10,2) DEFAULT NULL,
  `feeiva` decimal(10,2) DEFAULT NULL,
  `reserva` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destino` bigint DEFAULT NULL,
  `linea` bigint DEFAULT NULL,
  `canal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salesdetails_sale_id_foreign` (`sale_id`),
  KEY `salesdetails_product_id_foreign` (`product_id`),
  KEY `salesdetails_destino_id_foreign` (`destino`),
  KEY `salesdetails_linea_id_foreign` (`linea`),
  KEY `salesdetails_user_id_foreign` (`user_id`),
  KEY `salesdetails_unit_id_foreign` (`unit_id`),
  KEY `salesdetails_product_id_unit_id_index` (`product_id`,`unit_id`),
  CONSTRAINT `salesdetails_destino_id_foreign` FOREIGN KEY (`destino`) REFERENCES `aeropuertos` (`id_aeropuerto`),
  CONSTRAINT `salesdetails_linea_id_foreign` FOREIGN KEY (`linea`) REFERENCES `aerolineas` (`id_aerolinea`),
  CONSTRAINT `salesdetails_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salesdetails_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salesdetails_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salesdetails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salesdetails`
--

LOCK TABLES `salesdetails` WRITE;
/*!40000 ALTER TABLE `salesdetails` DISABLE KEYS */;
INSERT INTO `salesdetails` VALUES (36,101,15,NULL,NULL,1.0000,0.0000,1,45.36,45.36,0.00,0.00,0.00,5.90,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-08-19 05:42:07','2025-08-19 05:42:07'),(37,101,15,NULL,NULL,1.0000,0.0000,1,45.36,45.36,0.00,0.00,0.00,5.90,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-08-19 05:42:45','2025-08-19 05:42:45');
/*!40000 ALTER TABLE `salesdetails` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 23:49:11
