-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `wphrm_messages`;
CREATE TABLE `wphrm_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messagesTitle` varchar(50) NOT NULL,
  `messagesDesc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `wphrm_messages` (`id`, `messagesTitle`, `messagesDesc`) VALUES
(1,	'Add Employee',	'Employee has been successfully added.'),
(2,	'Update Employee',	'Employee has been successfully updated.'),
(3,	'Update Personal Details',	'Personal Details have been successfully updated.'),
(4,	'Update Bank  Details',	'Bank Details have been successfully updated.'),
(5,	'Update Documents',	'Documents have been successfully updated.'),
(6,	'Update Other Details',	'Other Details have been successfully updated.'),
(7,	'Update Salary Details',	'Salary Details have been successfully updated.'),
(8,	'Add Department',	'Department has been successfully added.'),
(9,	'Update Department',	'Department has been successfully updated.'),
(10,	'Add Designation',	'Designation has been successfully added.'),
(11,	'Update Designation',	'Designation has been successfully updated.'),
(12,	'Delete Designation',	'Designation has been successfully deleted.'),
(13,	'Delete Department',	'Department has been successfully deleted.'),
(14,	'Add Holiday',	'Holiday has been successfully added.'),
(15,	'Delete Holiday',	'Holiday has been successfully deleted. '),
(16,	'Mark Attendance',	'Attendance has been successfully marked.'),
(17,	'Delete Leave Application',	'Leave Application has been successfully deleted.'),
(18,	'Update Leave Type',	'Leave Type  has been successfully updated.'),
(19,	'Add Leave Type',	'Leave Type  has been successfully added.'),
(20,	'Delete Leave Type',	'Leave Type has been successfully deleted.'),
(21,	'Create Salary slip',	'Salary Slip Details have been successfully created.'),
(22,	'Update Salary Slip',	'Salary Slip has been successfully updated.'),
(23,	'Delete Salary slip',	'Salary Slip has been successfully deleted.'),
(24,	'Sent Salary slip Request',	'Salary Slip Request has been successfully sent.'),
(25,	'Sent Salary slip',	'Salary Slip has been successfully sent.'),
(26,	'Update Notices',	'Notice has been successfully updated.'),
(27,	'Update General Settings',	'General Settings have been successfully updated.'),
(28,	'Update Notifications Settings',	'Notifications Settings  have been successfully updated.'),
(29,	'Update Change Password',	'Password has been successfully updated.'),
(30,	'Update Salary Slip Settings',	'Salary Slip Settings has been successfully updated.'),
(31,	'Update Users Permission Settings',	'Users Permission has been successfully updated.'),
(32,	'Update Leave Application',	'Leave Application has been successfully updated.'),
(33,	'Sent Leave Appliction',	'Leave appliction has been successfully sent.'),
(34,	'Update Messges Settings',	'Messge has been successfully updated.'),
(35,	'Expense Amount Update',	'Expense amount has been successfully updated.'),
(36,	'Add Financials',	'Financial has been successfully added.'),
(37,	'Update Financials',	'Financial has been successfully updated.'),
(38,	'Duplicate Salary Slip',	'Salary Slip has been successfully duplicated.'),
(39,	'Update Settings',	'Settings field has been successfully updated.'),
(40,	'Add Deduction label',	'Deduction label has been successfully added.'),
(41,	'Update Deduction label',	'Deduction label has been successfully updated.'),
(42,	'Delete Settings Field label',	'Settings Field label has been successfully deleted.');

-- 2016-07-12 11:43:21
