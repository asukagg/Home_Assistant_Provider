-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2026 at 08:56 PM
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
-- Database: `home_assistant_provider`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `booking_status` varchar(50) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `customer_id`, `provider_id`, `service_id`, `booking_status`, `time`, `date`, `notes`, `created_at`) VALUES
(1, NULL, NULL, NULL, 'confirmed', '10:00:00', '2026-04-20', NULL, '2026-04-24 22:58:51'),
(2, NULL, NULL, NULL, 'pending', '12:00:00', '2026-04-21', NULL, '2026-04-24 22:58:51'),
(3, NULL, NULL, NULL, 'completed', '14:00:00', '2026-04-22', NULL, '2026-04-24 22:58:51'),
(4, NULL, NULL, NULL, 'cancelled', '16:00:00', '2026-04-23', NULL, '2026-04-24 22:58:51'),
(5, NULL, NULL, NULL, 'confirmed', '18:00:00', '2026-04-24', NULL, '2026-04-24 22:58:51'),
(6, 5, 4, 5, 'confirmed', '17:00:00', '2026-05-02', 'address e eshe call diben', '2026-04-24 23:09:13'),
(7, 5, 4, 2, 'cancelled', '16:00:00', '0026-04-30', '', '2026-04-26 11:39:05'),
(8, 5, 6, 5, 'completed', '16:00:00', '2026-05-20', 'bhai niche eshe call diyen.', '2026-04-30 21:01:33'),
(9, 5, 6, 5, 'completed', '16:01:00', '2026-05-21', 'hi', '2026-04-30 21:19:30'),
(10, 5, 6, 5, 'rejected', '17:00:00', '2026-06-05', 'drawing', '2026-04-30 21:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `can_book`
--

CREATE TABLE `can_book` (
  `customer_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `can_book`
--

INSERT INTO `can_book` (`customer_id`, `service_id`, `booking_id`) VALUES
(2, 1, 1),
(2, 5, 5),
(3, 2, 2),
(4, 3, 3),
(5, 2, 7),
(5, 4, 4),
(5, 5, 6),
(5, 5, 8),
(5, 5, 9),
(5, 5, 10);

-- --------------------------------------------------------

--
-- Table structure for table `favourite`
--

CREATE TABLE `favourite` (
  `customer_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favourite`
--

INSERT INTO `favourite` (`customer_id`, `provider_id`) VALUES
(5, 6),
(5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`booking_id`, `user_id`) VALUES
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 2),
(8, 5),
(8, 6),
(9, 5),
(9, 6);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `notif_msg` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `status`, `notif_msg`, `user_id`, `created_at`) VALUES
(1, 'unread', 'Booking confirmed', 2, '2026-04-24 22:58:51'),
(2, 'read', 'Payment successful', 3, '2026-04-24 22:58:51'),
(3, 'read', 'New offer available', 4, '2026-04-24 22:58:51'),
(4, 'read', 'Service completed', 5, '2026-04-24 22:58:51'),
(5, 'unread', 'Review requested', 2, '2026-04-24 22:58:51'),
(6, 'read', 'New booking request received.', 4, '2026-04-24 23:09:13'),
(7, 'read', 'Your booking was accepted.', 5, '2026-04-24 23:10:10'),
(8, 'read', 'Your provider account was approved.', 6, '2026-04-24 23:15:30'),
(9, 'unread', 'New booking request received.', 4, '2026-04-26 11:39:05'),
(10, 'unread', 'A booking was cancelled by the customer.', 4, '2026-04-26 11:54:36'),
(11, 'unread', 'A booking was cancelled by the customer.', 4, '2026-04-26 11:54:41'),
(12, 'unread', 'A booking was cancelled by the customer.', 4, '2026-04-26 11:54:46'),
(13, 'unread', 'Your provider account was approved.', 7, '2026-04-30 13:50:18'),
(15, 'read', 'New booking request received.', 6, '2026-04-30 21:01:33'),
(16, 'read', 'Your booking was accepted.', 5, '2026-04-30 21:02:44'),
(17, 'read', 'Service completed. Please leave a review.', 5, '2026-04-30 21:08:38'),
(18, 'read', 'Payment successful.', 5, '2026-04-30 21:13:53'),
(19, 'read', 'Service completed. Please leave a review.', 5, '2026-04-30 21:14:02'),
(20, 'read', 'New booking request received.', 6, '2026-04-30 21:19:30'),
(21, 'read', 'Your booking was accepted.', 5, '2026-04-30 21:19:58'),
(22, 'read', 'Payment successful.', 5, '2026-04-30 21:20:22'),
(23, 'read', 'Payment received.', 6, '2026-04-30 21:20:22'),
(24, 'read', 'Service completed. Please leave a review.', 5, '2026-04-30 21:20:36'),
(25, 'read', 'Review given.', 6, '2026-04-30 21:21:48'),
(26, 'unread', 'New booking request received.', 6, '2026-04-30 21:28:46'),
(27, 'unread', 'Your booking was rejected.', 5, '2026-04-30 21:28:57'),
(28, 'unread', 'Your booking was rejected.', 5, '2026-04-30 21:29:05'),
(29, 'unread', 'Your booking was rejected.', 5, '2026-04-30 21:48:27'),
(30, 'unread', 'Your booking was rejected.', 5, '2026-04-30 21:48:32'),
(31, 'unread', 'Your booking was rejected.', 5, '2026-04-30 21:49:04');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `service_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`service_id`, `provider_id`) VALUES
(1, 3),
(2, 4),
(2, 7),
(3, 5),
(4, 3),
(4, 7),
(5, 4),
(5, 6),
(5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `transaction_id` int(11) NOT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`transaction_id`, `transaction_ref`, `payment_status`, `payment_method`, `amount`, `paid_at`, `booking_id`) VALUES
(1, NULL, 'paid', 'card', 500.00, NULL, 1),
(2, NULL, 'pending', 'cash', 600.00, NULL, 2),
(3, NULL, 'paid', 'bkash', 300.00, NULL, 3),
(4, NULL, 'failed', 'card', 800.00, NULL, 4),
(5, NULL, 'paid', 'nagad', 700.00, NULL, 5),
(6, '3fcfd4a1151413e8', 'paid', 'card', 700.00, '2026-04-30 21:13:53', 8),
(7, '685334718d796de6', 'paid', 'cash', 700.00, '2026-04-30 21:20:22', 9);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `review_date` date DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `review_date`, `rating`, `comment`, `provider_id`, `booking_id`, `customer_id`) VALUES
(1, '2026-04-20', 5, 'Excellent service', 3, 1, NULL),
(2, '2026-04-21', 4, 'Good job', 4, 2, NULL),
(3, '2026-04-22', 3, 'Average', 5, 3, NULL),
(4, '2026-04-23', 2, 'Not satisfied', 3, 4, NULL),
(5, '2026-04-24', 5, 'Highly recommended', 4, 5, NULL),
(6, '2026-04-30', 5, 'pretty drawings', 6, 9, 5);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `service_id` int(11) NOT NULL,
  `service_category` varchar(100) DEFAULT NULL,
  `price_per_hour` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`service_id`, `service_category`, `price_per_hour`) VALUES
