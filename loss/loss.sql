-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:5036
-- Generation Time: Apr 05, 2016 at 03:52 AM
-- Server version: 5.6.20-log
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bgr002proban`
--

-- --------------------------------------------------------

--
-- Table structure for table `loss`
--

CREATE TABLE IF NOT EXISTS `loss` (
  `id` varchar(100) CHARACTER SET utf8 NOT NULL,
  `inch` varchar(10) NOT NULL,
  `size` varchar(20) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `type` varchar(40) NOT NULL,
  `patten` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '0',
  `branch` varchar(10) NOT NULL,
  `nm_customer` varchar(50) DEFAULT NULL,
  `tlp` varchar(15) DEFAULT NULL,
  `tgl_input` datetime NOT NULL,
  `user` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
