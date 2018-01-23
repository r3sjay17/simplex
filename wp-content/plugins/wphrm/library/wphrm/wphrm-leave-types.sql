-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `wphrm_leavetypes`;
CREATE TABLE `wphrm_leavetypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `leaveType` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `period` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `numberOfLeave` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `leavetypes_leavetype_index` (`leaveType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `wphrm_leavetypes` (`id`, `leaveType`, `period`, `numberOfLeave`) VALUES
(1,	'Sick Leave',	'Yearly',	6)
(2,	'Annual Leave',	'Yearly',	6)

-- 2016-07-11 12:44:57