(1, 'Plumbing', 500.00),
(2, 'Electrician', 600.00),
(3, 'Cleaning', 300.00),
(4, 'AC Repair', 1000.00),
(5, 'Painting', 700.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT NULL,
  `nid_card` varchar(50) DEFAULT NULL,
  `nid_file` varchar(255) DEFAULT NULL,
  `verification_status` varchar(50) DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `address`, `role`, `flag`, `nid_card`, `nid_file`, `verification_status`, `balance`, `created_at`, `admin_id`) VALUES
(1, 'Admin User', 'admin@gmail.com', '$2y$10$LSxL.urTMcArpDFsvlGoCOuzaVDENyND/AxqBJL7QrvmHhe1xcICS', '01700000001', 'Dhaka', 'admin', 1, 'NID001', NULL, 'verified', 0.00, '2026-04-24 22:58:51', NULL),
(2, 'Rahim', 'rahim@gmail.com', 'pass123', '01700000002', 'Dhaka', 'customer', 0, 'NID002', NULL, 'verified', 0.00, '2026-04-24 22:58:51', 1),
(3, 'Karim', 'karim@gmail.com', '$2y$10$Mnuxl4bw/rcdjQFVcbX4S.WQdQAHIQWQXg5YyEn.rFf0EMqldhaOm', '01700000003', 'Chittagong', 'customer', 0, 'NID003', NULL, 'pending', 0.00, '2026-04-24 22:58:51', 1),
(4, 'Sadia', 'sadia@gmail.com', '$2y$10$kIOIq40m8xQVw5e3bn3IMe743N76Ejy5M5D2fI53dUMuhrzfqtzWe', '01700000004', 'Khulna', 'provider', 0, 'NID004', NULL, 'verified', 0.00, '2026-04-24 22:58:51', 1),
(5, 'Hasan', 'hasan@gmail.com', '$2y$10$KrGXZL82jkZ.0bP9yRwQ1ewquuG6q.fHP8/D2E2qucVoYUHzrwZge', '01700000005', 'Sylhet', 'customer', 0, 'NID005', NULL, 'pending', 0.00, '2026-04-24 22:58:51', 1),
(6, 'dipto', 'dipto@gmail.com', '$2y$10$fUWhxWMDeha5Qge7ge.pPOWCsoCS5slRGpzxthFtgBz4fKrszy.qy', '12345', 'aaaaa', 'provider', 0, 'N123', 'uploads/nid/nid_69eba4e8c4b470.82168694.png', 'verified', 0.00, '2026-04-24 23:14:16', NULL),
(7, 'mashrafi', 'mashrafi@gmail.com', '$2y$10$7aYTB7KbO7LbhfIvY05/9uj69Tr/YhRG/X170cHiQNT2RxNwPiJiS', '01711079933', 'Dhaka', 'provider', 0, '112', 'uploads/nid/nid_69f30258cf68e0.10395835.png', 'verified', 0.00, '2026-04-30 13:18:48', NULL),
(9, 'test_cust', 'test_cust@gmail.com', '$2y$10$t7OReudMx2MtoO7.kctm/uQY8.CZwp7RhDgVqZPZazygQ.JoU94QO', '1234567890', 'Chittagang', 'customer', 0, '', NULL, 'verified', 0.00, '2026-04-30 20:10:33', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `can_book`
--
ALTER TABLE `can_book`
  ADD PRIMARY KEY (`customer_id`,`service_id`,`booking_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`customer_id`,`provider_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`booking_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`service_id`,`provider_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `can_book`
--
ALTER TABLE `can_book`
  ADD CONSTRAINT `can_book_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `can_book_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `can_book_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `favourite`
--
ALTER TABLE `favourite`
  ADD CONSTRAINT `favourite_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favourite_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
