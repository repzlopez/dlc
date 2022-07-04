-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 03, 2022 at 09:07 PM
-- Server version: 5.7.38-cll-lve
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `diamondl_pcm`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `un` varchar(16) NOT NULL DEFAULT '',
  `pw` text,
  `scop` varchar(32) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`un`, `pw`, `scop`, `status`) VALUES
('45678', '174108c2c73a572ce4437d27a9e30e72', 'PCM', 1),
('67890', '5df5538b7df08a5863a26d40f7aae3fb', 'PCM', 1),
('107', '9df95956e3387e926e692209293002a5', 'PCM', 1),
('46106', '274935c5a1d795ebf68d1f1f6aabeb39', 'PCM', 1),
('99999', '1fe44720e0a1f42889fc0aa3ae9dbd1e', 'PCM', 1),
('46107', 'f8c200aa26cfc9e15a927eb747965b6b', 'PCM', 1),
('46103', '5348364afe629b17501ca9440e53ee1a', 'PCM', 1),
('46104', 'b444f4910ebdab1ed34d6bf29be06e6d', 'PCM', 1),
('46105', 'f91c6468d2e84c211e531acb41aa77c4', 'PCM', 1),
('46110', 'd9a580dbcbc85a78ef975a2006580f9d', 'PCM', 1),
('46114', 'ee74b7aa4daaff512a3f6ca76f50887f', 'PCM', 1),
('34567', 'd7a6f71133f1697d7af53378992a4488', 'PCM', 1),
('46108', '2b746fb193330e49f9e303729e426904', 'PCM', 1),
('46109', '837b981ddd1dff6c6d54985a815ac257', 'PCM', 1),
('46111', 'b6c5c4415f211c962a9d72e1ff7ef8fa', 'PCM', 1),
('46112', '2d2237c586b55a2fd11111c1929d9f3e', 'PCM', 1),
('46113', 'e204b307b6f0c94caea06c36144ee058', 'PCM', 1),
('46115', '5aa53a54603c725b0b5fd0b0fd6be39c', 'PCM', 1),
('46116', 'e8cc0fa3f0b081031402a74665f7066b', 'PCM', 1),
('46117', '236fc3430b795b7ea27d8e18955fad99', 'PCM', 1),
('123', 'c786c467d399f2c7c071085186b5bf6f', 'PCM', 0),
('125', '3ff5918f6f3315709e2aa658403cf9c9', 'PCM', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbldistri`
--

CREATE TABLE `tbldistri` (
  `dsdid` varchar(12) NOT NULL DEFAULT '',
  `dslname` varchar(32) DEFAULT NULL,
  `dsfname` varchar(32) DEFAULT NULL,
  `dsmname` varchar(32) DEFAULT NULL,
  `dscont` varchar(32) DEFAULT NULL,
  `dsstrt` text,
  `dsbrgy` text,
  `dscity` text,
  `dsprov` text,
  `dsbday` varchar(16) DEFAULT NULL,
  `dstin` varchar(16) DEFAULT NULL,
  `dssid` varchar(12) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `date` varchar(8) DEFAULT NULL,
  `branch` varchar(8) DEFAULT NULL,
  `req` varchar(8) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbllog`
--

CREATE TABLE `tbllog` (
  `id` int(16) UNSIGNED ZEROFILL NOT NULL,
  `reftran` varchar(16) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `action` tinyint(1) NOT NULL,
  `login` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblorders`
--

CREATE TABLE `tblorders` (
  `refno` varchar(16) NOT NULL,
  `refdate` varchar(8) DEFAULT NULL,
  `dsdid` varchar(16) DEFAULT NULL,
  `dsnam` varchar(64) DEFAULT NULL,
  `dscon` varchar(12) DEFAULT NULL,
  `dstin` varchar(12) DEFAULT NULL,
  `paycash` varchar(32) DEFAULT '0',
  `paychek` varchar(32) DEFAULT '0',
  `paycard` varchar(32) DEFAULT '0',
  `payfund` varchar(32) DEFAULT '0',
  `paydate` varchar(8) DEFAULT NULL,
  `payconf` text,
  `paystat` tinyint(1) NOT NULL DEFAULT '0',
  `replen` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `remitid` varchar(20) DEFAULT NULL,
  `invoice` varchar(16) DEFAULT NULL,
  `orders` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblremit`
--

CREATE TABLE `tblremit` (
  `transact` varchar(20) NOT NULL,
  `paydate` varchar(10) DEFAULT NULL,
  `paytype` varchar(16) DEFAULT NULL,
  `payamt` varchar(16) NOT NULL DEFAULT '0',
  `paynote` text,
  `payscan` tinyint(1) NOT NULL DEFAULT '0',
  `stat` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblremit`
--

INSERT INTO `tblremit` (`transact`, `paydate`, `paytype`, `payamt`, `paynote`, `payscan`, `stat`) VALUES
('99999820200827111733', '2020.08.27', 'Fund Transfer', '4322.00', 'paymaya', 0, 0),
('99999820200827111820', '2020.08.27', 'Fund Transfer', '4322.00', 'paymaya', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblreplenish`
--

CREATE TABLE `tblreplenish` (
  `code` varchar(10) NOT NULL,
  `qty` int(8) NOT NULL DEFAULT '0',
  `req` int(8) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblsetup`
--

CREATE TABLE `tblsetup` (
  `id` varchar(16) NOT NULL DEFAULT '',
  `wh` varchar(5) DEFAULT NULL,
  `dfrec` varchar(32) DEFAULT NULL,
  `dfcon` varchar(32) DEFAULT NULL,
  `dfadd` text,
  `dfcor` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`un`);

--
-- Indexes for table `tbldistri`
--
ALTER TABLE `tbldistri`
  ADD PRIMARY KEY (`dsdid`);

--
-- Indexes for table `tbllog`
--
ALTER TABLE `tbllog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblorders`
--
ALTER TABLE `tblorders`
  ADD PRIMARY KEY (`refno`);

--
-- Indexes for table `tblreplenish`
--
ALTER TABLE `tblreplenish`
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `tblsetup`
--
ALTER TABLE `tblsetup`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbllog`
--
ALTER TABLE `tbllog`
  MODIFY `id` int(16) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
