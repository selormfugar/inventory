-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2025 at 10:04 PM
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
(3, 'January Salaries', 5000.00, '2025-01-30', 2, 'paid', '2025-01-27 13:12:59'),
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
(9, 6, '2025-02-05', 'Cash', '2025-02-05 15:46:40'),
(10, 3, '2025-02-14', 'Bank Transfer', '2025-02-14 20:08:24');

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
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `customer_name`, `customer_number`, `invoice_date`, `total_amount`) VALUES
(5, 'Joespko', 'GHA12345', '2025-02-14 11:07:56', 6000.00),
(10, 'Test Name', '123456789', '2025-02-14 11:35:59', 100.00),
(11, 'Test Name', '123456789', '2025-02-14 11:36:32', 100.00),
(15, 'Test Name', '123456789', '2025-02-14 11:37:36', 100.00),
(16, 'rrr', 'GHA12345', '2025-02-14 11:43:07', 30.00),
(17, 'ttt', 'GHA12345', '2025-02-14 19:55:05', 9600.00),
(18, 'ttt', 'GHA12345', '2025-02-14 19:57:22', 9600.00),
(19, 'ttt', 'GHA12345', '2025-02-14 19:57:29', 9600.00),
(20, 'ttt', 'GHA12345', '2025-02-14 19:57:49', 80.00),
(21, 'Joespko', 'GHA12345', '2025-02-14 20:04:43', 50.00),
(22, 'ttt', 'FGa111', '2025-02-16 12:37:37', 6000.00),
(23, 'Joespko', 'GHA12345', '2025-02-16 12:38:38', 3650.00),
(24, 'Joespko', 'FGa111', '2025-02-16 12:46:46', 450.00),
(25, 'Joespko', 'GHA12345', '2025-02-16 12:49:42', 4800.00),
(26, 'Joespko', 'GHA12345', '2025-02-16 13:59:17', 5260.00),
(27, 'ttt', 'GHA12345', '2025-02-16 16:02:04', 4800.00),
(28, 'Joespko', 'GHA12345', '2025-02-16 17:01:12', 1200.00),
(29, 'Joespko', 'GHA12345', '2025-02-16 17:02:46', 1350.00),
(30, 'Joespko', 'GHA12345', '2025-02-16 17:03:39', 13600.00);

-- --------------------------------------------------------

--
-- Table structure for table `new_invoice_order`
--

CREATE TABLE `new_invoice_order` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_invoice_order`
--

INSERT INTO `new_invoice_order` (`id`, `invoice_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(1, 5, 1, 5, 1200.00, 6000.00, '2025-02-14 11:07:56'),
(2, 11, 1, 2, 50.00, 100.00, '2025-02-14 11:36:32'),
(3, 15, 1, 2, 50.00, 100.00, '2025-02-14 11:37:36'),
(4, 16, 3, 3, 10.00, 30.00, '2025-02-14 11:43:07'),
(5, 17, 1, 8, 1200.00, 9600.00, '2025-02-14 19:55:05'),
(6, 18, 1, 8, 1200.00, 9600.00, '2025-02-14 19:57:22'),
(7, 19, 1, 8, 1200.00, 9600.00, '2025-02-14 19:57:29'),
(8, 20, 3, 8, 10.00, 80.00, '2025-02-14 19:57:49'),
(9, 21, 3, 5, 10.00, 50.00, '2025-02-14 20:04:43'),
(10, 22, 1, 5, 1200.00, 6000.00, '2025-02-16 12:37:37'),
(11, 23, 1, 3, 1200.00, 3600.00, '2025-02-16 12:38:38'),
(12, 23, 3, 5, 10.00, 50.00, '2025-02-16 12:38:38'),
(13, 24, 2, 3, 150.00, 450.00, '2025-02-16 12:46:46'),
(14, 25, 1, 4, 1200.00, 4800.00, '2025-02-16 12:49:42'),
(15, 26, 1, 4, 1200.00, 4800.00, '2025-02-16 13:59:17'),
(16, 26, 2, 2, 150.00, 300.00, '2025-02-16 13:59:17'),
(17, 26, 3, 2, 10.00, 20.00, '2025-02-16 13:59:17'),
(18, 26, 6, 2, 50.00, 100.00, '2025-02-16 13:59:17'),
(19, 26, 3, 4, 10.00, 40.00, '2025-02-16 13:59:17'),
(20, 27, 1, 4, 1200.00, 4800.00, '2025-02-16 16:02:04'),
(21, 28, 1, 1, 1200.00, 1200.00, '2025-02-16 17:01:12'),
(22, 29, 1, 1, 1200.00, 1200.00, '2025-02-16 17:02:46'),
(23, 29, 2, 1, 150.00, 150.00, '2025-02-16 17:02:46'),
(24, 30, 2, 10, 150.00, 1500.00, '2025-02-16 17:03:39'),
(25, 30, 3, 10, 10.00, 100.00, '2025-02-16 17:03:39'),
(26, 30, 1, 10, 1200.00, 12000.00, '2025-02-16 17:03:39');

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
(1, 'Laptop', 'EL-1001', 1, 1, 1200.00, 199452, NULL, '2025-01-27 13:12:59'),
(2, 'Office Chair', 'FR-2001', 2, 2, 150.00, 385, NULL, '2025-01-27 13:12:59'),
(3, 'Notebook Pack', 'ST-3001', 3, 2, 10.00, 1961, NULL, '2025-01-27 13:12:59'),
(6, 'John Deer', 'JD 123', 3, 2, 50.00, 994, NULL, '2025-02-07 10:24:20');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_number` varchar(50) NOT NULL,
  `invoice_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `customer_name`, `customer_number`, `invoice_date`, `total_amount`) VALUES
