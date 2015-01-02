-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2014 at 12:02 PM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pendoreille`
--
CREATE DATABASE IF NOT EXISTS `pendoreille` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `pendoreille`;

-- --------------------------------------------------------

--
-- Table structure for table `environmental_data`
--

CREATE TABLE IF NOT EXISTS `environmental_data` (
  `date_recorded` date NOT NULL,
  `air_temp` text,
  `bar_press` text,
  `wind_speed` text,
  PRIMARY KEY (`date_recorded`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- User accounts
--

GRANT USAGE ON *.* TO 'pendadmin'@'localhost' IDENTIFIED BY PASSWORD '*B807E6B9C07ADD2E3CE3AB0E44003454B3F105BF';
GRANT ALL PRIVILEGES ON `pendoreille`.* TO 'pendadmin'@'localhost' WITH GRANT OPTION;
GRANT USAGE ON *.* TO 'penduser'@'localhost' IDENTIFIED BY PASSWORD '*B807E6B9C07ADD2E3CE3AB0E44003454B3F105BF';
GRANT SELECT ON `pendoreille`.* TO 'penduser'@'localhost';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
