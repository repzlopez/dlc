-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 14, 2022 at 09:39 AM
-- Server version: 5.7.38-cll-lve
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `diamondl_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `delivers`
--

CREATE TABLE `delivers` (
  `delivers_id` int(16) UNSIGNED ZEROFILL NOT NULL,
  `dsdid` varchar(16) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `contact` varchar(16) DEFAULT NULL,
  `street` varbinary(64) DEFAULT NULL,
  `province` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `brgy` varchar(32) DEFAULT NULL,
  `postal` varchar(8) NOT NULL,
  `note` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `distributors`
--

CREATE TABLE `distributors` (
  `dscoid` varchar(255) NOT NULL,
  `dsdid` varchar(255) NOT NULL,
  `dsfnam` varchar(255) DEFAULT NULL,
  `dsmnam` varchar(255) NOT NULL,
  `dslnam` varchar(255) DEFAULT NULL,
  `dsoph` varchar(255) NOT NULL,
  `dshph` varchar(255) NOT NULL,
  `dsmph` varchar(255) NOT NULL,
  `dsstrt` varchar(255) NOT NULL,
  `dsbarn` varchar(255) NOT NULL,
  `dscity` varchar(255) NOT NULL,
  `dsprov` varchar(255) NOT NULL,
  `dssoid` varchar(255) NOT NULL,
  `dssid` varchar(255) NOT NULL,
  `dsbrth` varchar(255) NOT NULL,
  `dsdate` varchar(255) NOT NULL,
  `dstin` varchar(255) NOT NULL,
  `dseadd` varchar(255) NOT NULL,
  `dssetd` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `transactions_id` int(16) UNSIGNED ZEROFILL NOT NULL,
  `pid` int(5) UNSIGNED ZEROFILL DEFAULT NULL,
  `wsp` float NOT NULL DEFAULT '0',
  `pov` float NOT NULL DEFAULT '0',
  `srp` float NOT NULL DEFAULT '0',
  `pv` float NOT NULL DEFAULT '0',
  `qty` int(4) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payments_id` int(16) UNSIGNED ZEROFILL NOT NULL,
  `dsdid` varchar(16) DEFAULT NULL,
  `options` varchar(16) NOT NULL,
  `note` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactions_id` int(16) UNSIGNED ZEROFILL NOT NULL,
  `delivers_id` int(16) UNSIGNED ZEROFILL NOT NULL,
  `payments_id` int(16) UNSIGNED ZEROFILL NOT NULL,
  `dsdid` varchar(16) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fees` float NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `delivered` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `delivers`
--
ALTER TABLE `delivers`
  ADD PRIMARY KEY (`delivers_id`);

--
-- Indexes for table `distributors`
--
ALTER TABLE `distributors`
  ADD UNIQUE KEY `dsdid` (`dsdid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD KEY `orders_id` (`transactions_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payments_id`),
  ADD UNIQUE KEY `refNo` (`payments_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactions_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `delivers`
--
ALTER TABLE `delivers`
  MODIFY `delivers_id` int(16) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payments_id` int(16) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactions_id` int(16) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
