-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 06. Aug 2012 um 22:31
-- Server Version: 5.5.24
-- PHP-Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `bs`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_ally`
--

CREATE TABLE IF NOT EXISTS `bs_ally` (
  `allyID` mediumint(8) NOT NULL AUTO_INCREMENT,
  `tag` varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`allyID`),
  UNIQUE KEY `tag` (`tag`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_banner`
--

CREATE TABLE IF NOT EXISTS `bs_banner` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(80) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_banner_of_ally`
--

CREATE TABLE IF NOT EXISTS `bs_banner_of_ally` (
  `bannerID` int(8) NOT NULL DEFAULT '0',
  `allyID` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bannerID`,`allyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_message`
--

CREATE TABLE IF NOT EXISTS `bs_message` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_runde`
--

CREATE TABLE IF NOT EXISTS `bs_runde` (
  `rundenID` int(3) NOT NULL AUTO_INCREMENT,
  `rundenname` varchar(20) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`rundenID`),
  UNIQUE KEY `rundenname` (`rundenname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bs_teil_der_runde`
--

CREATE TABLE IF NOT EXISTS `bs_teil_der_runde` (
  `allyID` int(8) NOT NULL DEFAULT '0',
  `rundenID` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`allyID`,`rundenID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
