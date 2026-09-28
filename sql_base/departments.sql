/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint unsigned NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_country_id_foreign` (`country_id`),
  CONSTRAINT `departments_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departments` (`id`, `country_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Amazonas', NULL, NULL);
INSERT INTO `departments` (`id`, `country_id`, `name`, `created_at`, `updated_at`) VALUES
(2, 1, 'Antioquia', NULL, NULL);
INSERT INTO `departments` (`id`, `country_id`, `name`, `created_at`, `updated_at`) VALUES
(3, 1, 'Arauca', NULL, NULL);
INSERT INTO `departments` (`id`, `country_id`, `name`, `created_at`, `updated_at`) VALUES
(4, 1, 'Archipiélago De San Andrés, Providencia Y Santa Catalina', NULL, NULL),
(5, 1, 'Atlántico', NULL, NULL),
(6, 1, 'Bogotá, D.C.', NULL, NULL),
(7, 1, 'Bolívar', NULL, NULL),
(8, 1, 'Boyacá', NULL, NULL),
(9, 1, 'Caldas', NULL, NULL),
(10, 1, 'Caquetá', NULL, NULL),
(11, 1, 'Casanare', NULL, NULL),
(12, 1, 'Cauca', NULL, NULL),
(13, 1, 'Cesar', NULL, NULL),
(14, 1, 'Chocó', NULL, NULL),
(15, 1, 'Córdoba', NULL, NULL),
(16, 1, 'Cundinamarca', NULL, NULL),
(17, 1, 'Guainía', NULL, NULL),
(18, 1, 'Guaviare', NULL, NULL),
(19, 1, 'Huila', NULL, NULL),
(20, 1, 'La Guajira', NULL, NULL),
(21, 1, 'Magdalena', NULL, NULL),
(22, 1, 'Meta', NULL, NULL),
(23, 1, 'Nariño', NULL, NULL),
(24, 1, 'Norte De Santander', NULL, NULL),
(25, 1, 'Putumayo', NULL, NULL),
(26, 1, 'Quindio', NULL, NULL),
(27, 1, 'Risaralda', NULL, NULL),
(28, 1, 'Santander', NULL, NULL),
(29, 1, 'Sucre', NULL, NULL),
(30, 1, 'Tolima', NULL, NULL),
(31, 1, 'Valle Del Cauca', NULL, NULL),
(32, 1, 'Vaupés', NULL, NULL),
(33, 1, 'Vichada', NULL, NULL),
(34, 2, 'Azuay', NULL, NULL),
(35, 2, 'Bolívar', NULL, NULL),
(36, 2, 'Cañar', NULL, NULL),
(37, 2, 'Carchi', NULL, NULL),
(38, 2, 'Cotopaxi', NULL, NULL),
(39, 2, 'Chimborazo', NULL, NULL),
(40, 2, 'El Oro', NULL, NULL),
(41, 2, 'Esmeraldas', NULL, NULL),
(42, 2, 'Guayas', NULL, NULL),
(43, 2, 'Imbabura', NULL, NULL),
(44, 2, 'Loja', NULL, NULL),
(45, 2, 'Los Ríos', NULL, NULL),
(46, 2, 'Manabí', NULL, NULL),
(47, 2, 'Morona Santiago', NULL, NULL),
(48, 2, 'Napo', NULL, NULL),
(49, 2, 'Pastaza', NULL, NULL),
(50, 2, 'Pichincha', NULL, NULL),
(51, 2, 'Tungurahua', NULL, NULL),
(52, 2, 'Zamora Chinchipe', NULL, NULL),
(53, 2, 'Galápagos', NULL, NULL),
(54, 2, 'Sucumbíos', NULL, NULL),
(55, 2, 'Orellana', NULL, NULL),
(56, 2, 'Santo Domingo De Los Tsáchilas', NULL, NULL),
(57, 2, 'Santa Elena', NULL, NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;