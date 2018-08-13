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

-- Export de données de la table stillsf.category : ~0 rows (environ)
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
/*!40000 ALTER TABLE `category` ENABLE KEYS */;

-- Export de données de la table stillsf.contact : ~9 rows (environ)
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
	(9, 'Mohammed', 'Chiba', NULL, 'Mohammedia', NULL, 'Contact', NULL, NULL);
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;

-- Export de données de la table stillsf.info : ~1 rows (environ)
/*!40000 ALTER TABLE `info` DISABLE KEYS */;
INSERT INTO `info` (`id`, `type`, `label`, `value`, `contact_id`) VALUES
	(1, 'LandLine', 'Fix', '0523214587', 1);
/*!40000 ALTER TABLE `info` ENABLE KEYS */;

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

-- Export de données de la table stillsf.user : ~0 rows (environ)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
