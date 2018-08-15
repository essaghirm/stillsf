-- --------------------------------------------------------
-- Hôte :                        localhost
-- Version du serveur:           5.7.19 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Export de la structure de la base pour stillsf
DROP DATABASE IF EXISTS `stillsf`;
CREATE DATABASE IF NOT EXISTS `stillsf` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `stillsf`;

-- Export de la structure de la table stillsf. category
DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table stillsf.category : ~8 rows (environ)
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` (`id`, `title`, `lft`, `rgt`) VALUES
	(1, 'Institutions Publiques', 1, 6),
	(2, 'Services aux PME', 7, 8),
	(3, 'Tissu privé', 9, 10),
	(4, 'Services de BTP', 11, 12),
	(5, 'Contractants de BTP', 13, 14),
	(6, 'Marques de BTP', 15, 16),
	(7, 'institutios de pilotage', 2, 3),
	(8, 'Institutions contrôlées par l\'état', 4, 5);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;

-- Export de la structure de la table stillsf. contact
DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_site` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4C62E63812469DE2` (`category_id`),
  CONSTRAINT `FK_4C62E63812469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table stillsf.contact : ~19 rows (environ)
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` (`id`, `fname`, `lname`, `web_site`, `city`, `notes`, `type`, `created`, `category_id`) VALUES
	(1, 'Mouhcine', 'ESSAGHIR', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(2, 'Khalid', 'Bahri', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(3, 'Karima', 'Behri', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(4, 'Aziza', 'Khomri', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(5, 'Fatima', 'Mostaadir', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(6, 'Ahemd', 'Mansouri', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(7, 'Ziad', 'Berrada', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(8, 'Ilyas', 'Lotfi', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(9, 'Mohammed', 'Chiba', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL),
	(10, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:24:30', NULL),
	(11, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:32:20', NULL),
	(12, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:37:05', NULL),
	(13, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:37:12', NULL),
	(14, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:37:14', NULL),
	(15, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:49:56', 1),
	(16, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:52:37', 1),
	(17, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:55:14', 1),
	(18, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:55:42', 1),
	(19, 'Anouar', 'Khalili', NULL, 'Mohammedia', NULL, 'Contact', '2018-08-15 17:59:08', NULL);
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;

-- Export de la structure de la table stillsf. info
DROP TABLE IF EXISTS `info`;
CREATE TABLE IF NOT EXISTS `info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_CB8931571D775834` (`value`),
  KEY `IDX_CB893157E7A1254A` (`contact_id`),
  CONSTRAINT `FK_CB893157E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table stillsf.info : ~1 rows (environ)
/*!40000 ALTER TABLE `info` DISABLE KEYS */;
INSERT INTO `info` (`id`, `type`, `label`, `value`, `contact_id`) VALUES
	(1, 'LandLine', 'Fix', '0523214587', 1);
/*!40000 ALTER TABLE `info` ENABLE KEYS */;

-- Export de la structure de la table stillsf. migration_versions
DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Export de données de la table stillsf.migration_versions : ~7 rows (environ)
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
INSERT INTO `migration_versions` (`version`) VALUES
	('20180813161000'),
	('20180813161646'),
	('20180813162244'),
	('20180813172606'),
	('20180813173506'),
	('20180813174040'),
	('20180813181026');
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;

-- Export de la structure de la table stillsf. relation
DROP TABLE IF EXISTS `relation`;
CREATE TABLE IF NOT EXISTS `relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_UNQ_CONTACT_FRIEND` (`contact_id`,`friend_id`),
  KEY `IDX_62894749E7A1254A` (`contact_id`),
  KEY `IDX_628947496A5458E8` (`friend_id`),
  CONSTRAINT `FK_628947496A5458E8` FOREIGN KEY (`friend_id`) REFERENCES `contact` (`id`),
  CONSTRAINT `FK_62894749E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table stillsf.relation : ~6 rows (environ)
/*!40000 ALTER TABLE `relation` DISABLE KEYS */;
INSERT INTO `relation` (`id`, `contact_id`, `friend_id`, `occupation`) VALUES
	(1, 1, 2, NULL),
	(3, 1, 3, NULL),
	(4, 2, 3, NULL),
	(7, 5, 2, NULL),
	(9, 2, 1, NULL),
	(10, 7, 2, NULL);
/*!40000 ALTER TABLE `relation` ENABLE KEYS */;

-- Export de la structure de la table stillsf. user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `roles` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table stillsf.user : ~0 rows (environ)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
