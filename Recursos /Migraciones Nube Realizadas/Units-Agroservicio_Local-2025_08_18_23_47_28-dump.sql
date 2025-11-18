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
-- Table structure for table `units`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código del catálogo CAT-014 del MH',
  `unit_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre de la unidad de medida',
  `unit_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo: peso, volumen, longitud, area, conteo, etc.',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descripción de la unidad',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica si la unidad está activa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_unit_code_unique` (`unit_code`),
  KEY `units_unit_code_is_active_index` (`unit_code`,`is_active`),
  KEY `units_unit_type_index` (`unit_type`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,'34','Kilogramo','peso','Unidad de masa del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(2,'36','Libra','peso','Unidad de masa del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(3,'38','Onza','peso','Unidad de masa del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(4,'39','Gramo','peso','Unidad de masa del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(5,'40','Miligramo','peso','Unidad de masa del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(6,'29','Tonelada métrica','peso','Unidad de masa del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(7,'30','Tonelada','peso','Unidad de masa del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(8,'31','Quintal métrico','peso','Unidad de masa del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(9,'32','Quintal','peso','Unidad de masa tradicional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(10,'33','Arroba','peso','Unidad de masa tradicional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(11,'23','Litro','volumen','Unidad de volumen del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(12,'26','Mililitro','volumen','Unidad de volumen del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(13,'22','Galón','volumen','Unidad de volumen del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(14,'27','Onza fluida','volumen','Unidad de volumen del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(15,'18','Metro cúbico','volumen','Unidad de volumen del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(16,'21','Pie cúbico','volumen','Unidad de volumen del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(17,'20','Barril','volumen','Unidad de volumen para líquidos',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(18,'01','Metro','longitud','Unidad de longitud del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(19,'06','Milímetro','longitud','Unidad de longitud del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(20,'02','Yarda','longitud','Unidad de longitud del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(21,'03','Vara','longitud','Unidad de longitud tradicional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(22,'04','Pie','longitud','Unidad de longitud del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(23,'05','Pulgada','longitud','Unidad de longitud del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(24,'13','Metro cuadrado','area','Unidad de área del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(25,'10','Hectárea','area','Unidad de área del Sistema Internacional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(26,'11','Manzana','area','Unidad de área tradicional',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(27,'12','Acre','area','Unidad de área del sistema imperial',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(28,'59','Unidad','conteo','Unidad de conteo estándar',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(29,'55','Millar','conteo','Mil unidades',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(30,'56','Medio millar','conteo','Quinientas unidades',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(31,'57','Ciento','conteo','Cien unidades',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(32,'58','Docena','conteo','Doce unidades',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(33,'24','Botella','especial','Unidad de empaque',1,'2025-08-19 05:26:26','2025-08-19 05:26:26'),(34,'99','Otra','especial','Otra unidad de medida',1,'2025-08-19 05:26:26','2025-08-19 05:26:26');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 23:47:28
