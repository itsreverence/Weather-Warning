-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Apr 04, 2024 at 07:43 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_docker`
--

-- --------------------------------------------------------

--
-- Table structure for table `SensorData`
--

CREATE TABLE `SensorData` (
  `id` int UNSIGNED NOT NULL,
  `sensor` varchar(30) NOT NULL,
  `location` varchar(30) NOT NULL,
  `value1` varchar(10) DEFAULT NULL,
  `value2` varchar(10) DEFAULT NULL,
  `reading_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `SensorData`
--

INSERT INTO `SensorData` (`id`, `sensor`, `location`, `value1`, `value2`, `reading_time`) VALUES
(1, 'DHT11', 'Bedroom', '69.50', '34.00', '2024-04-04 19:41:38'),
(2, 'DHT11', 'Bedroom', '69.10', '33.50', '2024-04-04 19:42:09'),
(3, 'DHT11', 'Bedroom', '70.60', '34.50', '2024-04-04 19:42:40'),
(4, 'DHT11', 'Bedroom', '70.00', '33.00', '2024-04-04 19:43:11'),
(5, 'DHT11', 'Bedroom', '69.90', '34.00', '2024-04-04 19:44:42'),
(6, 'DHT11', 'Bedroom', '70.80', '33.10', '2024-04-04 19:45:13'),
(7, 'DHT11', 'Bedroom', '71.00', '35.00', '2024-04-04 19:45:44'),
(8, 'DHT11', 'Bedroom', '69.20', '34.90', '2024-04-04 19:46:15'),
(9, 'DHT11', 'Bedroom', '70.40', '33.40', '2024-04-04 19:46:46'),
(10, 'DHT11', 'Bedroom', '69.80', '34.00', '2024-04-04 19:47:17'),
(11, 'DHT11', 'Bedroom', '70.60', '33.30', '2024-04-04 19:47:48'),
(12, 'DHT11', 'Bedroom', '70.90', '35.00', '2024-04-04 19:48:19'),
(13, 'DHT11', 'Bedroom', '69.30', '34.50', '2024-04-04 19:48:50'),
(14, 'DHT11', 'Bedroom', '70.70', '33.20', '2024-04-04 19:49:21'),
(15, 'DHT11', 'Bedroom', '69.50', '34.80', '2024-04-04 19:49:52'),
(16, 'DHT11', 'Bedroom', '70.95', '33.50', '2024-04-04 19:50:23'),
(17, 'DHT11', 'Bedroom', '69.40', '34.00', '2024-04-04 19:50:54'),
(18, 'DHT11', 'Bedroom', '71.00', '33.00', '2024-04-04 19:51:25'),
(19, 'DHT11', 'Bedroom', '69.70', '35.00', '2024-04-04 19:51:56'),
(20, 'DHT11', 'Bedroom', '70.50', '34.00', '2024-04-04 19:52:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `SensorData`
--
ALTER TABLE `SensorData`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `SensorData`
--
ALTER TABLE `SensorData`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;