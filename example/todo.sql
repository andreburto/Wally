-- --------------------------------------------------------
-- Host:                         aglaope
-- Server version:               5.5.24 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2014-03-17 21:05:35
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for todo
CREATE DATABASE IF NOT EXISTS `todo` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `todo`;


-- Dumping structure for table todo.todo
CREATE TABLE IF NOT EXISTS `todo` (
  `itemid` int(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `todo` text NOT NULL,
  `created` double NOT NULL,
  PRIMARY KEY (`itemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table todo.users
CREATE TABLE IF NOT EXISTS `users` (
  `userid` bigint(20) NOT NULL AUTO_INCREMENT,
  `usrname` varchar(255) DEFAULT '0',
  `password` varchar(255) DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
