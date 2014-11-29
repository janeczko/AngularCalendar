-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `calendar` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
USE `calendar`;

DROP TABLE IF EXISTS `input`;
CREATE TABLE `input` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `from` datetime NOT NULL,
  `to` datetime NOT NULL,
  `note` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `input` (`id`, `name`, `from`, `to`, `note`) VALUES
(2,	'Prejmenovany a upraveny Test 1',	'2014-11-04 10:13:00',	'2014-11-04 12:07:00',	'Testovaci poznamka 1'),
(3,	'Prodat babku',	'2015-01-15 06:05:00',	'2015-01-15 11:00:00',	NULL),
(6,	'Test bla bla',	'2014-11-04 00:25:00',	'2014-11-04 04:01:00',	'Pridal jsem poznamku'),
(8,	'Dodelat',	'2014-11-28 00:55:00',	'2014-11-28 11:00:00',	'Konecne dodelat tuto divnou vec :D'),
(12,	'Blab bla',	'2014-11-04 12:00:00',	'2014-11-04 12:30:00',	''),
(13,	'Neco neco a jeste neco',	'2014-11-28 00:10:00',	'2014-11-28 11:20:00',	''),
(11,	'Uiii',	'2014-11-04 12:10:00',	'2014-11-04 06:00:00',	'');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `username`, `password`, `admin`) VALUES
(1,	'janeczko',	'16f19095c3e6eebfb3d6f97708ca3a6e404ea61b',	1),
(4,	'karel',	'debfa3e4508108cd27f0535bd9f4e6e22861e70c',	0),
(5,	'opick',	'debfa3e4508108cd27f0535bd9f4e6e22861e70c',	0);

-- 2014-11-29 20:35:28
