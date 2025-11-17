-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 01:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hrmdbonesolution`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `logo_alt_text` varchar(255) DEFAULT NULL,
  `banner_alt_text` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `mobile1` varchar(20) DEFAULT NULL,
  `mobile2` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `founded_year` int(11) DEFAULT NULL,
  `employee_count` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `description` text DEFAULT NULL,
  `operating_hours` varchar(100) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `parent_company` varchar(255) DEFAULT NULL,
  `additional_contact` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `logo`, `banner`, `logo_alt_text`, `banner_alt_text`, `email`, `mobile1`, `mobile2`, `website`, `industry`, `tax_id`, `linkedin`, `facebook`, `twitter`, `founded_year`, `employee_count`, `status`, `description`, `operating_hours`, `address1`, `address2`, `latitude`, `longitude`, `parent_company`, `additional_contact`) VALUES
(10, '1solutions', 'file.enc', 'image-500x500 (1).jpg', '', '', 'hr@1solutions.biz', '8173033512', '8173033512', 'https://1solutions.biz/', 'Digital Marketing ', '3456', 'https://1solutions.biz/', 'https://1solutions.biz/', 'https://1solutions.biz/', 2014, 30, 'active', 'Google Ads helps businesses connect with the right audience at the right time. It’s a smart way to drive traffic, generate leads, and increase sales by targeting people actively searching for what you offer.\r\n\r\nWith Google Ads, you can easily track performance and see clear results. Ready to grow your business? Contact us today to get started with a targeted, cost-effective ad strategy.', '10', 'Laxminagar ', 'Laxminagar ', 9999.999999, 9999.999999, 'Expertise pvt ltd', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