(1, 0, 'Joespko', 'FGa111', '2025-02-13 18:32:07', 8400.00),
(2, 0, 'Joespko', 'FGa111', '2025-02-13 18:33:58', 8400.00),
(3, 0, 'Joespko', 'FGa111', '2025-02-13 18:34:11', 8400.00),
(4, 0, 'Joespko', 'FGa111', '2025-02-13 18:35:04', 8400.00),
(5, 0, 'Joespko', 'FGa111', '2025-02-13 18:38:09', 8400.00),
(6, 0, 'Joespko', 'FGa111', '2025-02-13 18:38:15', 8400.00),
(7, 0, 'Joespko', 'FGa111', '2025-02-13 18:39:02', 8400.00),
(8, 0, 'Joespko', 'GHA12345', '2025-02-13 18:39:29', 22800.00),
(9, 0, 'Joespko', 'GHA12345', '2025-02-13 18:40:20', 22800.00),
(10, 0, 'Kwameena`', 'GHA12345', '2025-02-13 18:40:37', 6000.00),
(11, 0, 'Kwameena`', 'GHA12345', '2025-02-13 18:46:44', 6000.00),
(12, 0, 'Kwameena`', 'GHA12345', '2025-02-13 18:48:25', 6000.00),
(13, 0, 'test 2', 'gha123455', '2025-02-14 09:27:38', 6000.00),
(14, 0, 'Joespko', 'FGa111', '2025-02-14 11:11:40', 51120.00),
(15, 0, 'Joespko', 'GHA12345', '2025-02-14 11:12:00', 40.00),
(16, 0, 'Kwameena`', 'GHA12345', '2025-02-14 11:13:30', 850.00),
(17, 0, 'Kwameena`', 'FGa111', '2025-02-14 12:01:02', 450.00),
(18, 0, 'Joespko', 'FGa111', '2025-02-14 20:05:00', 6000.00),
(19, 0, 'Joespko', 'FGa111', '2025-02-14 20:13:46', 6000.00),
(20, 0, 'ttt', 'FGa111', '2025-02-14 20:13:57', 3600.00),
(21, 0, 'wssss', 'ss', '2025-02-14 20:21:35', 750.00),
(22, 0, 'Jephthah', 'GIYY1234', '2025-02-15 11:53:56', 7950.00),
(23, 3, 'trial user', 'GHA12345', '2025-02-15 12:42:55', 6000.00),
(25, 3, 'Joespko', 'FGa111', '2025-02-15 13:15:02', 8400.00),
(26, 3, 'Joespko', 'GHA12345', '2025-02-15 13:50:40', 9600.00),
(27, 3, 'Joespko', 'GHA12345', '2025-02-16 12:35:52', 4800.00),
(28, 3, 'Joespko', 'FGa111', '2025-02-16 12:46:26', 2400.00),
(29, 3, 'Joespko', 'GHA12345', '2025-02-16 12:50:05', 450.00),
(30, 3, 'Joespko', 'GHA12345', '2025-02-16 12:56:07', 3600.00),
(31, 3, 'Joespko', 'GHA12345', '2025-02-16 13:24:24', 4800.00),
(32, 3, 'Joespko', 'GHA12345', '2025-02-16 13:26:14', 9600.00),
(33, 3, 'Joespko', 'GHA12345', '2025-02-16 13:58:09', 8400.00),
(34, 3, 'Joespko', 'GHA12345', '2025-02-16 14:02:25', 2400.00),
(35, 3, 'Joespko', 'GHA12345', '2025-02-16 14:04:10', 450.00),
(36, 3, 'Joespko', 'FGa111', '2025-02-16 14:49:55', 6000.00),
(37, 3, 'ttt', 'GHA12345', '2025-02-16 15:02:38', 2400.00),
(38, 2, 'Joespko', 'GHA12345', '2025-02-16 15:49:39', 3600.00),
(39, 2, 'Joespko', 'GHA12345', '2025-02-16 15:50:13', 4800.00),
(40, 1, 'Joespko', 'GHA12345', '2025-02-16 15:50:39', 6000.00),
(41, 2, 'Joespko', 'GHA12345', '2025-02-16 15:51:41', 12000.00),
(42, 2, 'ttt', 'GHA12345', '2025-02-16 16:02:55', 3600.00),
(43, 3, 'Joespko', 'GHA12345', '2025-02-16 16:03:27', 4800.00),
(44, 3, 'Joespko', 'GHA12345', '2025-02-16 16:04:53', 6000.00),
(45, 3, 'Joespko', 'GHA12345', '2025-02-16 16:05:39', 600.00),
(46, 3, 'Joespko', 'GHA12345', '2025-02-16 16:06:08', 3600.00),
(47, 3, 'Joespko', 'GHA12345', '2025-02-16 16:06:52', 4250.00),
(48, 3, 'Joespko', 'GHA12345', '2025-02-16 16:08:29', 3600.00),
(49, 3, 'Joespko', 'GHA12345', '2025-02-16 16:09:43', 4800.00),
(50, 3, 'Joespko', 'GHA12345', '2025-02-16 16:12:21', 1500.00),
(51, 3, 'Joespko', 'GHA12345', '2025-02-16 16:31:04', 1200.00),
(52, 3, 'Joespko', 'GHA12345', '2025-02-16 16:36:14', 1350.00),
(53, 3, 'ttt', 'GHA12345', '2025-02-16 16:49:13', 6000.00),
(60, 3, 'Joespko', 'GHA12345', '2025-02-16 17:10:44', 3600.00);

