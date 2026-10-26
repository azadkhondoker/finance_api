-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 04:33 AM
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
-- Database: `finance_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(10) UNSIGNED NOT NULL,
  `asset_type` varchar(20) NOT NULL,
  `title` varchar(150) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `invest_date` date NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  `sale_amount` decimal(12,2) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `asset_type`, `title`, `details`, `amount`, `invest_date`, `status`, `sale_amount`, `sale_date`, `note`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'gold', '24C', '1 bhori', 10000.00, '2026-10-24', 'sold', 15000.00, '2026-11-24', '', '2026-10-24 20:38:50', '2026-10-24 20:39:55', NULL),
(2, 'land', 'Mirpur 1 katha', '1 katha', 10000.00, '2026-10-24', 'sold', 20000.00, '2026-09-29', '', '2026-10-24 20:42:06', '2026-10-24 20:43:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'kamal', 'aksdjfjk', NULL, NULL, NULL, NULL),
(7, 'kamal', 'aksdjfjk', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `expense_at` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `title`, `amount`, `note`, `expense_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'asd', 200.00, '', '2026-10-24 22:20:07', '2026-10-24 22:20:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED DEFAULT NULL,
  `borrower_name` varchar(150) NOT NULL,
  `borrower_phone` varchar(20) DEFAULT NULL,
  `principal` decimal(12,2) NOT NULL,
  `interest_rate` decimal(6,2) NOT NULL DEFAULT 0.00,
  `loan_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `duration_months` int(10) UNSIGNED DEFAULT NULL,
  `repayment_type` varchar(15) DEFAULT NULL,
  `penalty_rate` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `member_id`, `borrower_name`, `borrower_phone`, `principal`, `interest_rate`, `loan_date`, `due_date`, `note`, `created_at`, `updated_at`, `deleted_at`, `duration_months`, `repayment_type`, `penalty_rate`) VALUES
(1, NULL, 'kamal', '01880997983', 2000.00, 0.12, '2026-10-24', NULL, '', '2026-10-24 20:34:06', NULL, NULL, NULL, NULL, NULL),
(2, 5, 'baker', '01389098008', 5000.00, 20.00, '2026-10-25', '2027-07-25', '', '2026-10-25 21:13:12', NULL, '2026-10-25 21:16:00', 9, 'half_monthly', 15.00),
(3, 6, 'hossan', '01936778899', 5000.00, 12.00, '2026-10-25', '2027-03-25', '', '2026-10-25 21:17:03', NULL, NULL, 5, 'half_monthly', 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `loan_installments`
--

CREATE TABLE `loan_installments` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(10) UNSIGNED NOT NULL,
  `installment_no` int(10) UNSIGNED NOT NULL,
  `due_date` date NOT NULL,
  `principal_part` decimal(12,2) NOT NULL,
  `interest_part` decimal(12,2) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'unpaid',
  `paid_date` date DEFAULT NULL,
  `penalty_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_installments`
--

INSERT INTO `loan_installments` (`id`, `loan_id`, `installment_no`, `due_date`, `principal_part`, `interest_part`, `status`, `paid_date`, `penalty_paid`, `payment_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, '2026-11-09', 277.78, 41.67, 'paid', '2026-10-25', 0.00, 3, '2026-10-25 21:13:12', '2026-10-25 21:14:05', NULL),
(2, 2, 2, '2026-11-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(3, 2, 3, '2026-12-10', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(4, 2, 4, '2026-12-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(5, 2, 5, '2027-01-09', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(6, 2, 6, '2027-01-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(7, 2, 7, '2027-02-09', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(8, 2, 8, '2027-02-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(9, 2, 9, '2027-03-12', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(10, 2, 10, '2027-03-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(11, 2, 11, '2027-04-09', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(12, 2, 12, '2027-04-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(13, 2, 13, '2027-05-10', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(14, 2, 14, '2027-05-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(15, 2, 15, '2027-06-09', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(16, 2, 16, '2027-06-25', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(17, 2, 17, '2027-07-10', 277.78, 41.67, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(18, 2, 18, '2027-07-25', 277.74, 41.61, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:13:12', NULL, NULL),
(19, 3, 1, '2026-11-09', 500.00, 25.00, 'paid', '2026-10-25', 0.00, 4, '2026-10-25 21:17:03', '2026-10-25 21:20:41', NULL),
(20, 3, 2, '2026-11-25', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(21, 3, 3, '2026-12-10', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(22, 3, 4, '2026-12-25', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(23, 3, 5, '2027-01-09', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(24, 3, 6, '2027-01-25', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(25, 3, 7, '2027-02-09', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(26, 3, 8, '2027-02-25', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(27, 3, 9, '2027-03-12', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL),
(28, 3, 10, '2027-03-25', 500.00, 25.00, 'unpaid', NULL, 0.00, NULL, '2026-10-25 21:17:03', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

CREATE TABLE `loan_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(10) UNSIGNED NOT NULL,
  `principal_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `interest_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `installment_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_payments`
--

INSERT INTO `loan_payments` (`id`, `loan_id`, `principal_amount`, `interest_amount`, `payment_date`, `note`, `created_at`, `updated_at`, `deleted_at`, `installment_id`) VALUES
(1, 1, 200.00, 0.11, '2026-10-24', '', '2026-10-24 20:35:29', NULL, NULL, NULL),
(2, 1, 200.00, 20.00, '2026-10-25', '', '2026-10-25 09:31:38', NULL, NULL, NULL),
(3, 2, 277.78, 41.67, '2026-10-25', 'Installment #1', '2026-10-25 21:14:05', NULL, NULL, 1),
(4, 3, 500.00, 25.00, '2026-10-25', 'Installment #1', '2026-10-25 21:20:41', NULL, NULL, 19);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `phone`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'shakib', '01874998798', '2026-10-24 04:55:07', NULL, NULL),
(2, 'rahsed', '0188049567', '2026-10-24 14:26:31', NULL, NULL),
(3, 'ashik', '0193895678', '2026-10-24 14:26:54', NULL, NULL),
(4, 'fahad', '01344556678', '2026-10-24 14:27:16', NULL, NULL),
(5, 'baker', '01389098008', '2026-10-24 14:27:40', NULL, NULL),
(6, 'hossan', '01936778899', '2026-10-24 14:28:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `savings`
--

CREATE TABLE `savings` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `saving_date` date NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savings`
--

INSERT INTO `savings` (`id`, `member_id`, `amount`, `saving_date`, `note`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1000.00, '2026-10-24', '', '2026-10-24 06:55:35', NULL, NULL),
(2, 3, 1000.00, '2026-10-24', '', '2026-10-24 16:29:45', NULL, NULL),
(3, 5, 10000.00, '2026-10-24', '', '2026-10-24 16:30:04', NULL, NULL),
(4, 4, 2000.00, '2026-10-24', '', '2026-10-24 16:30:21', NULL, NULL),
(5, 6, 1000.00, '2026-10-24', '', '2026-10-24 16:30:40', NULL, NULL),
(6, 2, 2000.00, '2026-10-24', '', '2026-10-24 16:30:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `remember_token` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `contact_no`, `email`, `password`, `status`, `remember_token`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Kamal Uddin', NULL, 'kamal@yahoo.com', '738f69d66dd207d6184e396e0c7e6bd582165627', 1, '14201791604761119689', NULL, NULL, NULL, NULL),
(3, 'kamal', NULL, 'kamal@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 1, '91381792898914224330', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_installments`
--
ALTER TABLE `loan_installments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_installments_loan` (`loan_id`);

--
-- Indexes for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loan_payments_loan` (`loan_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `savings`
--
ALTER TABLE `savings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_savings_member` (`member_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_installments`
--
ALTER TABLE `loan_installments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `savings`
--
ALTER TABLE `savings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `savings`
--
ALTER TABLE `savings`
  ADD CONSTRAINT `fk_savings_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
