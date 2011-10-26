-- phpMyAdmin SQL Dump
-- version 3.3.10.4
-- http://www.phpmyadmin.net
--
-- Host: mysql.victorlara.net
-- Generation Time: Oct 13, 2011 at 10:40 PM
-- Server version: 5.1.39
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `cattracker_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `arrival`
--

CREATE TABLE IF NOT EXISTS `arrival` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location` int(10) unsigned NOT NULL,
  `time` time DEFAULT NULL,
  `line` int(10) unsigned NOT NULL,
  `sessionID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `line` (`line`,`time`,`sessionID`,`location`),
  KEY `location_idxfk` (`location`),
  KEY `sessionID_idxfk` (`sessionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=95 ;

--
-- Dumping data for table `arrival`
--


-- --------------------------------------------------------

--
-- Table structure for table `daysofoperation`
--

CREATE TABLE IF NOT EXISTS `daysofoperation` (
  `lineID` int(10) unsigned NOT NULL,
  `day` int(1) NOT NULL,
  UNIQUE KEY `day` (`lineID`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `daysofoperation`
--


-- --------------------------------------------------------

--
-- Table structure for table `line`
--

CREATE TABLE IF NOT EXISTS `line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `line`
--


-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lat` decimal(17,14) DEFAULT NULL,
  `lng` decimal(17,14) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `location`
--


-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `session`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `arrival`
--
ALTER TABLE `arrival`
  ADD CONSTRAINT `arrival_ibfk_4` FOREIGN KEY (`sessionID`) REFERENCES `session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `arrival_ibfk_1` FOREIGN KEY (`location`) REFERENCES `location` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `arrival_ibfk_2` FOREIGN KEY (`location`) REFERENCES `location` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `arrival_ibfk_3` FOREIGN KEY (`line`) REFERENCES `line` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daysofoperation`
--
ALTER TABLE `daysofoperation`
  ADD CONSTRAINT `daysofoperation_ibfk_1` FOREIGN KEY (`lineID`) REFERENCES `line` (`id`) ON DELETE CASCADE;







--/* SQLEditor (MySQL (2))*/
--DROP TABLE IF EXISTS location;
--CREATE TABLE location
--(
--id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
--lat DECIMAL(24,20),
--lng DECIMAL(24,20),
--description VARCHAR(255),
--`name` VARCHAR(255),
--PRIMARY KEY (id)
--)ENGINE = INNODB;
--
--DROP TABLE IF EXISTS line;
--CREATE TABLE line
--(
--id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
--name VARCHAR(255),
--PRIMARY KEY (id)
--)ENGINE = INNODB;
--
--DROP TABLE IF EXISTS session;
--CREATE TABLE session
--(
--id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
--description VARCHAR(255),
--active BOOLEAN,
--PRIMARY KEY (id)
--)ENGINE = INNODB;
--
--DROP TABLE IF EXISTS arrival;
--CREATE TABLE arrival
--(
--  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--  `location` int(10) unsigned NOT NULL,
--  `time` time DEFAULT NULL,
--  `line` int(10) unsigned NOT NULL,
--  `sessionID` int(10) unsigned NOT NULL,
--  PRIMARY KEY (`id`),
--  KEY `location_idxfk` (`location`),
--  KEY `line_idxfk` (`line`),
--  KEY `sessionID_idxfk` (`sessionID`)
--) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
--
--create table if not exists `daysofoperation` (
-- `lineID` int(10) unsigned NOT NULL,
-- `day` int(1) NOT NULL,
-- UNIQUE KEY `day`(`lineID`,`day`)
--) engine=innodb
--
--ALTER TABLE arrival ADD FOREIGN KEY location_idxfk (location) REFERENCES location (id) ON DELETE CASCADE;
--
--ALTER TABLE arrival ADD FOREIGN KEY line_idxfk (line) REFERENCES line (id) ON DELETE CASCADE;
--
--ALTER TABLE arrival ADD FOREIGN KEY sessionID_idxfk (sessionID) REFERENCES session (id) ON DELETE CASCADE;
--
--ALTER TABLE daysofoperation ADD FOREIGN KEY lineID_idxfk (lineID) REFERENCES line (id) ON DELETE CASCADE;