-- --------------------------------------------------------

--
-- Table structure for table `sale_order`
--

CREATE TABLE `sale_order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_order`
--

INSERT INTO `sale_order` (`id`, `user_id`, `sales_id`, `product_id`, `quantity`, `unit_price`, `created_at`) VALUES
(1, 0, 1, 1, 7, 1200.00, '2025-02-13 18:32:07'),
(2, 0, 2, 1, 7, 1200.00, '2025-02-13 18:33:58'),
(3, 0, 3, 1, 7, 1200.00, '2025-02-13 18:34:11'),
(4, 0, 4, 1, 7, 1200.00, '2025-02-13 18:35:04'),
(5, 0, 5, 1, 7, 1200.00, '2025-02-13 18:38:09'),
(6, 0, 6, 1, 7, 1200.00, '2025-02-13 18:38:15'),
(7, 0, 7, 1, 7, 1200.00, '2025-02-13 18:39:02'),
(8, 0, 8, 1, 18, 1200.00, '2025-02-13 18:39:29'),
(9, 0, 8, 2, 8, 150.00, '2025-02-13 18:39:29'),
(10, 0, 9, 1, 18, 1200.00, '2025-02-13 18:40:20'),
(11, 0, 9, 2, 8, 150.00, '2025-02-13 18:40:20'),
(12, 0, 10, 1, 5, 1200.00, '2025-02-13 18:40:37'),
(13, 0, 11, 1, 5, 1200.00, '2025-02-13 18:46:44'),
(14, 0, 12, 1, 5, 1200.00, '2025-02-13 18:48:25'),
(15, 0, 13, 1, 5, 1200.00, '2025-02-14 09:27:38'),
(16, 0, 14, 1, 9, 1200.00, '2025-02-14 11:11:40'),
(17, 0, 14, 2, 50, 150.00, '2025-02-14 11:11:40'),
(18, 0, 14, 3, 3282, 10.00, '2025-02-14 11:11:40'),
(19, 0, 15, 3, 4, 10.00, '2025-02-14 11:12:00'),
(20, 0, 16, 3, 6, 10.00, '2025-02-14 11:13:30'),
(21, 0, 16, 6, 11, 50.00, '2025-02-14 11:13:30'),
(22, 0, 16, 3, 24, 10.00, '2025-02-14 11:13:30'),
(23, 0, 17, 2, 3, 150.00, '2025-02-14 12:01:02'),
(24, 0, 18, 1, 5, 1200.00, '2025-02-14 20:05:00'),
(25, 0, 19, 1, 5, 1200.00, '2025-02-14 20:13:46'),
(26, 0, 20, 1, 3, 1200.00, '2025-02-14 20:13:57'),
(27, 0, 21, 2, 5, 150.00, '2025-02-14 20:21:35'),
(28, 0, 22, 1, 6, 1200.00, '2025-02-15 11:53:56'),
(29, 0, 22, 2, 5, 150.00, '2025-02-15 11:53:56'),
(30, 3, 23, 1, 5, 1200.00, '2025-02-15 12:42:55'),
(31, 3, 25, 1, 7, 1200.00, '2025-02-15 13:15:02'),
(32, 3, 26, 1, 3, 1200.00, '2025-02-15 13:50:41'),
(33, 3, 26, 1, 5, 1200.00, '2025-02-15 13:50:41'),
(34, 3, 27, 1, 4, 1200.00, '2025-02-16 12:35:52'),
(35, 3, 28, 1, 2, 1200.00, '2025-02-16 12:46:26'),
(36, 3, 29, 2, 3, 150.00, '2025-02-16 12:50:05'),
(37, 3, 30, 1, 3, 1200.00, '2025-02-16 12:56:07'),
(38, 3, 31, 1, 4, 1200.00, '2025-02-16 13:24:24'),
(39, 3, 32, 1, 8, 1200.00, '2025-02-16 13:26:14'),
(40, 3, 33, 1, 7, 1200.00, '2025-02-16 13:58:09'),
(41, 3, 34, 1, 2, 1200.00, '2025-02-16 14:02:25'),
(42, 3, 35, 2, 3, 150.00, '2025-02-16 14:04:10'),
(43, 3, 36, 1, 5, 1200.00, '2025-02-16 14:49:55'),
(44, 3, 37, 1, 2, 1200.00, '2025-02-16 15:02:38'),
(45, 2, 38, 1, 3, 1200.00, '2025-02-16 15:49:39'),
(46, 2, 39, 1, 4, 1200.00, '2025-02-16 15:50:13'),
(47, 1, 40, 1, 5, 1200.00, '2025-02-16 15:50:39'),
(48, 2, 41, 1, 10, 1200.00, '2025-02-16 15:51:41'),
(49, 2, 42, 1, 3, 1200.00, '2025-02-16 16:02:55'),
(50, 3, 43, 1, 4, 1200.00, '2025-02-16 16:03:27'),
(51, 3, 44, 1, 5, 1200.00, '2025-02-16 16:04:53'),
(52, 3, 45, 2, 4, 150.00, '2025-02-16 16:05:39'),
(53, 3, 46, 1, 3, 1200.00, '2025-02-16 16:06:08'),
(54, 3, 47, 1, 3, 1200.00, '2025-02-16 16:06:52'),
(55, 3, 47, 2, 4, 150.00, '2025-02-16 16:06:52'),
(56, 3, 47, 3, 5, 10.00, '2025-02-16 16:06:52'),
(57, 3, 48, 1, 3, 1200.00, '2025-02-16 16:08:29'),
(58, 3, 49, 1, 4, 1200.00, '2025-02-16 16:09:43'),
(59, 3, 50, 2, 10, 150.00, '2025-02-16 16:12:21'),
(60, 3, 51, 2, 8, 150.00, '2025-02-16 16:31:04'),
(61, 3, 52, 2, 9, 150.00, '2025-02-16 16:36:14'),
(62, 3, 53, 1, 5, 1200.00, '2025-02-16 16:49:13'),
(75, 3, 60, 1, 3, 1200.00, '2025-02-16 17:10:44');

