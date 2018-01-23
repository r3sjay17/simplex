-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `wphrm_settings`;
CREATE TABLE `wphrm_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `authorID` varchar(200) NOT NULL,
  `settingKey` varchar(200) NOT NULL,
  `settingValue` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `wphrm_settings` (`id`, `authorID`, `settingKey`, `settingValue`) VALUES
(1,	'0',	'wphrmMonths',	'YToxMjp7czoyOiIwMSI7czo3OiJKYW51YXJ5IjtzOjI6IjAyIjtzOjg6IkZlYnJ1YXJ5IjtzOjI6IjAzIjtzOjU6Ik1hcmNoIjtzOjI6IjA0IjtzOjU6IkFwcmlsIjtzOjI6IjA1IjtzOjM6Ik1heSI7czoyOiIwNiI7czo0OiJKdW5lIjtzOjI6IjA3IjtzOjQ6Ikp1bHkiO3M6MjoiMDgiO3M6NjoiQXVndXN0IjtzOjI6IjA5IjtzOjk6IlNlcHRlbWJlciI7aToxMDtzOjc6Ik9jdG9iZXIiO2k6MTE7czo4OiJOb3ZlbWJlciI7aToxMjtzOjg6IkRlY2VtYmVyIjt9'),
(3,	'0',	'wphrmGeneralSettingsInfo',	'YTo2OntzOjE4OiJ3cGhybV9jb21wYW55X2xvZ28iO3M6MDoiIjtzOjIzOiJ3cGhybV9jb21wYW55X2Z1bGxfbmFtZSI7czowOiIiO3M6MTk6IndwaHJtX2NvbXBhbnlfZW1haWwiO3M6MDoiIjtzOjE5OiJ3cGhybV9jb21wYW55X3Bob25lIjtzOjA6IiI7czoyMToid3Bocm1fY29tcGFueV9hZGRyZXNzIjtzOjE1MjoiICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAiO3M6MTQ6IndwaHJtX2N1cnJlbmN5IjtzOjc6IuKCuS1JTlIiO30='),
(7,	'0',	'wphrmNotificationsSettingsInfo',	'YTo0OntzOjI5OiJ3cGhybV9hdHRlbmRhbmNlX25vdGlmaWNhdGlvbiI7TjtzOjI1OiJ3cGhybV9ub3RpY2Vfbm90aWZpY2F0aW9uIjtzOjE6IjEiO3M6MjQ6IndwaHJtX2xlYXZlX25vdGlmaWNhdGlvbiI7czoxOiIxIjtzOjE4OiJ3cGhybV9lbXBsb3llZV9hZGQiO047fQ=='),
(8,	'0',	'wphrmSalarySlipInfo',	'YTo2OntzOjE2OiJ3cGhybV9sb2dvX2FsaWduIjtzOjQ6ImxlZnQiO3M6MTg6IndwaHJtX3NsaXBfY29udGVudCI7czo1OiJXUEhSTSI7czoyNjoid3Bocm1fZm9vdGVyX2NvbnRlbnRfYWxpZ24iO3M6NToicmlnaHQiO3M6MTg6IndwaHJtX2JvcmRlcl9jb2xvciI7czoxOiIjIjtzOjIyOiJ3cGhybV9iYWNrZ3JvdW5kX2NvbG9yIjtzOjc6IiNFQ0VGRjEiO3M6MTY6IndwaHJtX2ZvbnRfY29sb3IiO3M6NzoiIzU0NkU3QSI7fQ=='),
(9,	'0',	'wphrmUserPermissionInfo',	'YToxOntzOjIxOiJ3cGhybV91c2VyX3Blcm1pc3Npb24iO3M6MTA6InN1YnNjcmliZXIiO30='),
(10,	'0',	'wphrmExpenseReportInfo',	'YToxOntzOjIwOiJ3cGhybV9leHBlbnNlX2Ftb3VudCI7czo1OiIyMDAwMCI7fQ=='),
(11,	'0',	'Bankfieldskey',	'YToxOntzOjE1OiJCYW5rZmllbGRzbGViYWwiO2E6Mjp7aTowO3M6MTE6IkJyYW5jaCBOYW1lIjtpOjE7czo5OiJJRlNDIENvZGUiO319'),
(12,	'0',	'Otherfieldskey',	'YToxOntzOjE2OiJPdGhlcmZpZWxkc2xlYmFsIjthOjI6e2k6MDtzOjEzOiJHbWFpbCBBY2NvdW50IjtpOjE7czoxMzoiU2t5cGUgQWNjb3VudCI7fX0='),
(13,	'0',	'salarydetailfieldskey',	'YToxOntzOjIyOiJzYWxhcnlkZXRhaWxmaWVsZGxhYmVsIjthOjI6e2k6MDtzOjE0OiJKb2luaW5nIFNhbGFyeSI7aToxO3M6MTI6IkJhc2ljIFNhbGFyeSI7fX0='),
(14,	'0',	'wphrmEarningInfo',	'YToxOntzOjEyOiJlYXJuaW5nTGViYWwiO2E6MTp7aTowO3M6MzoiSFJBIjt9fQ=='),
(15,	'0',	'wphrmDeductionInfo',	'YToxOntzOjE0OiJkZWR1Y3Rpb25sZWJhbCI7YToxOntpOjA7czoyOiJQRiI7fX0=');

-- 2016-07-12 11:42:43
