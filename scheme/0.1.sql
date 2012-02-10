# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.9)
# Database: connect
# Generation Time: 2012-02-10 03:59:25 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table usermeta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `usermeta`;

CREATE TABLE `usermeta` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_user` int(200) NOT NULL,
  `meta_key` varchar(45) NOT NULL,
  `meta_value` longtext,
  `autoload` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`meta_id`),
  KEY `user_id` (`meta_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_slug` varchar(45) DEFAULT NULL,
  `user_email` varchar(45) DEFAULT NULL,
  `user_pass` varchar(100) CHARACTER SET utf8 NOT NULL,
  `user_name` varchar(45) DEFAULT NULL,
  `user_registration` datetime DEFAULT NULL COMMENT 'Relative to GMT',
  `user_suspended` int(1) unsigned zerofill NOT NULL,
  `user_timezone` varchar(45) NOT NULL DEFAULT 'UM8',
  `birthday` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


# Dump of table acct_passchg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `acct_passchg`;

CREATE TABLE `acct_passchg` (
  `chg_id` int(11) NOT NULL AUTO_INCREMENT,
  `chg_user` int(200) NOT NULL,
  `chg_time` int(20) NOT NULL,
  `chg_hash` varchar(45) NOT NULL,
  PRIMARY KEY (`chg_id`),
  KEY `user_id` (`chg_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='User Password Change requests';


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