--
-- Triggers `sale_order`
--
DELIMITER $$
CREATE TRIGGER `after_sale_insert` AFTER INSERT ON `sale_order` FOR EACH ROW BEGIN
    DECLARE current_stock INT;

    -- Get current stock for the product
    SELECT SUM(quantity) INTO current_stock 
    FROM stock_movements 
    WHERE product_id = NEW.product_id;

    -- Prevent negative stock
    IF current_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Not enough stock available';
    ELSE
        -- Insert stock movement record
        INSERT INTO stock_movements (product_id, user_id, type, quantity, reason, created_at)
        VALUES (NEW.product_id, NEW.user_id, 'out', NEW.quantity, 'Sale', NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `user_id`, `product_id`, `type`, `quantity`, `reason`, `created_at`) VALUES
(1, 0, 1, 'in', 10, 'Initial stock', '2025-01-27 13:12:59'),
(2, 0, 2, 'in', 25, 'Initial stock', '2025-01-27 13:12:59'),
(3, 0, 3, 'in', 50, 'Initial stock', '2025-01-27 13:12:59'),
(4, 0, 1, 'out', 2, 'Sold', '2025-01-27 13:12:59'),
(5, 0, 2, 'out', 1, 'Damaged', '2025-01-27 13:12:59'),
(6, 0, 1, 'out', 6, 'Sale', '2025-02-13 17:50:43'),
(7, 0, 1, 'out', 6, 'Sale', '2025-02-13 17:50:43'),
(8, 0, 1, 'out', 7, 'Sale', '2025-02-13 18:32:07'),
(9, 0, 1, 'out', 7, 'Sale', '2025-02-13 18:33:58'),
(10, 0, 1, 'out', 7, 'Sale', '2025-02-13 18:34:11'),
(11, 0, 1, 'out', 7, 'Sale', '2025-02-13 18:35:04'),
(12, 0, 1, 'out', 7, 'Sale', '2025-02-13 18:38:09'),
(13, 0, 1, 'out', 7, 'Sale', '2025-02-13 18:38:15'),
(14, 0, 1, 'out', 7, 'Sale', '2025-02-13 18:39:02'),
(15, 0, 1, 'out', 18, 'Sale', '2025-02-13 18:39:29'),
(16, 0, 2, 'out', 8, 'Sale', '2025-02-13 18:39:29'),
(17, 0, 1, 'out', 18, 'Sale', '2025-02-13 18:40:20'),
(18, 0, 2, 'out', 8, 'Sale', '2025-02-13 18:40:20'),
(19, 0, 1, 'out', 5, 'Sale', '2025-02-13 18:40:37'),
(20, 0, 1, 'out', 5, 'Sale', '2025-02-13 18:46:44'),
(21, 0, 1, 'out', 5, 'Sale', '2025-02-13 18:48:25'),
(22, 0, 1, 'out', 5, 'Sale', '2025-02-14 09:27:38'),
(23, 0, 1, 'out', 9, 'Sale', '2025-02-14 11:11:40'),
(24, 0, 2, 'out', 50, 'Sale', '2025-02-14 11:11:40'),
(25, 0, 3, 'out', 3282, 'Sale', '2025-02-14 11:11:40'),
(26, 0, 3, 'out', 4, 'Sale', '2025-02-14 11:12:00'),
(27, 0, 3, 'out', 6, 'Sale', '2025-02-14 11:13:30'),
(28, 0, 6, 'out', 11, 'Sale', '2025-02-14 11:13:30'),
(29, 0, 3, 'out', 24, 'Sale', '2025-02-14 11:13:30'),
(30, 0, 2, 'out', 3, 'Sale', '2025-02-14 12:01:02'),
(31, 0, 1, 'out', 5, 'Sale', '2025-02-14 20:05:00'),
(32, 0, 1, 'out', 5, 'Sale', '2025-02-14 20:13:46'),
(33, 0, 1, 'out', 3, 'Sale', '2025-02-14 20:13:57'),
(34, 0, 2, 'out', 5, 'Sale', '2025-02-14 20:21:35'),
(35, 0, 1, 'out', 6, 'Sale', '2025-02-15 11:53:56'),
(36, 0, 2, 'out', 5, 'Sale', '2025-02-15 11:53:56'),
(37, 3, 1, 'out', 5, 'Sale', '2025-02-15 12:42:55'),
(38, 3, 1, 'out', 7, 'Sale', '2025-02-15 13:15:02'),
(39, 3, 1, 'out', 7, 'Sale', '2025-02-15 13:15:02'),
(40, 3, 1, 'out', 3, 'Sale', '2025-02-15 13:50:41'),
(41, 3, 1, 'out', 5, 'Sale', '2025-02-15 13:50:41'),
(42, 3, 1, 'out', 4, 'Sale', '2025-02-16 12:35:52'),
(43, 3, 1, 'out', 2, 'Sale', '2025-02-16 12:46:26'),
(44, 3, 2, 'out', 3, 'Sale', '2025-02-16 12:50:05'),
(45, 3, 1, 'out', 3, 'Sale', '2025-02-16 12:56:07'),
(46, 3, 1, 'out', 4, 'Sale', '2025-02-16 13:24:24'),
(47, 3, 1, 'out', 8, 'Sale', '2025-02-16 13:26:14'),
(48, 3, 1, 'out', 7, 'Sale', '2025-02-16 13:58:09'),
(49, 3, 1, 'out', 2, 'Sale', '2025-02-16 14:02:25'),
(50, 3, 2, 'out', 3, 'Sale', '2025-02-16 14:04:10'),
(51, 3, 1, 'out', 5, 'Sale', '2025-02-16 14:49:55'),
(52, 3, 1, 'out', 2, 'Sale', '2025-02-16 15:02:38'),
(53, 2, 1, 'out', 3, 'Sale', '2025-02-16 15:49:39'),
(54, 2, 1, 'out', 4, 'Sale', '2025-02-16 15:50:13'),
(55, 1, 1, 'out', 5, 'Sale', '2025-02-16 15:50:39'),
(56, 2, 1, 'out', 10, 'Sale', '2025-02-16 15:51:41'),
(57, 2, 1, 'out', 3, 'Sale', '2025-02-16 16:02:55'),
(58, 3, 1, 'out', 4, 'Sale', '2025-02-16 16:03:27'),
(59, 3, 1, 'out', 5, 'Sale', '2025-02-16 16:04:53'),
(60, 3, 2, 'out', 4, 'Sale', '2025-02-16 16:05:39'),
(61, 3, 1, 'out', 3, 'Sale', '2025-02-16 16:06:08'),
(62, 3, 1, 'out', 3, 'Sale', '2025-02-16 16:06:52'),
(63, 3, 2, 'out', 4, 'Sale', '2025-02-16 16:06:52'),
(64, 3, 3, 'out', 5, 'Sale', '2025-02-16 16:06:52'),
(65, 3, 1, 'out', 3, 'Sale', '2025-02-16 16:08:29'),
(66, 3, 1, 'out', 4, 'Sale', '2025-02-16 16:09:43'),
(67, 3, 2, 'out', 10, 'Sale', '2025-02-16 16:12:21'),
(68, 3, 2, 'out', 8, 'Sale', '2025-02-16 16:31:04'),
(69, 3, 2, 'out', 9, 'Sale', '2025-02-16 16:36:14'),
(70, 3, 1, 'out', 5, 'Sale', '2025-02-16 16:49:13'),
(77, 3, 1, 'out', 3, 'Sale', '2025-02-16 17:10:44');

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
-- Indexes for table `new_invoice_order`
--
ALTER TABLE `new_invoice_order`
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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_order`
--
ALTER TABLE `sale_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `sales_order` (`sales_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `new_invoice_order`
--
ALTER TABLE `new_invoice_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `sale_order`
--
ALTER TABLE `sale_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

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
-- Constraints for table `sale_order`
--
ALTER TABLE `sale_order`
  ADD CONSTRAINT `sale_order_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_order` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`id`);

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
