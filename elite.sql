-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 12:48 PM
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
-- Database: `elite`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `entry_time` datetime NOT NULL,
  `days` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `buying_price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `entry_time`, `days`, `quantity`, `size`, `buying_price`, `image`) VALUES
(111, 'Apex Men\'s Casual Shoe', 9.00, 'Apex Mens Casual', '0000-00-00 00:00:00', 'Thursday', 300, '40', 9.00, 'uploads/681dba9b492e7_a3.jpg'),
(112, 'Oxford', 900.00, '9000', '2025-05-09 00:00:00', 'Friday', 3, '90', 3000.00, 'uploads/681dbd0668491_a5.jpg'),
(113, 'Oxford', 3000.00, 'Apex Men\'s Casual Shoe', '2025-07-30 00:00:00', 'Wednesday', 3, '40', 2800.00, 'uploads/688a489e9f82e_681b44e6bd710_a1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `role` enum('admin','employee') DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `first_name`, `last_name`, `username`, `email`, `phone`, `district`, `role`, `password`, `created_at`) VALUES
(1, 'Robin', 'Hossain', 'robinhossain70', 'rh503648@gmail.com', '1312427030', 'Manikganj', 'admin', '12341234', '2025-05-14 13:44:30'),
(2, 'Rahat Parves', 'Rahi', 'robin70', 'rahat@gmail.com', '1312427030', 'Sylet', 'admin', '$2y$10$fblOjDSc4jvNo/yOdR3FN.m3/Ir7iNXHqVgAWn2l2K5gSpVS0GC92', '2025-05-14 14:17:47'),
(3, 'Mossarraf Hossain', 'Robin', 'robinhossain73', 'admin@gmail.com', '1312427030', 'Manikganj', 'admin', '$2y$10$GJRRzfKGV6iKlyLNgL22aO5MA12WwjUibzuPIPAF4FVJqPthBsCiy', '2025-07-30 16:28:38');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `sale_time` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
