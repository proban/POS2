-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2016 at 02:30 AM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bks001proban`
--

-- --------------------------------------------------------

--
-- Table structure for table `movements_pusat`
--

CREATE TABLE IF NOT EXISTS `movements_pusat` (
  `move_id` varchar(50) NOT NULL,
  `document_no` int(11) NOT NULL,
  `location_id` varchar(20) NOT NULL,
  `move_date` datetime NOT NULL,
  `move_type` int(11) NOT NULL,
  `from_warehouse` varchar(20) NOT NULL,
  `to_warehouse` varchar(20) NOT NULL,
  `move_by` varchar(20) NOT NULL,
  `move_ref` varchar(50) DEFAULT NULL,
  `insert_by` varchar(50) NOT NULL,
  `insert_date` datetime NOT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movements_pusat`
--
ALTER TABLE `movements_pusat`
 ADD PRIMARY KEY (`move_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
