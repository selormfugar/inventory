-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2025 at 08:49 PM
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
-- Database: `stan-inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('paid','unpaid') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `type`, `amount`, `due_date`, `category_id`, `status`, `created_at`) VALUES
(1, 'Electricity Bill', 250.00, '2025-01-31', 1, 'paid', '2025-01-27 13:12:59'),
(2, 'Water Bill', 100.00, '2025-02-05', 1, 'paid', '2025-01-27 13:12:59'),
(3, 'January Salaries', 5000.00, '2025-01-30', 2, 'unpaid', '2025-01-27 13:12:59'),
(4, 'Office Maintenance', 300.00, '2025-02-15', 3, 'paid', '2025-01-27 13:12:59'),
(5, 'trial', -500.00, '2025-02-13', 2, 'paid', '2025-02-02 19:58:07'),
(6, 'Salearsi', 50.00, '2025-02-27', 3, 'paid', '2025-02-05 15:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `bill_categories`
--

CREATE TABLE `bill_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_categories`
--

INSERT INTO `bill_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Utilities', '2025-01-27 13:12:59'),
(2, 'Employee Salaries', '2025-01-27 13:12:59'),
(3, 'Operational Expenses', '2025-01-27 13:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `bill_payments`
--

CREATE TABLE `bill_payments` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_payments`
--

INSERT INTO `bill_payments` (`id`, `bill_id`, `payment_date`, `payment_method`, `created_at`) VALUES
(1, 1, '2025-01-20', 'Bank Transfer', '2025-01-27 13:12:59'),
(2, 3, '2025-01-25', 'Cash', '2025-01-27 13:12:59'),
(3, 4, '2025-02-02', 'Cash', '2025-02-02 17:07:59'),
(4, 2, '2025-02-02', 'Cash', '2025-02-02 19:47:31'),
(5, 5, '2025-02-02', 'Cash', '2025-02-02 19:58:21'),
(6, 1, '2025-02-02', 'Bank Transfer', '2025-02-02 19:58:27'),
(7, 6, '2025-02-05', 'Cash', '2025-02-05 15:23:04'),
(8, 6, '2025-02-05', 'Cash', '2025-02-05 15:29:13'),
(9, 6, '2025-02-05', 'Cash', '2025-02-05 15:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Electronics', '2025-01-27 13:12:59'),
(2, 'Furniture', '2025-01-27 13:12:59'),
(3, 'Stationery', '2025-01-27 13:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_number` varchar(50) NOT NULL,
  `invoice_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('paid','unpaid') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `customer_name`, `customer_number`, `invoice_date`, `total_amount`, `status`) VALUES
(1, 'John Doe', 'STF001', '2025-01-27 13:12:59', 230.00, 'paid'),
(2, 'Jane Smith', 'NID123456789', '2025-01-27 13:12:59', 45.00, 'paid'),
(3, 'joe', '1212122', '2025-02-02 19:55:20', 4000.00, 'unpaid'),
(4, 'Kwamena', 'FGa111', '2025-02-05 19:02:10', 6000.00, 'unpaid'),
(5, 'Kwamena', 'FGa111', '2025-02-05 19:06:55', 6000.00, 'unpaid'),
(6, 'Joespko', 'gha123455', '2025-02-05 19:08:14', 7950.00, 'unpaid'),
(7, 'Joespko', 'gha123455', '2025-02-05 19:11:11', 7950.00, 'unpaid'),
(8, 'Joespko', 'gha123455', '2025-02-05 19:19:16', 7950.00, 'unpaid'),
(9, 'Joespko', 'gha123455', '2025-02-05 19:21:09', 7950.00, 'unpaid'),
(10, 'Joespko', 'gha123455', '2025-02-05 19:22:07', 7950.00, 'unpaid'),
(11, 'Joespko', 'gha123455', '2025-02-05 19:23:50', 7950.00, 'unpaid'),
(12, 'Joespko', 'gha123455', '2025-02-05 19:23:59', 7950.00, 'unpaid'),
(13, 'Joespko', 'gha123455', '2025-02-05 19:25:21', 7950.00, 'unpaid'),
(14, '33', '33', '2025-02-05 19:36:21', 1199.99, 'unpaid'),
(15, '33', '33', '2025-02-05 19:36:27', 1199.99, 'unpaid'),
(16, '33', '33', '2025-02-05 19:37:10', 1199.99, 'unpaid'),
(17, '33', '33', '2025-02-05 19:37:17', 1199.99, 'unpaid'),
(18, '33', '33', '2025-02-05 19:37:24', 1199.99, 'unpaid'),
(19, '33', '33', '2025-02-05 19:38:28', 1199.99, 'unpaid'),
(20, '33', '33', '2025-02-05 19:38:34', 1199.99, 'unpaid'),
(21, '444', '4', '2025-02-05 19:39:35', 130.00, 'unpaid'),
(22, '444', '4', '2025-02-05 19:39:40', 130.00, 'unpaid'),
(23, '444', '4', '2025-02-05 19:41:20', 130.00, 'unpaid'),
(24, 'rrr', 'rrr', '2025-02-05 19:41:45', 6000.00, 'unpaid'),
(25, 'rrr', 'rr', '2025-02-05 19:41:54', 4800.00, 'unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('stock','bill') NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` enum('read','unread') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `status`, `created_at`) VALUES
(1, 1, 'stock', 'Low stock alert for product: Laptop', 'unread', '2025-01-27 13:12:59'),
(2, 1, 'bill', 'Upcoming bill payment: Electricity Bill due on 2025-01-31', 'unread', '2025-01-27 13:12:59'),
(3, 2, 'bill', 'Bill payment recorded for January Salaries', 'read', '2025-01-27 13:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `category_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `sku`, `category_id`, `supplier_id`, `price`, `stock`, `expiry_date`, `created_at`) VALUES
(1, 'Laptop', 'EL-1001', 1, 1, 1200.00, 19984, NULL, '2025-01-27 13:12:59'),
(2, 'Office Chair', 'FR-2001', 2, 2, 150.00, 500, NULL, '2025-01-27 13:12:59'),
(3, 'Notebook Pack', 'ST-3001', 3, 2, 10.00, 5292, NULL, '2025-01-27 13:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_id`, `product_id`, `quantity`, `unit_price`, `created_at`) VALUES
(1, 1, 1, 1, 1200.00, '2025-01-27 13:12:59'),
(2, 1, 2, 1, 150.00, '2025-01-27 13:12:59'),
(3, 2, 3, 3, 10.00, '2025-01-27 13:12:59'),
(4, 3, 2, 25, 150.00, '2025-02-02 19:55:20'),
(5, 3, 3, 25, 10.00, '2025-02-02 19:55:20'),
(6, 4, 1, 5, 1200.00, '2025-02-05 19:02:10'),
(7, 5, 1, 5, 1200.00, '2025-02-05 19:06:55'),
(8, 6, 3, 25, 10.00, '2025-02-05 19:08:14'),
(9, 6, 2, 50, 150.00, '2025-02-05 19:08:14'),
(10, 6, 3, 20, 10.00, '2025-02-05 19:08:14'),
(11, 7, 3, 25, 10.00, '2025-02-05 19:11:11'),
(12, 7, 2, 50, 150.00, '2025-02-05 19:11:11'),
(13, 7, 3, 20, 10.00, '2025-02-05 19:11:11'),
(14, 8, 3, 25, 10.00, '2025-02-05 19:19:16'),
(15, 8, 2, 50, 150.00, '2025-02-05 19:19:16'),
(16, 8, 3, 20, 10.00, '2025-02-05 19:19:16'),
(17, 9, 3, 25, 10.00, '2025-02-05 19:21:09'),
(18, 9, 2, 50, 150.00, '2025-02-05 19:21:09'),
(19, 9, 3, 20, 10.00, '2025-02-05 19:21:09'),
(20, 10, 3, 25, 10.00, '2025-02-05 19:22:07'),
(21, 10, 2, 50, 150.00, '2025-02-05 19:22:07'),
(22, 10, 3, 20, 10.00, '2025-02-05 19:22:07'),
(23, 11, 3, 25, 10.00, '2025-02-05 19:23:50'),
(24, 11, 2, 50, 150.00, '2025-02-05 19:23:50'),
(25, 11, 3, 20, 10.00, '2025-02-05 19:23:50'),
(26, 12, 3, 25, 10.00, '2025-02-05 19:23:59'),
(27, 12, 2, 50, 150.00, '2025-02-05 19:23:59'),
(28, 12, 3, 20, 10.00, '2025-02-05 19:23:59'),
(29, 13, 3, 25, 10.00, '2025-02-05 19:25:21'),
(30, 13, 2, 50, 150.00, '2025-02-05 19:25:21'),
(31, 13, 3, 20, 10.00, '2025-02-05 19:25:21'),
(32, 14, 1, 1, 1199.99, '2025-02-05 19:36:21'),
(33, 15, 1, 1, 1199.99, '2025-02-05 19:36:27'),
(34, 16, 1, 1, 1199.99, '2025-02-05 19:37:10'),
(35, 17, 1, 1, 1199.99, '2025-02-05 19:37:17'),
(36, 18, 1, 1, 1199.99, '2025-02-05 19:37:24'),
(37, 19, 1, 1, 1199.99, '2025-02-05 19:38:28'),
(38, 20, 1, 1, 1199.99, '2025-02-05 19:38:34'),
(39, 21, 3, 13, 10.00, '2025-02-05 19:39:35'),
(40, 22, 3, 13, 10.00, '2025-02-05 19:39:40'),
(41, 23, 3, 13, 10.00, '2025-02-05 19:41:20'),
(42, 24, 1, 5, 1200.00, '2025-02-05 19:41:45'),
(43, 25, 1, 4, 1200.00, '2025-02-05 19:41:54');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `type`, `quantity`, `reason`, `created_at`) VALUES
(1, 1, 'in', 10, 'Initial stock', '2025-01-27 13:12:59'),
(2, 2, 'in', 25, 'Initial stock', '2025-01-27 13:12:59'),
(3, 3, 'in', 50, 'Initial stock', '2025-01-27 13:12:59'),
(4, 1, 'out', 2, 'Sold', '2025-01-27 13:12:59'),
(5, 2, 'out', 1, 'Damaged', '2025-01-27 13:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `contact_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `contact_details`, `created_at`) VALUES
(1, 'Tech Supplies Co.', 'www', '734748115', 'josefsfugar@gmail.com', 'Selorm', 'techsupplies@example.com, +123456789', '2025-01-27 13:12:59'),
(2, 'Office Essentials Ltd.', '', '0244226398', 'raymondhowusu770@gmail.com', 'Community 5 F/ 45', 'officeessentials@example.com, +987654321', '2025-01-27 13:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin_user', 'hashed_password_1', 'admin', '2025-01-27 13:12:59'),
(2, 'manager_user', 'hashed_password_2', 'manager', '2025-01-27 13:12:59'),
(3, 'staff_user', 'hashed_password_3', 'staff', '2025-01-27 13:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `full_name`, `email`, `phone`, `address`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 2, 'kwame ', 'keamr@gmail.com', '0258963214', 'tema', NULL, '2025-02-02 17:50:23', '2025-02-02 17:50:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `bill_categories`
--
ALTER TABLE `bill_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `bill_payments`
--
ALTER TABLE `bill_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_id` (`user_id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bill_categories`
--
ALTER TABLE `bill_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bill_payments`
--
ALTER TABLE `bill_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `bill_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_payments`
--
ALTER TABLE `bill_payments`
  ADD CONSTRAINT `bill_payments_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
