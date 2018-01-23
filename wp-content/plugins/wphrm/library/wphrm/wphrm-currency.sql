-- Adminer 4.2.4 MySQL dump


SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `wphrm_currency`;
CREATE TABLE `wphrm_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currencyName` varchar(200) NOT NULL,
  `currencySign` varchar(200) NOT NULL,
  `currencyDesc` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `wphrm_currency` (`id`, `currencyName`, `currencySign`, `currencyDesc`) VALUES
(1, 'USD', '&#36', 'USD Currency'),
(2, 'INR', '&#8377', 'INR Currency'),
(3, 'GBP', '&#163', 'GBP Currency'),
(4, 'JPY', '&#165', 'JPY Currency'),
(5, 'YEN', '&#165', 'YEN Currency'),
(6, 'EUR', '&#8364', 'EUR Currency'),
(7, 'WON', '&#8361', 'WON Currency'),
(8, 'TRY', '&#8356', 'TRY Currency'),
(9, 'RUB', '&#1088', 'RUB Currency'),
(10, 'RMB', '&#165', 'RMB Currency'),
(11, 'KRW', '&#8361', 'KRW Currency'),
(12, 'BTC', '&#8361', 'BTC Currency'),
(13, 'THB', '&#3647', 'THB Currency'),
(14, 'BDT', '&#2547', 'BDT Currency'),
(15, 'CRC', '&#8353', 'CRC Currency'),
(16, 'GEL', '&#4314', 'GEL Currency');
-- 2016-07-12 11:43:21