-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 16, 2013 at 06:19 PM
-- Server version: 5.1.69
-- PHP Version: 5.3.2-1ubuntu4.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `anonghost`
--

-- --------------------------------------------------------

--
-- Table structure for table `floodlimit`
--

DROP TABLE IF EXISTS `floodlimit`;
CREATE TABLE IF NOT EXISTS `floodlimit` (
  `hash` char(32) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `postID` int(8) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `masknumber` char(18) NOT NULL,
  `posttext` varchar(1024) NOT NULL,
  `parentID` int(8) DEFAULT NULL,
  PRIMARY KEY (`postID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4201 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `postview`
--
DROP VIEW IF EXISTS `postview`;
CREATE TABLE IF NOT EXISTS `postview` (
`postID` int(8)
,`sincetime` text
,`masknumber` char(18)
,`posttext` varchar(1024)
,`TIMESTAMP` timestamp
);
-- --------------------------------------------------------

--
-- Structure for view `postview`
--
DROP TABLE IF EXISTS `postview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `postview` AS select `posts`.`postID` AS `postID`,`timeconvert`(time_to_sec(timediff(now(),`posts`.`timestamp`))) AS `sincetime`,`posts`.`masknumber` AS `masknumber`,`posts`.`posttext` AS `posttext`,`posts`.`timestamp` AS `TIMESTAMP` from `posts` order by `posts`.`timestamp` desc;

DELIMITER $$
--
-- Functions
--
DROP FUNCTION IF EXISTS `timeconvert`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `timeconvert`(seconds INT) RETURNS text CHARSET utf8
BEGIN

	DECLARE displaytime VARCHAR(20);

		IF seconds = 1 THEN SET displaytime = '1 second';
		ELSEIF seconds < 60 THEN SET displaytime = (Concat(seconds,CONVERT(' seconds' USING utf8)));
		ELSEIF seconds < (60*2) THEN SET displaytime = '1 minute';
		ELSEIF seconds < (60*60) THEN SET displaytime = (Concat(round(seconds/60),CONVERT(' minutes' USING utf8)));
		ELSEIF seconds < (60*60*2) THEN SET displaytime = '1 hour';
		ELSEIF seconds < (60*60*24) THEN SET displaytime = (Concat(round(seconds/(60*60)),CONVERT(' hours' USING utf8)));
		ELSEIF seconds < (60*60*24*2) THEN SET displaytime = '1 day';
		ELSEIF seconds < (60*60*24*30) THEN SET displaytime = (Concat(round(seconds/(60*60*24)),CONVERT(' days' USING utf8)));
		ELSEIF seconds < (60*60*24*30*2) THEN SET displaytime = '1 month';
		ELSE SET displaytime = (Concat(round(seconds/(60*60*24*30)),CONVERT(' months' USING utf8)));
		END IF;

	RETURN displaytime;

END$$

DELIMITER ;
