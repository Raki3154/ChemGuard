-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 02:25 PM
-- Server version: 8.0.41
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chemguard`
--

-- --------------------------------------------------------

--
-- Table structure for table `boiler_data`
--

CREATE TABLE `boiler_data` (
  `id` int NOT NULL,
  `timestamp` datetime NOT NULL,
  `boiler_id` varchar(50) NOT NULL,
  `temperature` decimal(8,2) NOT NULL,
  `pressure` decimal(8,2) NOT NULL,
  `efficiency` decimal(5,2) NOT NULL,
  `pH` decimal(4,2) NOT NULL,
  `flow_control` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `boiler_data`
--

INSERT INTO `boiler_data` (`id`, `timestamp`, `boiler_id`, `temperature`, `pressure`, `efficiency`, `pH`, `flow_control`) VALUES
(1, '2025-09-26 10:05:00', 'PC-Boiler01', 520.45, 28.34, 88.23, 6.92, 32),
(2, '2025-09-26 10:10:00', 'PC-Boiler02', 512.78, 30.12, 85.67, 7.05, 40),
(3, '2025-09-26 10:15:00', 'PC-Boiler03', 498.63, 29.88, 90.12, 6.80, 27),
(4, '2025-09-26 10:20:00', 'PC-Boiler04', 505.21, 31.25, 87.45, 6.75, 35),
(5, '2025-09-26 10:25:00', 'PC-Boiler05', 528.66, 28.95, 89.90, 6.98, 45),
(6, '2025-09-26 10:30:00', 'PC-Boiler06', 514.22, 27.50, 91.20, 7.01, 38),
(7, '2025-09-26 10:35:00', 'PC-Boiler07', 500.11, 26.89, 84.56, 6.88, 42),
(8, '2025-09-26 10:40:00', 'PC-Boiler08', 530.44, 32.10, 92.33, 6.70, 28),
(9, '2025-09-26 10:45:00', 'PC-Boiler09', 515.67, 29.44, 86.78, 7.02, 31),
(10, '2025-09-26 10:50:00', 'PC-Boiler10', 522.89, 27.91, 90.65, 6.95, 37),
(11, '2025-09-26 10:55:00', 'PC-Boiler11', 499.56, 28.67, 85.34, 6.99, 29),
(12, '2025-09-26 11:00:00', 'PC-Boiler12', 507.33, 30.45, 89.11, 6.85, 36),
(13, '2025-09-26 11:05:00', 'PC-Boiler13', 519.74, 31.10, 87.92, 7.03, 33),
(14, '2025-09-26 11:10:00', 'PC-Boiler14', 525.20, 29.77, 90.34, 6.78, 41),
(15, '2025-09-26 11:15:00', 'PC-Boiler15', 510.98, 27.88, 88.05, 7.10, 39),
(16, '2025-09-26 11:20:00', 'PC-Boiler16', 532.12, 30.67, 91.44, 6.82, 34),
(17, '2025-09-26 11:25:00', 'PC-Boiler17', 501.87, 28.20, 86.11, 6.93, 26),
(18, '2025-09-26 11:30:00', 'PC-Boiler18', 517.55, 29.99, 89.66, 7.07, 43),
(19, '2025-09-26 11:35:00', 'PC-Boiler19', 523.40, 30.33, 92.12, 6.74, 30),
(20, '2025-09-26 11:40:00', 'PC-Boiler20', 508.66, 28.11, 85.89, 6.97, 35),
(21, '2025-09-26 11:45:00', 'PC-Boiler21', 521.99, 27.77, 88.67, 7.06, 32),
(22, '2025-09-26 11:50:00', 'PC-Boiler22', 529.11, 31.40, 91.78, 6.81, 36),
(23, '2025-09-26 11:55:00', 'PC-Boiler23', 503.42, 28.65, 87.34, 6.90, 29),
(24, '2025-09-26 12:00:00', 'PC-Boiler24', 511.20, 29.12, 89.22, 7.00, 37),
(25, '2025-09-26 12:05:00', 'PC-Boiler25', 518.77, 27.55, 90.91, 6.87, 40),
(26, '2025-09-26 12:10:00', 'PC-Boiler26', 526.14, 30.70, 92.56, 6.79, 31),
(27, '2025-09-26 12:15:00', 'PC-Boiler27', 504.88, 28.45, 84.99, 7.08, 28),
(28, '2025-09-26 12:20:00', 'PC-Boiler28', 516.33, 29.67, 86.78, 6.94, 42),
(29, '2025-09-26 12:25:00', 'PC-Boiler29', 524.50, 30.90, 90.56, 6.76, 33),
(30, '2025-09-26 12:30:00', 'PC-Boiler30', 509.99, 27.89, 88.12, 7.09, 39);

-- --------------------------------------------------------

--
-- Table structure for table `plant_details`
--

CREATE TABLE `plant_details` (
  `id` int NOT NULL,
  `plant_name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `capacity` varchar(100) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plant_details`
--

INSERT INTO `plant_details` (`id`, `plant_name`, `location`, `capacity`, `contact_email`, `contact_phone`, `created_at`) VALUES
(1, 'Main Boiler Plant', 'Industrial Zone A', '500 MW', 'admin@chemguard.com', '+1-555-0123', '2025-09-30 15:03:56');

-- --------------------------------------------------------

--
-- Table structure for table `registration_logs`
--

CREATE TABLE `registration_logs` (
  `log_id` int NOT NULL,
  `plant_id` int NOT NULL,
  `industry_type` varchar(100) NOT NULL,
  `fuel_type` varchar(100) NOT NULL,
  `capacity` decimal(10,2) NOT NULL,
  `registration_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `registration_logs`
--



-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `plant_name` varchar(100) DEFAULT NULL,
  `plant_id` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `industry_type` varchar(50) DEFAULT NULL,
  `fuel_type` varchar(50) DEFAULT NULL,
  `boiler_capacity` varchar(50) DEFAULT NULL,
  `sustainability_score` int DEFAULT '50',
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--



--
-- Indexes for dumped tables
--

--
-- Indexes for table `boiler_data`
--
ALTER TABLE `boiler_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plant_details`
--
ALTER TABLE `plant_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plant_name` (`plant_name`);

--
-- Indexes for table `registration_logs`
--
ALTER TABLE `registration_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_plant_id` (`plant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boiler_data`
--
ALTER TABLE `boiler_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `plant_details`
--
ALTER TABLE `plant_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registration_logs`
--
ALTER TABLE `registration_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
