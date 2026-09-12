-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: johen_marketplace
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `account_listings`
--

DROP TABLE IF EXISTS `account_listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_listings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `game` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_photo_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_photo_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_photo_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_photo_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_photo_5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `specifications` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `original_price` decimal(12,2) DEFAULT NULL,
  `whatsapp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `promo_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `discount_percent` int DEFAULT NULL,
  `is_sold` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_listings`
--

LOCK TABLES `account_listings` WRITE;
/*!40000 ALTER TABLE `account_listings` DISABLE KEYS */;
INSERT INTO `account_listings` VALUES (1,'Mobile Legends','account-listings/R1pHZo5UEUCIIVvXVepD82icqoPHfjhPAvQCDwZV.png','account-listings/ZFbOk5xsYLE1PHv9l6M7aqMGZve2ud7WS8Ujykjh.jpg','account-listings/EBVuB2wlX5D5IaRUQpFxD5SPbCrzLfieUznMQjTm.jpg','account-listings/JdjBKFQIQmJSq75jzlh82ypwcB3incnwRJtuHhXC.jpg','account-listings/ser6ueyDBUWFXUt3SJTjz6iYMEx4DeQNXkgYAhjL.jpg','account-listings/33Bj5Cg79DwIhS9HqCUEWJHWefMt57Hy1abhz2cU.jpg','account-listings/IAXTL0biNXacjk0mChlXtJvJIjn1RFuo73d0OfiL.png','https://www.youtube.com/watch?v=HQKOCncERYo&time_continue=0&source_ve_path=NzY3NTg&embeds_referring_euri=https%3A%2F%2Fvexagame.com%2F','Zodiac Selena','MLBB - ID. 208801854 | 10.000.000 | RM.2.254\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV & HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING & THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS & ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY & ALUCARD STARWARS, LEOMORD & IRITHEL DUCATI, CHANGE ANGELA RUBY & LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS & FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR & DYROTH KOF, CHANGE & FLORYN SANRIO, CLAUDE & BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX  RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen MLBB',4500000.00,5000000.00,'085156521726',1,'limited',NULL,0,'2026-07-17 18:11:46','2026-07-20 00:21:23'),(2,'PUBG Mobile',NULL,'account-listings/OZ5TjynORTyQjF4p9UxLUSbTTjN6SbmjtaVxKxBx.png','account-listings/ESb2c5VrZrEWKUDEca3H72t1XDkermU7nC296uS0.webp','account-listings/LE9byYFdpqcSDHXLMiK7rk67SHh0D3FrCpSJto3F.webp','account-listings/3rDCJ5V74sGZ2lpNdx2o2vOgWIxw4EJfUTnbSY7O.png','account-listings/DzrJfvOvbwzw4Luqk6obV8gH8V61hK70uKf1YuDv.png','account-listings/saT4DarI7WQ9zCO3LBg7Sfpcz3UFgAPf38NVCRY2.png','https://www.youtube.com/watch?v=uQ8hMUztxlA','OAOSDKWKMDKWD','MLBB - ID. 208801854 | 10.000.000 | RM.2.254\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV & HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING & THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS & ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY & ALUCARD STARWARS, LEOMORD & IRITHEL DUCATI, CHANGE ANGELA RUBY & LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS & FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR & DYROTH KOF, CHANGE & FLORYN SANRIO, CLAUDE & BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX  RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen PUBG',3000000.00,3500000.00,'085156521726',1,'none',NULL,0,'2026-07-17 18:15:53','2026-07-20 00:29:30'),(3,'Mobile Legends',NULL,'account-listings/R9iJICmFApawMz4j6B5mhwsIRdkHpoYdSuo1yl4y.jpg','account-listings/zIAIo3WohQFf6R3UCTtrDdxuafwNsrmGbP6GuOH0.jpg','account-listings/f7MBwP7nJi10krOxQTpGyd0rrkP3XbgXgfXtmvku.jpg','account-listings/a6w0zj0JU4wovKrD6skvcV8jCUTgUecyjeughjSL.jpg','account-listings/kp2xtsYdj6iyvEy1iBndDpWqVRwkub9Yt9eAnbRW.jpg','account-listings/RfbXtOJFRsCYJygqzhSvf9z6PHFx5lsQzyEl7F1n.jpg','https://www.youtube.com/watch?v=HQKOCncERYo&time_continue=0&source_ve_path=NzY3NTg&embeds_referring_euri=https%3A%2F%2Fvexagame.com%2F','Zodiac Lancelot','MLBB - ID. 208801854 | 10.000.000 | RM.2.254\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV & HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING & THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS & ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY & ALUCARD STARWARS, LEOMORD & IRITHEL DUCATI, CHANGE ANGELA RUBY & LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS & FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR & DYROTH KOF, CHANGE & FLORYN SANRIO, CLAUDE & BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX  RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen MLBB',4500000.00,5000000.00,'085156521726',1,'promo',NULL,0,'2026-07-17 18:16:50','2026-07-19 20:25:24'),(4,'Roblox',NULL,'account-listings/SZPxdsWgBD6vKaS9Ch6WcW1piGc8W9RMFsi0S4Sf.png',NULL,NULL,NULL,NULL,NULL,'https://www.youtube.com/watch?v=uQ8hMUztxlA','OAOSDKWKMDKWD','MLBB - ID. 208801854 | 10.000.000 | RM.2.254\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV & HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING & THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS & ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY & ALUCARD STARWARS, LEOMORD & IRITHEL DUCATI, CHANGE ANGELA RUBY & LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS & FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR & DYROTH KOF, CHANGE & FLORYN SANRIO, CLAUDE & BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX  RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen Roblox',500000.00,600000.00,'085156521726',1,'hot',NULL,0,'2026-07-17 19:02:03','2026-07-20 00:29:13'),(5,'Mobile Legends',NULL,'account-listings/Ix22kZ8Or3YflXi1Z2EEphzXW7Hmh1oXvRKNJ41Z.jpg','account-listings/qG54jHejhgB8aH6RudMBYyeue2JQwpIF3sH6loYR.jpg','account-listings/qdqPhUebKITntP3OHD2YI1m1diHxh6bqVtYTv7yn.jpg','account-listings/TCsmkzEF07kqbZKjYjmUkZYrqDKzoahl1Ysnm7ls.jpg','account-listings/5K1bnrsErN0ute7Owe8Q7MZTB3bmds2Wg5CBRxwa.jpg',NULL,'https://www.youtube.com/watch?v=HQKOCncERYo','Legend Lunox','MLBB - ID. 208801854 | 10.000.000 | RM.2.254\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV & HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING & THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS & ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY & ALUCARD STARWARS, LEOMORD & IRITHEL DUCATI, CHANGE ANGELA RUBY & LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS & FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR & DYROTH KOF, CHANGE & FLORYN SANRIO, CLAUDE & BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX  RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen MLBB',4500000.00,5000000.00,'085156521726',1,'hot',NULL,0,'2026-07-20 00:19:09','2026-07-20 00:28:59'),(6,'Mobile Legends',NULL,'account-listings/8Lp0zsGpPmXjKAsHaO5QWTLCcjzasdFvyEMEdgQ6.jpg','account-listings/TheV3l1hxJVmotadsBQURTZiLClRmGEpdYOAwm0F.jpg','account-listings/LGpihxw6XdDe9M02JRqWKhcJPl8PuGHWJcDvda9m.jpg','account-listings/2NJzmzdvD63Kfs42YhRs7wDj3aqq2au1LQ4eTwZT.jpg','account-listings/4hwUljuXnDH4ZQsqajEXjCi6DQORYxB9aD4zCIJu.jpg',NULL,'https://www.youtube.com/watch?v=HQKOCncERYo','Legend Saber','MLBB - ID. 208801854 | 10.000.000 | RM.2.254\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV & HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING & THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS & ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY & ALUCARD STARWARS, LEOMORD & IRITHEL DUCATI, CHANGE ANGELA RUBY & LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS & FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR & DYROTH KOF, CHANGE & FLORYN SANRIO, CLAUDE & BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX  RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen MLBB',3250000.00,3500000.00,'085156521726',1,'promo',NULL,0,'2026-07-20 00:20:06','2026-07-20 00:29:04'),(7,'Mobile Legends',NULL,'account-listings/OIijNu1yEXJURKv4XnxrcF1b5gSOzXBAU0wFwNDK.jpg','account-listings/G1jUAFQzjPRxvZULjk8Uk7vqAEsECdydBMvmFw1x.jpg','account-listings/BrPwVhjc7fARy6M8U8EEuJ4KkTrl9IvF8MvKYJ2X.jpg','account-listings/0PJ3kVX34sGYqPCsMQ3pyC9nqWgc4D5nwkaEl5DM.jpg','account-listings/H9j15yxhcdtLy6cP0joed6Jjk4vtg2zozk3YRNhg.jpg',NULL,'https://www.youtube.com/watch?v=HQKOCncERYo','Legend Johnson','MLBB - ID. 208801854 | 10.000.000 | RM.2.254\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV & HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING & THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS & ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY & ALUCARD STARWARS, LEOMORD & IRITHEL DUCATI, CHANGE ANGELA RUBY & LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS & FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR & DYROTH KOF, CHANGE & FLORYN SANRIO, CLAUDE & BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX  RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen MLBB',4500000.00,5000000.00,'085156521726',1,'limited',NULL,0,'2026-07-20 00:31:27','2026-07-20 00:31:27'),(8,'Mobile Legends',NULL,'account-listings/qZgk1ZiPwUG5NzJoSU7rQP3ldczk0xieZtKkE5DP.jpg','account-listings/T3Z7h2VhABQpdF7yUpCwMw0hc7o63tPzr7TZ40ng.jpg','account-listings/IzGaJfrbz5BGTexLqmSI13VHVHOJ9WroxnXTnphn.jpg','account-listings/aD82Ql4Oai6VuQhJx7dwgrxYg8UtvTZkM7q7gwVh.jpg','account-listings/dvWN0TItl2ddPGlZvyqWN2nR6JolWbQmgypTRpX0.jpg',NULL,'https://www.youtube.com/watch?v=HQKOCncERYo','Legend Fanny','MLBB - ID. 208801854 | 10.000.000 | RM.2.254<br />\r\nLEGEND : FRANCO JOHNSON LESLEY LUNOX GRANGER,COLLECTOR : CLINT NATALIA YSS PHARSA LESLEY GUSION BADANG LING BRODY MELISSA,SUYOU SASUKE, LANCELOT DAWNING, MOSKOV &amp; HANABI ALLSTAR, MELISSA JJK, NOLAN 11.11, LING &amp; THAMUZ KUNGFUPANDA, XBORG GRANGER ALDOUS &amp; ROGER TRANSFORMERS, GRANGER EXORCISTS, HARITH HXH, KIMMY &amp; ALUCARD STARWARS, LEOMORD &amp; IRITHEL DUCATI, CHANGE ANGELA RUBY &amp; LAYLA ASPIRANTS, HANABI SOULVESSEL, MARTIS &amp; FANNY AOT, VEXANA ZENITH, NANA MISTBENDER, KARINA CHOU AURORA VALIR &amp; DYROTH KOF, CHANGE &amp; FLORYN SANRIO, CLAUDE &amp; BEATRIX PRIME, KAGURA CLOUDS,EMBLEM FULL MAX RECALL TASTAS + RECALL EVOS + RECALL AYAM + EX IMMO 102','Johen MLBB',7000000.00,8500000.00,'085156521726',1,'promo',NULL,0,'2026-07-20 00:33:22','2026-07-20 00:33:22');
/*!40000 ALTER TABLE `account_listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_orders`
--

DROP TABLE IF EXISTS `account_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_ref` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_listing_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_invoice_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_string` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_price` decimal(12,2) NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_orders_order_ref_unique` (`order_ref`),
  KEY `account_orders_account_listing_id_foreign` (`account_listing_id`),
  KEY `account_orders_user_id_foreign` (`user_id`),
  KEY `account_orders_gateway_invoice_id_index` (`gateway_invoice_id`),
  CONSTRAINT `account_orders_account_listing_id_foreign` FOREIGN KEY (`account_listing_id`) REFERENCES `account_listings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `account_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_orders`
--

LOCK TABLES `account_orders` WRITE;
/*!40000 ALTER TABLE `account_orders` DISABLE KEYS */;
INSERT INTO `account_orders` VALUES (1,NULL,3,NULL,'ilyas','m.ilyasalfadlih@gmail.com','085156521726','BCA Virtual Account',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-19 18:15:59','2026-07-19 18:15:59'),(2,NULL,3,NULL,'ilyas','m.ilyasalfadlih@gmail.com','085156521726','BNI Virtual Account',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-19 18:29:44','2026-07-19 18:29:44'),(3,NULL,3,NULL,'ilyas','m.ilyasalfadlih@gmail.com','085156521726','GoPay',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-19 18:40:26','2026-07-19 18:40:26'),(4,NULL,3,NULL,'ilyas','m.ilyasalfadlih@gmail.com','085156521726','GoPay',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-19 21:49:00','2026-07-19 21:49:00'),(5,NULL,1,NULL,'Muhammad Ilyas','m.ilyasalfadlih@gmail.com','085156521726','Dana',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-20 01:52:43','2026-07-20 01:52:43'),(6,NULL,8,NULL,'Ahmad Musyadad Haury','ahmadmusyadadhaury@gmail.com','089507135674','Dana',NULL,NULL,NULL,'pending',7000000.00,'dqeq','2026-07-20 02:06:55','2026-07-20 02:06:55'),(7,NULL,1,NULL,'Muhammad Ilyas','m.ilyasalfadlih@gmail.com','085156521726','QRIS',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-20 18:40:05','2026-07-20 18:40:05'),(8,NULL,3,NULL,'Muhammad Ilyas','m.ilyasalfadlih@gmail.com','085156521726','GoPay',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-20 18:40:43','2026-07-20 18:40:43'),(9,NULL,8,NULL,'Muhammad Ilyas','m.ilyasalfadlih@gmail.com','085156521726','BCA Virtual Account',NULL,NULL,NULL,'pending',7000000.00,NULL,'2026-07-20 21:21:08','2026-07-20 21:21:08'),(10,NULL,8,6,'ahmad','ahmadmusyadadhaury@gmail.com','089507135674','QRIS',NULL,NULL,NULL,'pending',7000000.00,'wrfawfdfed','2026-07-20 23:22:32','2026-07-20 23:22:32'),(11,NULL,3,8,'Muhammad Ilyas','m.ilyasalfadlih@gmail.com','085156521726','Mandiri Virtual Account',NULL,NULL,NULL,'pending',4500000.00,NULL,'2026-07-21 21:25:15','2026-07-21 21:25:15'),(12,NULL,8,8,'Muhammad Ilyas','m.ilyasalfadlih@gmail.com','085156521726','GoPay',NULL,NULL,NULL,'pending',7000000.00,NULL,'2026-07-22 00:24:56','2026-07-22 00:24:56'),(13,NULL,6,6,'ahmad','ahmadmusyadadhaury@gmail.com','085156521726','BCA Virtual Account',NULL,NULL,NULL,'pending',3250000.00,NULL,'2026-07-27 09:40:11','2026-07-27 09:40:11'),(14,'JBA-AMZ2TVYT3P',8,NULL,'Ahmad Musyadad Haury','ahmadmusyadadhaury@gmail.com','089507135674','QRIS',NULL,NULL,NULL,'pending',7000000.00,NULL,'2026-09-10 08:56:27','2026-09-10 08:56:27');
/*!40000 ALTER TABLE `account_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_img_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_img_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_img_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carousel_bg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_bg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_bg_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'center',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'topup',
  `requires_zone_id` tinyint(1) NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'PUBG Mobile',NULL,'brands/pubg/icon.webp','brands/br6uLboonrEkWm8K69ee8NsJM69EsYIvu9g0ajN4.webp','brands/nLChMbTdsHNNeV2WcaPAtfxLMtstHpveCkMV6UVk.webp','brands/I2hSc6NrX5wuP06JVquxeNMYtUAupq6rkbPzLs1P.webp','brands/EOqX9ZrDq0ci6lutwx1Ntxp8NVeDFRUcLyH84d8a.webp','brands/pubg/carousel-bg.webp','brands/bg/fTBiSxkhxHQD2Kt2BHNTqsppnZXUncVrgNpMBvHd.webp','center','Tencent','topup',0,'Isi ulang UC PUBG Mobile dengan lebih praktis melalui layanan top up yang cepat dan terpercaya. Didukung berbagai metode pembayaran dan proses transaksi yang efisien, UC akan langsung masuk ke akunmu dalam waktu singkat sehingga kamu bisa segera kembali bertempur dan meraih Winner Winner Chicken Dinner.',1,1,6,'2026-07-14 23:28:03','2026-09-07 04:32:55'),(3,'Roblox',NULL,'brands/roblox/icon.webp','brands/roblox/variant.webp',NULL,NULL,NULL,'brands/roblox/icon.webp','brands/roblox/icon.webp','center','Roblox Studio','topup',0,NULL,1,1,8,'2026-07-14 23:30:51','2026-09-07 04:32:55'),(4,'Free Fire',NULL,'brands/ff/icon.webp','brands/ff/banner.webp','brands/ff/variant.webp',NULL,NULL,'brands/ff/banner.webp','brands/ff/banner.webp','center','Garena','topup',0,NULL,1,1,9,'2026-07-14 23:33:02','2026-09-07 04:32:55'),(5,'Valorant',NULL,'brands/valorant/icon.webp','brands/valorant/banner.webp','brands/valorant/variant.webp',NULL,NULL,'brands/valorant/banner.webp','brands/valorant/banner.webp','center','Riot Games','topup',0,NULL,1,1,10,'2026-07-14 23:33:26','2026-09-07 04:32:56'),(6,'Honor of Kings',NULL,'brands/3FXvkSyBJ5KAlm7v5YVzjPFLH3947KZUkAasbziE.webp',NULL,NULL,NULL,NULL,NULL,NULL,'center','Tencent','topup',1,NULL,1,0,0,'2026-07-15 20:38:43','2026-09-07 04:32:56'),(7,'Arena of Valor',NULL,'brands/OiiVemWXhW9x3CR5y3Ca4moyNAR1NyehD8GwyGb3.webp',NULL,NULL,NULL,NULL,NULL,NULL,'center','Tencent Games','topup',1,NULL,1,0,2,'2026-07-15 20:39:34','2026-09-07 04:32:56'),(8,'Arena Breakout',NULL,'brands/qRXlGtJHILPzyrQiJbzvvPjsL4MBKrqEzSHGvHbV.webp',NULL,NULL,NULL,NULL,NULL,'brands/bg/jH4BUnnOjs0mq4r0lPfDI8WAwF4fbLvWvwyUrD8z.webp','center','Level Infinite','topup',0,NULL,1,0,3,'2026-07-15 20:40:20','2026-09-07 04:32:56'),(9,'Call of Duty',NULL,'brands/DwQa1rsBcYqAxN7FbvUvuEMQy2MamPpc3MYEmy4W.webp',NULL,NULL,NULL,NULL,NULL,'brands/bg/w1qqbQDvvlthzQ68ZLMm8MkrZf80Or0GdMYc0XGV.jpg','48.00721304347826% 31.15944202898551%','Garena','topup',0,NULL,1,0,4,'2026-07-15 20:41:18','2026-09-07 04:53:14'),(10,'Point Blank',NULL,'brands/pointblank/icon.webp',NULL,NULL,NULL,NULL,'brands/pointblank/icon.webp','brands/pointblank/icon.webp','center','Zepetto','topup',0,NULL,1,0,5,'2026-07-15 20:42:11','2026-09-07 04:32:56'),(11,'Genshin Impact',NULL,'brands/SN1QqimGF4vmztG4nBvVjWXYlinF9uba2LOt98D1.webp',NULL,NULL,NULL,NULL,NULL,NULL,'center','HoYoverse','topup',1,NULL,1,0,0,'2026-07-15 20:43:21','2026-09-07 04:32:56'),(12,'Clash of Clans',NULL,'brands/puyrabRph8RKE8XFv5mVQcPSqVPKcnoUb0OXWUoB.webp',NULL,NULL,NULL,NULL,NULL,NULL,'center','Supercell','topup',0,NULL,1,0,12,'2026-07-15 20:43:46','2026-09-07 04:32:56'),(13,'Clash Royale',NULL,'brands/B8jGGBu8DhFu2vijLVHuw8VASnZMjzpTUBwE0cmj.webp',NULL,NULL,NULL,NULL,NULL,NULL,'center','Supercell','topup',0,NULL,1,0,13,'2026-07-15 20:44:09','2026-09-07 04:32:56'),(14,'Asphalt 9',NULL,'brands/s4zQa7tleY2XsMgJRHLqjOrerbvzy7JBs5Dg4kam.webp',NULL,NULL,NULL,NULL,NULL,NULL,'center','Gameloft SE','topup',0,NULL,1,0,14,'2026-07-15 20:44:33','2026-09-07 04:32:57'),(15,'CrossFire',NULL,'brands/cYgpcYFUmx04OxJwmTN5Lz0m5gmd8i7Om7FPRL7s.webp',NULL,NULL,NULL,NULL,NULL,NULL,'center','Level Infinite','topup',0,NULL,1,0,15,'2026-07-15 20:45:03','2026-09-07 04:32:57'),(16,'E-Football',NULL,'brands/efootball/icon.webp','brands/efootball/banner.webp',NULL,NULL,NULL,'brands/efootball/banner.webp','brands/efootball/banner.webp','center','Konami','topup',0,NULL,1,1,1,'2026-07-17 19:28:07','2026-09-07 04:32:57'),(17,'FC Mobile',NULL,'brands/fcmobile/icon.webp','brands/fcmobile/banner.webp',NULL,NULL,NULL,'brands/fcmobile/icon.webp','brands/fcmobile/icon.webp','center','EA Sports','topup',0,NULL,1,1,12,'2026-07-17 19:31:16','2026-09-07 04:32:57'),(18,'Mobile Legends',NULL,'brands/ml/icon.webp','brands/hTDLCsWTMjMgU5vT9IUvzOoFx532FU8J5wqJgEjo.webp','brands/ml/art-1.webp','brands/ml/art-2.webp','brands/Rw70UnpkmH6Fkbv2yofjGehJjzTmn4signK7bsYs.webp','brands/bg/7J0aUEbBPrXQtEByXd6gvFHzpFd8CWyMW6YxWoq9.webp','brands/ml/art-2.webp','center','Moonton','topup',1,NULL,1,1,7,'2026-07-19 19:06:55','2026-09-07 04:32:58'),(19,'tes',NULL,'brands/Sbq2rfUIxrQsSP3lu2j0HdeIWCQkDPu2lOc7HiTJ.webp','brands/0jaRYIhcpivhgjW45RDXYbsVl98PbxA0MJBDf1rX.webp',NULL,NULL,NULL,'brands/bg/H53UPMYS3OSYviJMWlK4hjpRJiqVM2Q5XPmMcRRp.webp','brands/bg/BWav2nWCd86XwiHbQOd38ky2aVZ4BishTgrbw8hv.webp','center','tis','topup',0,NULL,1,1,6,'2026-09-07 03:20:56','2026-09-07 04:32:58');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('johen-marketplace-cache-popup_banners_active_ids','a:0:{}',1789034159),('johen-marketplace-cache-setting_digiflazz_key','s:40:\"dev-5e823820-96f2-11f1-8596-25a9cbaec972\";',1789034134),('johen-marketplace-cache-setting_digiflazz_last_sync','s:19:\"2026-09-01 11:26:07\";',1789033501),('johen-marketplace-cache-setting_digiflazz_product_count','s:2:\"31\";',1789033501),('johen-marketplace-cache-setting_digiflazz_production','s:1:\"1\";',1789034134),('johen-marketplace-cache-setting_digiflazz_username','s:12:\"teyumoor1K9g\";',1789034133),('johen-marketplace-cache-setting_jba_hero_banner','s:25:\"settings/jba-banner-1.png\";',1789034166),('johen-marketplace-cache-setting_jba_hero_banner_2','s:25:\"settings/jba-banner-2.png\";',1789034166),('johen-marketplace-cache-setting_jba_hero_banner_3','s:25:\"settings/jba-banner-3.png\";',1789034166),('johen-marketplace-cache-setting_promo_topup_active','N;',1788854984),('johen-marketplace-cache-setting_qris_image','s:54:\"settings/p6VVwu9j5wrXr58vQvbqqVUQTWtw0g3W1CkWCYnW.webp\";',1789034187),('johen-marketplace-cache-setting_site_hero_banner','s:27:\"settings/hero-banner-1.webp\";',1789034159),('johen-marketplace-cache-setting_site_hero_banner_2','s:27:\"settings/hero-banner-2.webp\";',1789034159),('johen-marketplace-cache-setting_site_hero_banner_3','s:27:\"settings/hero-banner-3.webp\";',1789034159),('johen-marketplace-cache-xendit.available_channels','a:11:{i:0;s:3:\"BCA\";i:1;s:3:\"BRI\";i:2;s:3:\"BNI\";i:3;s:7:\"MANDIRI\";i:4;s:7:\"PERMATA\";i:5;s:8:\"ALFAMART\";i:6;s:9:\"INDOMARET\";i:7;s:3:\"OVO\";i:8;s:4:\"DANA\";i:9;s:7:\"LINKAJA\";i:10;s:4:\"QRIS\";}',1789031178);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_inquiries`
--

DROP TABLE IF EXISTS `contact_inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_inquiries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_inquiries_user_id_foreign` (`user_id`),
  CONSTRAINT `contact_inquiries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_inquiries`
--

LOCK TABLES `contact_inquiries` WRITE;
/*!40000 ALTER TABLE `contact_inquiries` DISABLE KEYS */;
INSERT INTO `contact_inquiries` VALUES (7,8,'Muhammad iIyas','m.ilyasalfadhlih@gmail.com','085156521726','jual-beli-akun','Halo, saya melakukan top up Mobile Legends sebesar Rp50.000 menggunakan QRIS pada pukul 14.35 WIB. Pembayaran sudah berhasil dan saldo sudah terpotong, tetapi diamond belum masuk ke akun saya hingga sekarang. Mohon bantu dicek. Terima kasih.',NULL,1,NULL,'2026-07-27 01:34:23','2026-07-27 01:35:01'),(8,8,'Muhammad iIyas','m.ilyasalfadhlih@gmail.com','085156521726','topup','Halo, saya melakukan top up Mobile Legends sebesar Rp50.000 menggunakan QRIS pada pukul 14.35 WIB. Pembayaran sudah berhasil dan saldo sudah terpotong, tetapi diamond belum masuk ke akun saya hingga sekarang. Mohon bantu dicek. Terima kasih.',NULL,1,NULL,'2026-07-27 01:36:24','2026-07-27 01:45:57'),(9,8,'Muhammad iIyas','m.ilyasalfadhlih@gmail.com','085156521726','keluhan','sdsdsd','Alhamdulillah barokah',1,NULL,'2026-07-27 01:50:52','2026-09-03 03:05:01'),(10,6,'tes','ahmadmusyadadhaury@gmail.com','089507135674','topup','tes','tes',1,NULL,'2026-09-03 02:38:40','2026-09-03 03:13:27');
/*!40000 ALTER TABLE `contact_inquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flash_deals`
--

DROP TABLE IF EXISTS `flash_deals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flash_deals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flash_deals_product_id_foreign` (`product_id`),
  KEY `flash_deals_is_active_starts_at_ends_at_index` (`is_active`,`starts_at`,`ends_at`),
  CONSTRAINT `flash_deals_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_deals`
--

LOCK TABLES `flash_deals` WRITE;
/*!40000 ALTER TABLE `flash_deals` DISABLE KEYS */;
INSERT INTO `flash_deals` VALUES (1,176,NULL,10.00,20,'2026-09-08 12:43:10','2026-09-09 12:43:10',1,'2026-09-08 06:43:10','2026-09-08 06:43:10'),(4,177,NULL,8.00,20,'2026-09-08 15:43:10','2026-09-08 23:43:10',1,'2026-09-08 06:43:10','2026-09-08 06:43:10');
/*!40000 ALTER TABLE `flash_deals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_07_14_000001_create_products_table',1),(5,'2024_07_14_000002_create_orders_table',1),(6,'2024_07_14_000003_create_transactions_table',1),(7,'2026_07_14_064613_add_is_admin_to_users_table',1),(8,'2026_07_15_000001_create_payment_methods_table',1),(9,'2026_07_15_000002_create_brands_table',1),(10,'2026_07_15_000003_add_thumbnail_to_brands_table',1),(11,'2026_07_15_000004_add_is_popular_to_brands_table',1),(12,'2026_07_15_013403_add_carousel_bg_to_brands_table',1),(13,'2026_07_15_014602_add_sort_order_to_brands_table',1),(14,'2026_07_15_015722_add_photo_to_payment_methods_table',1),(15,'2026_07_15_020000_add_region_to_products_table',1),(16,'2026_07_15_035004_create_site_settings_table',1),(17,'2026_07_15_030000_add_username_to_users_table',2),(18,'2026_07_15_040000_add_category_to_payment_methods_table',3),(19,'2026_07_15_050000_add_photo_to_products_table',4),(20,'2026_07_15_075216_add_detail_bg_to_brands_table',5),(21,'2026_07_15_082612_add_detail_bg_position_to_brands_table',6),(22,'2026_07_15_133423_add_quantity_to_orders_table',7),(23,'2026_07_15_142544_add_service_type_to_brands_table',7),(24,'2026_07_16_020443_add_featured_thumbnail_to_brands_table',7),(25,'2026_07_16_023558_add_featured_images_to_brands_table',8),(26,'2026_07_17_014625_add_item_type_to_products_table',9),(27,'2026_07_17_015050_drop_item_type_from_products_table',9),(28,'2026_07_18_000001_create_account_listings_table',9),(29,'2026_07_18_000002_add_original_price_and_owner_to_account_listings',10),(30,'2026_07_18_000003_add_promo_to_account_listings',11),(31,'2026_07_18_000004_add_is_sold_to_account_listings',12),(32,'2026_07_19_000001_create_account_orders_table',13),(33,'2026_07_19_000002_create_otp_codes_table',13),(34,'2026_07_20_000001_add_video_url_to_account_listings',14),(35,'2026_07_21_060000_add_photo_light_to_payment_methods_table',15),(36,'2026_07_21_070000_add_google_id_to_users_table',16),(37,'2026_07_21_080000_create_contact_inquiries_table',17),(38,'2026_07_25_013357_make_user_id_nullable_in_orders_table',18),(39,'2026_07_25_013624_add_email_to_orders_table',19),(40,'2026_07_27_080001_add_user_id_to_contact_inquiries_table',20),(41,'2026_07_27_084206_add_responded_at_to_contact_inquiries_table',21),(42,'2026_08_21_000001_add_gateway_columns_to_orders_table',22),(43,'2026_08_21_000002_add_zone_id_to_orders_table',23),(44,'2026_08_21_000003_add_requires_zone_id_to_brands_table',23),(45,'2026_09_01_000001_add_qr_fields_to_orders_table',24),(46,'2026_09_01_000002_create_reviews_table',25),(47,'2026_09_01_000003_add_stock_alert_sent_to_products_table',26),(48,'2026_09_03_094359_add_admin_reply_to_contact_inquiries_table',27),(49,'2026_09_03_100000_add_multi_payment_columns_to_orders_table',28),(50,'2026_09_07_000000_create_popup_banners_table',29),(51,'2026_09_07_010000_add_image_fit_to_popup_banners_table',30),(52,'2026_09_08_000000_create_flash_deals_table',31),(53,'2026_09_08_000001_add_flash_deal_id_to_orders_table',31),(54,'2026_09_08_000002_add_image_to_flash_deals_table',32),(55,'2026_09_10_000001_add_gateway_to_account_orders',33);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_invoice_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_invoice_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'invoice',
  `qr_string` text COLLATE utf8mb4_unicode_ci,
  `va_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checkout_url` text COLLATE utf8mb4_unicode_ci,
  `gateway_extra` json DEFAULT NULL,
  `buyer_sku_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `flash_deal_id` bigint unsigned DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `original_price` decimal(12,2) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_id_unique` (`order_id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_flash_deal_id_foreign` (`flash_deal_id`),
  CONSTRAINT `orders_flash_deal_id_foreign` FOREIGN KEY (`flash_deal_id`) REFERENCES `flash_deals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (14,8,'TUP-2SOIGDVF3G',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'ml-275-diamond','121212',NULL,'1212',NULL,1,'275 (250+25) Diamonds','Mobile Legends','Mobile Legends',NULL,70485.00,NULL,'pending',NULL,'2026-07-21 20:44:50','2026-07-21 20:44:50'),(15,8,'TUP-T6PUFXWHDJ',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'ml-28-diamond','3434',NULL,'3434',NULL,1,'28 (25+3) Diamonds','Mobile Legends','Mobile Legends',NULL,8017.00,NULL,'pending',NULL,'2026-07-22 19:52:58','2026-07-22 19:52:58'),(16,8,'TUP-Y18NFTREP1',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'ml-tp','232323',NULL,'2323',NULL,1,'Twilight Pass','Mobile Legends','Mobile Legends',NULL,143261.00,NULL,'success',NULL,'2026-07-24 00:19:51','2026-07-24 18:59:36'),(17,NULL,'TUP-AYVNERVHIR',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'pubg-565-uc(id)','123456',NULL,'1234','tanpaakun@gmail.com',1,'565 (540+25) UC (ID)','PUBG Mobile','Tencent Games',NULL,155959.00,NULL,'success',NULL,'2026-07-24 18:39:32','2026-07-24 19:02:23'),(18,6,'TUP-WH6FBAIUTN',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'pubg-8100-uc','123456',NULL,'1234','ahmad@gmail.com',1,'8100 UC','PUBG Mobile','Tencent Games',NULL,1738057.00,NULL,'success',NULL,'2026-07-24 19:01:27','2026-07-24 19:02:10'),(19,8,'TUP-IKFJGVIAZ9',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'ff-2180-diamond','232323',NULL,'2323','m.ilyasalfadlih@gmail.com',1,'2180 Diamonds','Free Fire','Garena',NULL,287072.00,NULL,'success',NULL,'2026-07-27 09:07:49','2026-07-27 09:08:56'),(20,6,'TUP-GL79RWBRG3',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'pubg-8100-uc','232323',NULL,'2323','ahmadmusyadadhaury@gmail.com',1,'8100 UC','PUBG Mobile','Tencent Games',NULL,1738057.00,NULL,'success',NULL,'2026-07-27 09:36:31','2026-07-27 09:37:00'),(21,8,'TUP-FAX9PM63DI',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'ml-tp','15009302',NULL,'3239','m.ilyasalfadlih@gmail.com',1,'Twilight Pass','Mobile Legends','Mobile Legends',NULL,143261.00,NULL,'pending',NULL,'2026-07-30 01:59:49','2026-09-02 08:50:31'),(24,NULL,'TUP-F0AZCHQDCK','6a88091ec4936687180abb6d','https://checkout-staging.xendit.co/web/6a88091ec4936687180abb6d',NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'ax10','081234567890',NULL,'Test User','test@johengaming.id',1,'Axis 10.000','AXIS','Pulsa',NULL,11473.00,NULL,'failed','IP Anda tidak kami kenali: 114.10.45.170','2026-08-21 08:15:25','2026-08-21 08:21:34'),(29,6,'TUP-TIRRRKRRTJ','6a962c5743bc9c4d4ed912af','https://checkout-staging.xendit.co/web/6a962c5743bc9c4d4ed912af',NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','Haury','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'pending',NULL,'2026-09-01 01:37:26','2026-09-02 08:50:31'),(30,6,'TUP-T60X2HOPNN','6a962c87d9fcab275e8ea1d2','https://checkout-staging.xendit.co/web/6a962c87d9fcab275e8ea1d2',NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','Haury','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'pending',NULL,'2026-09-01 01:38:15','2026-09-02 08:50:31'),(31,6,'TUP-DE2RSENTYY','qr_0121159c-2790-41ea-ad56-371d335ffc7b',NULL,NULL,'qris','some-random-qr-string',NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','Haury','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'pending',NULL,'2026-09-01 01:54:13','2026-09-02 08:50:31'),(33,6,'TUP-1FSCZEDSHA',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIM71313a2a2982','2026-09-01 02:29:56','2026-09-02 08:50:31'),(34,6,'TUP-E8LXPO6PKX',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIMa64f3f36d15c','2026-09-01 02:32:10','2026-09-02 08:50:31'),(49,6,'TUP-XH9LNLBBG4',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIMf560b3006894','2026-09-01 02:58:09','2026-09-02 08:50:31'),(50,6,'TUP-EGZH24OOUP',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIM2a3afd430ff9','2026-09-01 03:29:57','2026-09-02 08:50:31'),(51,6,'TUP-92QEZUXLO1',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIM5c20275999c5','2026-09-01 03:35:23','2026-09-02 08:50:31'),(52,6,'TUP-RUQLYQTQSD',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIM3decbfb4a843','2026-09-01 03:36:01','2026-09-02 08:50:31'),(53,6,'TUP-1IZOVGJAZZ',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIM214fb5e248fe','2026-09-01 03:36:47','2026-09-02 08:50:31'),(54,6,'TUP-1IKQAXAZRU',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-PUBG2323','513672231',NULL,'PlayerSim8999','ahmadmusyadadhaury@gmail.com',1,'UC','PUBG Mobile','fps',NULL,2000.00,NULL,'success','SIM37f5ae4a6c5b','2026-09-01 03:58:01','2026-09-02 08:50:31'),(55,6,'TUP-SUXZMHIFKP',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-PUBG2323','513672231',NULL,'PlayerSim8999','ahmadmusyadadhaury@gmail.com',1,'UC','PUBG Mobile','fps',NULL,2000.00,NULL,'pending',NULL,'2026-09-01 03:58:31','2026-09-02 08:50:31'),(56,6,'TUP-ZP0XDRGIJG',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-PUBG2323','513672231',NULL,'PlayerSim8999','ahmadmusyadadhaury@gmail.com',1,'UC','PUBG Mobile','fps',NULL,2000.00,NULL,'success','SIM2062c137ead3','2026-09-01 04:01:35','2026-09-02 08:50:31'),(58,6,'TUP-HFC44MAPZH',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-PUBG2323','513672231',NULL,'PlayerSim8999','ahmadmusyadadhaury@gmail.com',1,'UC','PUBG Mobile','fps',NULL,2000.00,NULL,'success','SIMc6d01978e688','2026-09-01 04:07:38','2026-09-02 08:50:31'),(60,6,'TUP-8ODMSSHU5G',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIM402c217f25db','2026-09-02 08:43:35','2026-09-02 08:50:31'),(61,6,'TUP-HGJUYFTVPT',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIMb783cbeccb64','2026-09-02 09:13:26','2026-09-03 02:06:58'),(62,6,'TUP-ENP1CI02D5',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIM669ad1e7ea42','2026-09-03 01:14:14','2026-09-03 02:06:58'),(63,6,'TUP-SEDFHXHUYP',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-MLBB01','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'MOBILELEGEND - 3 Diamond','MOBILE LEGENDS','Games',NULL,1943.00,NULL,'success','SIMe863ce2f73b8','2026-09-03 01:15:40','2026-09-03 02:06:58'),(64,NULL,'TUP-4LZ9XSW44F',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-MLBB01','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'MOBILELEGEND - 3 Diamond','MOBILE LEGENDS','Games',NULL,1943.00,NULL,'success','SIM4d6252f9875c','2026-09-03 07:53:17','2026-09-03 07:53:22'),(65,NULL,'TUP-MZGCKZ0TPA',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-MLBB01','1454641242','16479','PlayerSim4758','johengamingmarketplace@gmail.com',1,'MOBILELEGEND - 3 Diamond','MOBILE LEGENDS','Games',NULL,1943.00,NULL,'success','SIM831033e3603a','2026-09-03 07:58:39','2026-09-03 07:58:43'),(66,NULL,'TUP-BCZTWJW2ZU',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','johengamingmarketplace@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIMedb9e16a6bc5','2026-09-04 02:27:12','2026-09-04 02:27:16'),(67,NULL,'TUP-4ZLEHBCMGK',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIMecf22b24cd18','2026-09-04 02:33:53','2026-09-04 02:34:00'),(68,NULL,'TUP-EBIJQB1XR5',NULL,NULL,NULL,'invoice',NULL,NULL,NULL,NULL,NULL,'JG-ML232','1454641242','16479','PlayerSim4758','ahmadmusyadadhaury@gmail.com',1,'diamond1','MOBILE LEGENDS','moba',NULL,11000.00,NULL,'success','SIMaa5d99bcc4e2','2026-09-04 02:54:59','2026-09-04 02:55:03');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'register',
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_codes`
--

LOCK TABLES `otp_codes` WRITE;
/*!40000 ALTER TABLE `otp_codes` DISABLE KEYS */;
INSERT INTO `otp_codes` VALUES (1,'m.ilyasalfadlih@gmail.com','626727','register','2026-07-19 18:12:51','2026-07-19 18:08:14','2026-07-19 18:07:51','2026-07-19 18:08:14'),(2,'m.ilyasalfadlih@gmail.com','794605','register','2026-07-19 18:12:52','2026-07-19 18:08:14','2026-07-19 18:07:52','2026-07-19 18:08:14'),(3,'m.ilyasalfadlih@gmail.com','206744','register','2026-07-19 18:13:14','2026-07-19 18:14:51','2026-07-19 18:08:14','2026-07-19 18:14:51'),(4,'m.ilyasalfadlih@gmail.com','868716','register','2026-07-19 18:15:00','2026-07-19 18:14:51','2026-07-19 18:10:00','2026-07-19 18:14:51'),(5,'m.ilyasalfadlih@gmail.com','745720','register','2026-07-19 18:17:33','2026-07-19 18:14:51','2026-07-19 18:12:33','2026-07-19 18:14:51'),(6,'m.ilyasalfadlih@gmail.com','090969','register','2026-07-19 18:19:51','2026-07-19 18:15:07','2026-07-19 18:14:51','2026-07-19 18:15:07'),(7,'ahmadmusyadadhaury@gmail.com','336775','register','2026-07-19 20:14:08','2026-07-19 20:09:50','2026-07-19 20:09:08','2026-07-19 20:09:50'),(8,'m.ilyasalfadlih@gmail.com','894181','register','2026-07-20 00:41:37','2026-07-20 00:36:59','2026-07-20 00:36:37','2026-07-20 00:36:59'),(9,'m.ilyasalfadlih@gmail.com','070438','register','2026-07-21 19:10:18',NULL,'2026-07-21 19:05:18','2026-07-21 19:05:18'),(10,'m.ilyasalfadlih@gmail.com','815135','register','2026-07-21 19:10:23',NULL,'2026-07-21 19:05:23','2026-07-21 19:05:23'),(11,'m.ilyasalfadlih@gmail.com','034234','register','2026-07-21 19:11:36','2026-07-21 19:06:57','2026-07-21 19:06:36','2026-07-21 19:06:57'),(12,'admintkalmadinah@gmail.com','717978','register','2026-08-14 03:19:22',NULL,'2026-08-14 03:14:22','2026-08-14 03:14:22');
/*!40000 ALTER TABLE `otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ewallet',
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_light` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_methods_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,'GoPay','gopay',NULL,'ewallet','payments/gopay.svg','payments/gopay-dark.webp',0,'2026-07-15 00:09:13','2026-09-07 04:32:53'),(2,'Dana','dana',NULL,'ewallet','payments/dana.svg',NULL,1,'2026-07-15 00:09:36','2026-08-12 08:35:28'),(3,'ShopeePay','shopeepay',NULL,'ewallet','payments/shopeepay.svg',NULL,0,'2026-07-15 00:10:01','2026-09-04 02:52:04'),(4,'Ovo','ovo',NULL,'ewallet','payments/ovo.svg',NULL,1,'2026-07-15 00:10:31','2026-08-12 08:35:28'),(5,'QRIS','qris',NULL,'qris','payments/qris.svg','payments/qris-dark.webp',1,'2026-07-15 00:21:58','2026-09-07 04:32:53'),(6,'BCA Virtual Account','bca_va',NULL,'va','payments/bca.svg',NULL,1,'2026-07-15 00:21:58','2026-08-12 08:35:28'),(7,'BNI Virtual Account','bni_va',NULL,'va','payments/bni.svg',NULL,1,'2026-07-15 00:21:58','2026-08-12 08:35:28'),(8,'Mandiri Virtual Account','mandiri_va',NULL,'va','payments/mandiri.svg',NULL,1,'2026-07-15 00:21:58','2026-08-12 08:35:28'),(9,'Indomaret','indomaret',NULL,'convenience_store','payments/indomaret.svg',NULL,1,'2026-07-15 00:21:58','2026-08-12 08:35:28'),(10,'Alfamart','alfamart',NULL,'convenience_store','payments/alfamart.svg',NULL,1,'2026-07-15 00:21:58','2026-08-12 08:35:28'),(12,'BRI Virtual Account','bri_va',NULL,'va','payments/bri.svg',NULL,1,'2026-09-04 02:44:53','2026-09-04 02:44:53'),(13,'Permata Virtual Account','permata_va',NULL,'va',NULL,NULL,1,'2026-09-04 02:44:53','2026-09-04 02:44:53'),(14,'LinkAja','linkaja',NULL,'ewallet',NULL,NULL,1,'2026-09-04 02:52:04','2026-09-04 02:52:04');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popup_banners`
--

DROP TABLE IF EXISTS `popup_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `popup_banners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_fit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contain',
  `image_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'center',
  `orientation` enum('landscape','portrait') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'portrait',
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popup_banners`
--

LOCK TABLES `popup_banners` WRITE;
/*!40000 ALTER TABLE `popup_banners` DISABLE KEYS */;
INSERT INTO `popup_banners` VALUES (2,'promo','Spesial disc. September','popup-banners/yWf3nWtWjGr4lD5Fv9DJq9sOZa9Y7wdnsPCEVdYw.webp','cover','center','portrait','https://johengaming.id/produk/jual-beli-akun',1,1,'2026-09-07 10:26:00','2026-09-08 10:26:00','2026-09-07 03:26:17','2026-09-07 04:26:00');
/*!40000 ALTER TABLE `popup_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `buyer_sku_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `selling_price` decimal(12,2) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `stock` int NOT NULL DEFAULT '0',
  `stock_alert_sent` tinyint(1) NOT NULL DEFAULT '0',
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_buyer_sku_code_unique` (`buyer_sku_code`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (119,'flash1','TELKOMSEL','Data','Telkomsel Data Flash 1 GB 30 Hari',NULL,12110.00,12716.00,'Flash',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(120,'flash2','TELKOMSEL','Data','Telkomsel Data Flash 2 GB 30 Hari',NULL,21425.00,22496.00,'Flash',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(121,'flash3','TELKOMSEL','Data','Telkomsel Data Flash 3 GB 30 Hari',NULL,28525.00,29951.00,'Flash',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(122,'flexs','XL','Data','XL Xtra Combo Flex S 28 Hari',NULL,32510.00,34136.00,'Xtra Combo Flex',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(123,'happy1','TRI','Data','Tri Data Happy 1.5 GB 1 Hari',NULL,7350.00,7718.00,'Happy',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(124,'happy3','TRI','Data','Tri Data Happy 3 GB 3 Hari',NULL,12269.00,12882.00,'Happy',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(125,'hotrod3g10d','XL','Aktivasi Voucher','Aktivasi Voucher XL XTRA HotRod Special 3 GB 10 Hari',NULL,18010.00,18911.00,'Hotrod Special',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(132,'iactive90','INDOSAT','Masa Aktif','Indosat Tambah Masa Aktif Kartu 90 Hari',NULL,32410.00,34031.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(135,'if3g3d','INDOSAT','Data','Indosat Freedom Internet 3 GB 3 Hari',NULL,12225.00,12836.00,'Freedom Internet',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(136,'if5g30d','INDOSAT','Data','Indosat Freedom Internet 5.5 GB 28 Hari',NULL,34625.00,36356.00,'Freedom Internet',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(138,'kvision30d','K-VISION dan GOL','TV','K-Vision & GOL Paket CLING (CL01)  30 Hari',NULL,19735.00,20722.00,'Cling',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(139,'pas10','TELKOMSEL','Paket SMS & Telpon','Telkomsel Telepon Pas 10.000',NULL,10910.00,11456.00,'Telepon Pas',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(140,'pas20','TELKOMSEL','Paket SMS & Telpon','Telkomsel Telepon Pas 20.000',NULL,21325.00,22391.00,'Telepon Pas',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(141,'pas50','TELKOMSEL','Paket SMS & Telpon','Telkomsel Telepon Pas 50.000',NULL,30025.00,31526.00,'Telepon Pas',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(142,'pertagas20','Pertamina Gas','Gas','Pertagas 20.000',NULL,21500.00,22575.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(143,'pln100','PLN','PLN','PLN 100.000',NULL,101525.00,106601.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(144,'pln1000','PLN','PLN','PLN 1.000.000',NULL,1001783.00,1051872.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(145,'pln20','PLN','PLN','PLN 20.000',NULL,21790.00,22880.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(146,'pln50','PLN','PLN','PLN 50.000',NULL,52095.00,54700.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(147,'s10','TELKOMSEL','Pulsa','Telkomsel 10.000',NULL,10155.00,10663.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(148,'s100','TELKOMSEL','Pulsa','Telkomsel 100.000',NULL,98850.00,103793.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(150,'s20','TELKOMSEL','Pulsa','Telkomsel 20.000',NULL,19825.00,20816.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(151,'s25','TELKOMSEL','Pulsa','Telkomsel 25.000',NULL,24802.00,26042.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(152,'s30','TELKOMSEL','Pulsa','Telkomsel 30.000',NULL,29545.00,31022.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(153,'s5','TELKOMSEL','Pulsa','Telkomsel 5.000',NULL,5193.00,5453.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(154,'s50','TELKOMSEL','Pulsa','Telkomsel 50.000',NULL,49475.00,51949.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(155,'sm10','SMARTFREN','Pulsa','Smartfren 10.000',NULL,10085.00,10589.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(156,'smdu1','SMARTFREN','Data','Smartfren Data Unlimited Harian 1 GB Berlaku 7 Hari',NULL,17410.00,18281.00,'Unlimited',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(157,'smdu2','SMARTFREN','Data','Smartfren Data Unlimited Harian 2 GB Berlaku 28 Hari',NULL,86475.00,90799.00,'Unlimited',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(158,'t10','TRI','Pulsa','Three 10.000',NULL,11485.00,12059.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(159,'t20','TRI','Pulsa','Three 20.000',NULL,19701.00,20686.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(160,'t5','TRI','Pulsa','Three 5.000',NULL,5155.00,5413.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(161,'tacthappys','TRI','Aktivasi Perdana','Aktivasi Perdana Tri Happy S+ 30 Hari',NULL,20500.00,21525.00,'Happy',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(162,'tactive4m','TRI','Masa Aktif','Tri Tambah Masa Aktif Kartu  4 Bulan',NULL,10000.00,10500.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(165,'vflexs','XL','Aktivasi Voucher','Aktivasi Voucher XL Xtra Combo Flex S 28 Hari',NULL,32050.00,33653.00,'Xtra Combo Flex',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(166,'vs2g5d','TELKOMSEL','Voucher','Voucher Telkomsel 2.5 GB 5 Hari (Jawa Barat)',NULL,13150.00,13808.00,'Jawa Barat',1,0,0,NULL,'2026-08-17 08:28:00','2026-08-18 01:49:57'),(167,'x10','XL','Pulsa','Xl 10.000',NULL,11053.00,11606.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(168,'x5','XL','Pulsa','Xl 5.000',NULL,5865.00,6158.00,'Umum',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:06'),(169,'yellow1','INDOSAT','Data','Indosat Yellow 1 GB 1 Hari',NULL,5755.00,6043.00,'Yellow',1,0,0,NULL,'2026-08-17 08:28:00','2026-09-01 04:26:07'),(172,'JG-MLBB01','MOBILE LEGENDS','Games','MOBILELEGEND - 3 Diamond','products/pPeIFT9bHUk7f2U5k7pU5rIya5djD1oGhH8C148u.webp',1850.00,1943.00,'instant',1,0,1,NULL,'2026-08-18 01:49:57','2026-09-03 07:58:43'),(173,'MOBILE LEGENED','Moontoon','moba','Skin Legend Fanny',NULL,100000.00,200000.00,'instant',1,2,0,NULL,'2026-08-31 09:52:01','2026-08-31 09:52:01'),(175,'JG-ML2323','Moontoon','moba','diamond1','products/XRe7cZJRiW2SYWkRBh7nfcKRGRPAe0ZMimi7nRtB.webp',10000.00,11000.00,'instant',1,2,0,NULL,'2026-09-01 01:28:32','2026-09-01 01:28:32'),(176,'JG-ML232','MOBILE LEGENDS','moba','diamond1','products/c3cajOSqTcpokxUDWtwmsifWKoHgVdGgjKwzXn2P.webp',10000.00,11000.00,'instant',1,5,1,NULL,'2026-09-01 01:35:47','2026-09-04 12:47:27'),(177,'JG-PUBG2323','PUBG Mobile','fps','UC','products/U3baIo4Oo2SQXvxXasgIyht2R8SNMS7Ensq4vPel.webp',1000.00,2000.00,'instant',1,1,0,NULL,'2026-09-01 03:53:33','2026-09-01 03:53:33'),(178,'ax10','AXIS','Pulsa','Axis 10.000',NULL,10927.00,11473.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(179,'axdj1','AXIS','Data','Axis Data Jawa 2.5 GB 5 Hari',NULL,13660.00,14343.00,'Jawa Bali Nusra',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(180,'axdss2','AXIS','Data','Axis Data SS 2 GB 3 Hari',NULL,10631.00,11163.00,'Aigo SS',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(181,'byu10','by.U','Pulsa','by.U 10.000',NULL,10465.00,10988.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(182,'i10','INDOSAT','Pulsa','Indosat 10.000',NULL,11930.00,12527.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(183,'i20','INDOSAT','Pulsa','Indosat 20.000',NULL,20710.00,21746.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(184,'i25','INDOSAT','Pulsa','Indosat 25.000',NULL,25245.00,26507.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(185,'i30','INDOSAT','Pulsa','Indosat 30.000',NULL,30575.00,32104.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(186,'i5','INDOSAT','Pulsa','Indosat 5.000',NULL,6620.00,6951.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(187,'i50','INDOSAT','Pulsa','Indosat 50.000',NULL,48895.00,51340.00,'Umum',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(188,'if2','INDOSAT','Data','Indosat Freedom Internet 2.5 GB 5 Hari',NULL,13800.00,14490.00,'Freedom Internet',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06'),(189,'if3g30d','INDOSAT','Data','Indosat Freedom Internet 3 GB 28 Hari',NULL,29560.00,31038.00,'Freedom Internet',1,0,0,NULL,'2026-09-01 04:26:06','2026-09-01 04:26:06');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `game` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_order_id_index` (`order_id`),
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (4,NULL,'TUP-EGZH24OOUP','ahmadmusyadadhaury@gmail.com','MOBILE LEGENDS',5,'tes','approved','2026-09-01 03:30:11','2026-09-01 03:30:11'),(5,NULL,'TUP-92QEZUXLO1','ahmadmusyadadhaury@gmail.com','MOBILE LEGENDS',5,'tes','approved','2026-09-01 03:35:36','2026-09-01 03:35:36'),(6,NULL,'TUP-1IZOVGJAZZ','ahmadmusyadadhaury@gmail.com','MOBILE LEGENDS',5,'tes','approved','2026-09-01 03:37:00','2026-09-01 03:37:00'),(7,NULL,'TUP-1IKQAXAZRU','ahmadmusyadadhaury@gmail.com','PUBG Mobile',5,'tes','approved','2026-09-01 03:58:15','2026-09-01 03:58:15'),(8,NULL,'TUP-ZP0XDRGIJG','ahmadmusyadadhaury@gmail.com','PUBG Mobile',5,'tes','approved','2026-09-01 04:01:45','2026-09-01 04:01:45'),(9,NULL,'TUP-HFC44MAPZH','ahmadmusyadadhaury@gmail.com','PUBG Mobile',5,NULL,'approved','2026-09-01 04:07:50','2026-09-01 04:07:50'),(10,NULL,'TUP-8ODMSSHU5G','ahmadmusyadadhaury@gmail.com','MOBILE LEGENDS',5,'tes','approved','2026-09-02 08:43:54','2026-09-02 08:43:54'),(11,NULL,'TUP-ENP1CI02D5','ahmadmusyadadhaury@gmail.com','MOBILE LEGENDS',5,'tes','approved','2026-09-03 01:14:30','2026-09-03 01:14:30'),(12,NULL,'TUP-MZGCKZ0TPA','johengamingmarketplace@gmail.com','MOBILE LEGENDS',5,NULL,'approved','2026-09-03 08:01:37','2026-09-03 08:01:37'),(13,NULL,'TUP-BCZTWJW2ZU','johengamingmarketplace@gmail.com','MOBILE LEGENDS',5,'tes','approved','2026-09-04 02:27:23','2026-09-04 02:27:23'),(14,NULL,'TUP-7M41MG2YUI','ahmadmusyadadhaury@gmail.com','MOBILE LEGENDS',5,'tes','approved','2026-09-04 12:47:35','2026-09-04 12:47:35');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('BzRZcqkHNTULb59dS0W9sKe00xLR8i6iP5olJXqQ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJIbWczVXZMbFdROEZzVkdobk51ZzM1MlRvWENPRWJ6alg4WXZNdEZ3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9qdWFsLWJlbGktYWt1blwvb3JkZXJcLzE0XC9zdGF0dXMiLCJyb3V0ZSI6Imp1YWwtYmVsaS1ha3VuLnBheW1lbnQuc3RhdHVzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789034173),('MxHXg9NjBEbwoIVaFD3DEVGUiBs5Y3BiKnE1bzRV',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJrME5GVWtFWnV5MkY1NGpmdnlCU0tMSXRMd3dIdEhTb3BycDgzZHFFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvZGFzaGJvYXJkIiwicm91dGUiOiJhZG1pbi5kYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl9hZG1pbl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyfQ==',1789031293);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_name','Johen Gaming','text','2026-07-15 18:53:14','2026-07-15 18:53:14'),(2,'site_tagline',NULL,'text','2026-07-15 18:53:14','2026-07-15 18:53:14'),(3,'site_description',NULL,'text','2026-07-15 18:53:14','2026-07-15 18:53:14'),(4,'contact_email','corporate@johengaming.id','text','2026-07-15 18:53:14','2026-09-03 02:38:07'),(5,'contact_whatsapp','082260707012','text','2026-07-15 18:53:14','2026-09-03 02:38:07'),(6,'contact_instagram','@johengaming.id','text','2026-07-15 18:53:14','2026-07-15 19:00:23'),(7,'footer_text',NULL,'text','2026-07-15 18:53:14','2026-07-15 18:53:14'),(8,'site_hero_banner','settings/hero-banner-1.webp','image','2026-07-15 18:53:14','2026-08-12 08:34:28'),(9,'site_hero_banner_2','settings/hero-banner-2.webp','image','2026-07-19 18:23:35','2026-08-12 08:34:29'),(10,'site_hero_banner_3','settings/hero-banner-3.webp','image','2026-07-19 18:23:35','2026-08-12 08:34:29'),(11,'site_logo','settings/logo.png','image','2026-07-19 19:39:33','2026-08-12 08:34:29'),(12,'jba_hero_banner','settings/jba-banner-1.png','image','2026-07-21 00:30:40','2026-08-12 08:34:29'),(13,'jba_hero_banner_2','settings/jba-banner-2.png','image','2026-07-21 00:37:26','2026-08-12 08:34:29'),(14,'jba_hero_banner_3','settings/jba-banner-3.png','image','2026-08-12 08:34:29','2026-08-12 08:34:29'),(15,'digiflazz_last_sync','2026-09-01 11:26:07','text','2026-08-13 08:48:25','2026-09-01 04:26:07'),(16,'digiflazz_product_count','31','text','2026-08-13 08:48:25','2026-09-01 04:26:07'),(17,'digiflazz_username','teyumoor1K9g','text','2026-09-03 02:38:07','2026-09-03 02:38:07'),(18,'digiflazz_key','dev-5e823820-96f2-11f1-8596-25a9cbaec972','text','2026-09-03 02:38:07','2026-09-03 02:38:07'),(19,'digiflazz_production','1','text','2026-09-03 02:38:07','2026-09-03 02:38:07'),(20,'qris_image','settings/p6VVwu9j5wrXr58vQvbqqVUQTWtw0g3W1CkWCYnW.webp','image','2026-09-10 08:55:33','2026-09-10 08:55:33');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gross_amount` decimal(12,2) NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fraud_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_response` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_transaction_id_unique` (`transaction_id`),
  KEY `transactions_order_id_foreign` (`order_id`),
  CONSTRAINT `transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (14,14,NULL,NULL,70485.00,'pending',NULL,NULL,'2026-07-21 20:44:50','2026-07-21 20:44:50'),(15,15,NULL,NULL,8017.00,'pending',NULL,NULL,'2026-07-22 19:52:58','2026-07-22 19:52:58'),(16,16,NULL,NULL,143261.00,'pending',NULL,NULL,'2026-07-24 00:19:51','2026-07-24 00:19:51'),(17,17,NULL,NULL,155959.00,'pending',NULL,NULL,'2026-07-24 18:39:32','2026-07-24 18:39:32'),(18,18,NULL,NULL,1738057.00,'pending',NULL,NULL,'2026-07-24 19:01:27','2026-07-24 19:01:27'),(19,19,NULL,NULL,287072.00,'pending',NULL,NULL,'2026-07-27 09:07:49','2026-07-27 09:07:49'),(20,20,NULL,NULL,1738057.00,'pending',NULL,NULL,'2026-07-27 09:36:31','2026-07-27 09:36:31'),(21,21,NULL,NULL,143261.00,'pending',NULL,NULL,'2026-07-30 01:59:49','2026-07-30 01:59:49'),(24,24,'6a88091ec4936687180abb6d','QR_CODE - QRIS',11473.00,'failed',NULL,'{\"id\": \"6a88091ec4936687180abb6d\", \"items\": [{\"name\": \"Axis 10.000\", \"price\": 11473, \"category\": \"Pulsa\", \"quantity\": 1}], \"amount\": 11473, \"status\": \"PAID\", \"created\": \"2026-08-21T08:15:26.331Z\", \"paid_at\": \"2026-08-21T08:21:17.692Z\", \"updated\": \"2026-08-21T08:21:19.310Z\", \"user_id\": \"6a850de912583281ce91823a\", \"currency\": \"IDR\", \"customer\": {\"email\": \"test@johengaming.id\", \"given_names\": \"Test User\"}, \"metadata\": null, \"payment_id\": \"qrpy_beb14af5-139e-48cc-9849-108f5b8950b8\", \"description\": \"Axis 10.000 - 081234567890\", \"expiry_date\": \"2026-08-22T08:15:26.238Z\", \"external_id\": \"TUP-F0AZCHQDCK\", \"invoice_url\": \"https://checkout-staging.xendit.co/web/6a88091ec4936687180abb6d\", \"paid_amount\": 11473, \"payer_email\": \"test@johengaming.id\", \"merchant_name\": \"Johen Gaming\", \"payment_method\": \"QR_CODE\", \"available_banks\": [{\"bank_code\": \"MUAMALAT\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"SAHABAT_SAMPOERNA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BRI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BNC\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BJB\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BNI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"MANDIRI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BCA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"CIMB\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"PERMATA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BSI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11473, \"account_holder_name\": \"JOHEN GAMING\"}], \"payment_channel\": \"QRIS\", \"payment_method_id\": \"pm-8e2b3500-593d-477d-a1cc-54a51f95a4b5\", \"should_send_email\": false, \"available_ewallets\": [{\"ewallet_type\": \"OVO\"}, {\"ewallet_type\": \"SHOPEEPAY\"}, {\"ewallet_type\": \"NEXCASH\"}, {\"ewallet_type\": \"DANA\"}, {\"ewallet_type\": \"ASTRAPAY\"}, {\"ewallet_type\": \"LINKAJA\"}, {\"ewallet_type\": \"JENIUSPAY\"}, {\"ewallet_type\": \"GOPAY\"}], \"available_qr_codes\": [{\"qr_code_type\": \"QRIS\"}], \"available_paylaters\": [{\"paylater_type\": \"KREDIVO\"}, {\"paylater_type\": \"AKULAKU\"}, {\"paylater_type\": \"ATOME\"}], \"failure_redirect_url\": \"http://127.0.0.1:8099/payment/detail/24\", \"success_redirect_url\": \"http://127.0.0.1:8099/payment/detail/24\", \"available_direct_debits\": [{\"direct_debit_type\": \"DD_BRI\"}, {\"direct_debit_type\": \"DD_MANDIRI\"}], \"available_retail_outlets\": [{\"retail_outlet_name\": \"INDOMARET\"}, {\"retail_outlet_name\": \"ALFAMART\"}], \"should_exclude_credit_card\": false, \"merchant_profile_picture_url\": \"https://du8nwjtfkinx.cloudfront.net/xendit.png\"}','2026-08-21 08:15:26','2026-08-21 08:21:34'),(27,29,'6a962c5743bc9c4d4ed912af','unknown',11000.00,'pending',NULL,'{\"id\": \"6a962c5743bc9c4d4ed912af\", \"items\": [{\"name\": \"diamond1\", \"price\": 11000, \"category\": \"moba\", \"quantity\": 1}], \"amount\": 11000, \"status\": \"PENDING\", \"created\": \"2026-09-01T01:37:27.504Z\", \"updated\": \"2026-09-01T01:37:27.504Z\", \"user_id\": \"6a850de912583281ce91823a\", \"currency\": \"IDR\", \"customer\": {\"email\": \"ahmadmusyadadhaury@gmail.com\", \"given_names\": \"Haury\"}, \"metadata\": null, \"description\": \"diamond1 - 1454641242\", \"expiry_date\": \"2026-09-02T01:37:27.465Z\", \"external_id\": \"TUP-TIRRRKRRTJ\", \"invoice_url\": \"https://checkout-staging.xendit.co/web/6a962c5743bc9c4d4ed912af\", \"payer_email\": \"ahmadmusyadadhaury@gmail.com\", \"merchant_name\": \"Johen Gaming\", \"available_banks\": [{\"bank_code\": \"BSI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BRI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BJB\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"SAHABAT_SAMPOERNA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"MUAMALAT\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BCA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"PERMATA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"CIMB\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"MANDIRI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BNI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BNC\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}], \"should_send_email\": false, \"available_ewallets\": [{\"ewallet_type\": \"OVO\"}, {\"ewallet_type\": \"SHOPEEPAY\"}, {\"ewallet_type\": \"NEXCASH\"}, {\"ewallet_type\": \"DANA\"}, {\"ewallet_type\": \"ASTRAPAY\"}, {\"ewallet_type\": \"LINKAJA\"}, {\"ewallet_type\": \"JENIUSPAY\"}, {\"ewallet_type\": \"GOPAY\"}], \"available_qr_codes\": [{\"qr_code_type\": \"QRIS\"}], \"available_paylaters\": [{\"paylater_type\": \"KREDIVO\"}, {\"paylater_type\": \"AKULAKU\"}, {\"paylater_type\": \"ATOME\"}], \"failure_redirect_url\": \"http://127.0.0.1:8000/payment/detail/29\", \"success_redirect_url\": \"http://127.0.0.1:8000/payment/detail/29\", \"available_direct_debits\": [{\"direct_debit_type\": \"DD_BRI\"}, {\"direct_debit_type\": \"DD_MANDIRI\"}], \"available_retail_outlets\": [{\"retail_outlet_name\": \"INDOMARET\"}, {\"retail_outlet_name\": \"ALFAMART\"}], \"should_exclude_credit_card\": false, \"merchant_profile_picture_url\": \"https://du8nwjtfkinx.cloudfront.net/xendit.png\"}','2026-09-01 01:37:27','2026-09-01 01:37:29'),(28,30,'6a962c87d9fcab275e8ea1d2','unknown',11000.00,'pending',NULL,'{\"id\": \"6a962c87d9fcab275e8ea1d2\", \"items\": [{\"name\": \"diamond1\", \"price\": 11000, \"category\": \"moba\", \"quantity\": 1}], \"amount\": 11000, \"status\": \"PENDING\", \"created\": \"2026-09-01T01:38:16.278Z\", \"updated\": \"2026-09-01T01:38:16.278Z\", \"user_id\": \"6a850de912583281ce91823a\", \"currency\": \"IDR\", \"customer\": {\"email\": \"ahmadmusyadadhaury@gmail.com\", \"given_names\": \"Haury\"}, \"metadata\": null, \"description\": \"diamond1 - 1454641242\", \"expiry_date\": \"2026-09-02T01:38:16.182Z\", \"external_id\": \"TUP-T60X2HOPNN\", \"invoice_url\": \"https://checkout-staging.xendit.co/web/6a962c87d9fcab275e8ea1d2\", \"payer_email\": \"ahmadmusyadadhaury@gmail.com\", \"merchant_name\": \"Johen Gaming\", \"available_banks\": [{\"bank_code\": \"BSI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BRI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BJB\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"SAHABAT_SAMPOERNA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"MUAMALAT\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BCA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"PERMATA\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"CIMB\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"MANDIRI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BNI\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}, {\"bank_code\": \"BNC\", \"bank_branch\": \"Virtual Account\", \"collection_type\": \"POOL\", \"identity_amount\": 0, \"transfer_amount\": 11000, \"account_holder_name\": \"JOHEN GAMING\"}], \"should_send_email\": false, \"available_ewallets\": [{\"ewallet_type\": \"OVO\"}, {\"ewallet_type\": \"SHOPEEPAY\"}, {\"ewallet_type\": \"NEXCASH\"}, {\"ewallet_type\": \"DANA\"}, {\"ewallet_type\": \"ASTRAPAY\"}, {\"ewallet_type\": \"LINKAJA\"}, {\"ewallet_type\": \"JENIUSPAY\"}, {\"ewallet_type\": \"GOPAY\"}], \"available_qr_codes\": [{\"qr_code_type\": \"QRIS\"}], \"available_paylaters\": [{\"paylater_type\": \"KREDIVO\"}, {\"paylater_type\": \"AKULAKU\"}, {\"paylater_type\": \"ATOME\"}], \"failure_redirect_url\": \"http://127.0.0.1:8000/payment/detail/30\", \"success_redirect_url\": \"http://127.0.0.1:8000/payment/detail/30\", \"available_direct_debits\": [{\"direct_debit_type\": \"DD_BRI\"}, {\"direct_debit_type\": \"DD_MANDIRI\"}], \"available_retail_outlets\": [{\"retail_outlet_name\": \"INDOMARET\"}, {\"retail_outlet_name\": \"ALFAMART\"}], \"should_exclude_credit_card\": false, \"merchant_profile_picture_url\": \"https://du8nwjtfkinx.cloudfront.net/xendit.png\"}','2026-09-01 01:38:16','2026-09-01 01:47:14'),(29,31,'qr_0121159c-2790-41ea-ad56-371d335ffc7b','QRIS - ID_XENDIT',11000.00,'pending',NULL,'{\"id\": \"qr_0121159c-2790-41ea-ad56-371d335ffc7b\", \"type\": \"DYNAMIC\", \"amount\": 11000, \"status\": \"ACTIVE\", \"created\": \"2026-09-01T01:54:14.332867Z\", \"updated\": \"2026-09-01T01:54:14.332867Z\", \"currency\": \"IDR\", \"metadata\": {\"product\": \"diamond1\", \"order_id\": \"TUP-DE2RSENTYY\", \"customer_number\": \"1454641242\"}, \"qr_string\": \"some-random-qr-string\", \"expires_at\": \"2026-09-02T01:54:13Z\", \"business_id\": \"6a850de912583281ce91823a\", \"description\": \"diamond1 - 1454641242\", \"channel_code\": \"ID_XENDIT\", \"reference_id\": \"TUP-DE2RSENTYY\"}','2026-09-01 01:54:14','2026-09-01 02:05:41'),(31,33,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM71313a2a2982\", \"price\": 0, \"ref_id\": \"TUP-1FSCZEDSHA\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-01 02:29:56','2026-09-01 02:30:00'),(32,34,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMa64f3f36d15c\", \"price\": 0, \"ref_id\": \"TUP-E8LXPO6PKX\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-01 02:32:10','2026-09-01 02:32:17'),(43,49,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMf560b3006894\", \"price\": 0, \"ref_id\": \"TUP-XH9LNLBBG4\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-01 02:58:09','2026-09-01 02:58:20'),(44,50,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM2a3afd430ff9\", \"price\": 0, \"ref_id\": \"TUP-EGZH24OOUP\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-01 03:29:57','2026-09-01 03:30:04'),(45,51,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM5c20275999c5\", \"price\": 0, \"ref_id\": \"TUP-92QEZUXLO1\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-01 03:35:23','2026-09-01 03:35:30'),(46,52,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM3decbfb4a843\", \"price\": 0, \"ref_id\": \"TUP-RUQLYQTQSD\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-01 03:36:01','2026-09-01 03:36:05'),(47,53,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM214fb5e248fe\", \"price\": 0, \"ref_id\": \"TUP-1IZOVGJAZZ\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-01 03:36:47','2026-09-01 03:36:54'),(48,54,NULL,'Simulasi (QRIS)',2000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM37f5ae4a6c5b\", \"price\": 0, \"ref_id\": \"TUP-1IKQAXAZRU\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"513672231\", \"buyer_sku_code\": \"JG-PUBG2323\"}}','2026-09-01 03:58:01','2026-09-01 03:58:08'),(49,55,NULL,NULL,2000.00,'pending',NULL,NULL,'2026-09-01 03:58:31','2026-09-01 03:58:31'),(50,56,NULL,'Simulasi (QRIS)',2000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM2062c137ead3\", \"price\": 0, \"ref_id\": \"TUP-ZP0XDRGIJG\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"513672231\", \"buyer_sku_code\": \"JG-PUBG2323\"}}','2026-09-01 04:01:35','2026-09-01 04:01:39'),(52,58,NULL,'Simulasi (QRIS)',2000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMc6d01978e688\", \"price\": 0, \"ref_id\": \"TUP-HFC44MAPZH\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"513672231\", \"buyer_sku_code\": \"JG-PUBG2323\"}}','2026-09-01 04:07:38','2026-09-01 04:07:45'),(53,60,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM402c217f25db\", \"price\": 0, \"ref_id\": \"TUP-8ODMSSHU5G\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-02 08:43:35','2026-09-02 08:43:43'),(54,61,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMb783cbeccb64\", \"price\": 0, \"ref_id\": \"TUP-HGJUYFTVPT\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-02 09:13:26','2026-09-02 09:13:30'),(55,62,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM669ad1e7ea42\", \"price\": 0, \"ref_id\": \"TUP-ENP1CI02D5\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-03 01:14:14','2026-09-03 01:14:19'),(56,63,NULL,'Simulasi (QRIS)',1943.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMe863ce2f73b8\", \"price\": 0, \"ref_id\": \"TUP-SEDFHXHUYP\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-MLBB01\"}}','2026-09-03 01:15:40','2026-09-03 01:15:47'),(57,64,NULL,'Simulasi (QRIS)',1943.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM4d6252f9875c\", \"price\": 0, \"ref_id\": \"TUP-4LZ9XSW44F\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-MLBB01\"}}','2026-09-03 07:53:17','2026-09-03 07:53:22'),(58,65,NULL,'Simulasi (QRIS)',1943.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIM831033e3603a\", \"price\": 0, \"ref_id\": \"TUP-MZGCKZ0TPA\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-MLBB01\"}}','2026-09-03 07:58:39','2026-09-03 07:58:43'),(59,66,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMedb9e16a6bc5\", \"price\": 0, \"ref_id\": \"TUP-BCZTWJW2ZU\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-04 02:27:12','2026-09-04 02:27:17'),(60,67,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMecf22b24cd18\", \"price\": 0, \"ref_id\": \"TUP-4ZLEHBCMGK\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-04 02:33:53','2026-09-04 02:34:00'),(61,68,NULL,'Simulasi (QRIS)',11000.00,'success',NULL,'{\"data\": {\"rc\": \"00\", \"sn\": \"SIMaa5d99bcc4e2\", \"price\": 0, \"ref_id\": \"TUP-EBIJQB1XR5\", \"status\": \"Sukses\", \"message\": \"TRANSACTION SUCCESSFUL\", \"customer_no\": \"1454641242.16479\", \"buyer_sku_code\": \"JG-ML232\"}}','2026-09-04 02:54:59','2026-09-04 02:55:03');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_google_id_unique` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Admin','admin','admin',NULL,NULL,NULL,'$2y$12$9oNwcM93AuXAP5kttOB7oO5EJpU4Rbshpi3p0Do7tbpJl4L/hxihy',1,NULL,'2026-07-14 23:23:20','2026-07-14 23:25:51'),(6,'ahmad','ahmad','ahmadmusyadadhaury@gmail.com',NULL,NULL,NULL,'$2y$12$MAmI1UkU3DjfXgDC3NZyPehkKYWniJHI.YNvlbkTy1GssD6HAErKO',0,'Zdsp5TjPkb9GvjYmlaPtpuSgyF7Ra1HXxyB8YlsyTbrgygz2NLavaBQz28Lr','2026-07-19 20:09:50','2026-09-03 01:21:39'),(8,'Muhammad Ilyas','ilyas','m.ilyasalfadlih@gmail.com',NULL,NULL,NULL,'$2y$12$j.U5lT9QSMr6NfivGfzGAeQyvHb3zOEclh.e7RZLH.WDngzDHnoQW',0,'yEYAqX1E0wpgT0qINX8SpCrbPK1tVPdWAPS4l3QxFiLOI8dcNm4q1eBGYFE8','2026-07-21 19:06:57','2026-07-22 02:12:56');
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

-- Dump completed on 2026-09-11  8:56:29
