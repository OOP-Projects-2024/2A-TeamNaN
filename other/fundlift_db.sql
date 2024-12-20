-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2024 at 05:01 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fundlift_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `campaigns_tbl`
--

CREATE TABLE `campaigns_tbl` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `goal_amount` decimal(10,2) DEFAULT NULL,
  `amount_raised` decimal(10,2) DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','completed','archived','pending_removal') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `campaigns_tbl`
--

INSERT INTO `campaigns_tbl` (`id`, `user_id`, `title`, `description`, `goal_amount`, `amount_raised`, `start_date`, `end_date`, `created_at`, `status`) VALUES
(21, 29, 'admincampaign1', 'testing', '1000.00', '20.00', '2024-11-01', '2025-01-01', '2024-12-20 10:49:20', 'active'),
(22, 29, 'admincampaign2', 'testing', '1000.00', '40.00', '2024-11-01', '2025-01-01', '2024-12-20 10:57:37', 'active'),
(23, 29, 'admincampaign3', 'testing', '1000.00', '60.00', '2024-11-01', '2025-01-01', '2024-12-20 10:57:52', 'active'),
(24, 29, 'admincampaign4', 'testing', '1000.00', '60.00', '2024-11-01', '2025-01-01', '2024-12-20 10:58:00', 'active'),
(25, 25, 'campaignownercampaign1', 'testing', '30000.00', '0.00', '2024-11-01', '2025-01-01', '2024-12-20 10:58:58', 'archived'),
(26, 25, 'update', 'test', '1000.00', '0.00', '2024-11-01', '2025-01-01', '2024-12-20 10:59:07', 'completed'),
(27, 25, 'campaignownercampaign3', 'testing', '30000.00', '0.00', '2024-11-01', '2025-01-01', '2024-12-20 10:59:13', 'pending_removal'),
(28, 25, 'campaignownercampaign4', 'testing', '30000.00', '0.00', '2024-11-01', '2025-01-01', '2024-12-20 10:59:20', 'pending_removal');

-- --------------------------------------------------------

--
-- Table structure for table `pledges_tbl`
--

CREATE TABLE `pledges_tbl` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `message` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `refund_reason` text,
  `refund_status` enum('not_requested','pending','refunded','denied') DEFAULT 'not_requested',
  `payment_status` enum('pending','paid','unsuccessful') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pledges_tbl`
--

INSERT INTO `pledges_tbl` (`id`, `campaign_id`, `user_id`, `amount`, `message`, `created_at`, `refund_reason`, `refund_status`, `payment_status`) VALUES
(71, 21, 29, '10.00', 'adminpledge1', '2024-12-20 11:08:38', 'adminrefundtest', 'denied', 'paid'),
(72, 22, 29, '10.00', 'adminpledge1', '2024-12-20 11:08:51', 'adminrefundtest', 'denied', 'paid'),
(73, 23, 29, '10.00', 'adminpledge1', '2024-12-20 11:08:59', NULL, 'denied', 'paid'),
(74, 24, 29, '10.00', 'adminpledge1', '2024-12-20 11:09:06', NULL, 'not_requested', 'paid'),
(75, 25, 29, '10.00', 'adminpledge1', '2024-12-20 11:09:16', NULL, 'not_requested', 'unsuccessful'),
(76, 26, 29, '10.00', 'adminpledge1', '2024-12-20 11:09:25', NULL, 'not_requested', 'pending'),
(77, 27, 29, '10.00', 'adminpledge1', '2024-12-20 11:09:35', NULL, 'not_requested', 'pending'),
(78, 28, 29, '10.00', 'adminpledge1', '2024-12-20 11:09:44', NULL, 'not_requested', 'pending'),
(79, 21, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:05', 'ownerefundtest', 'denied', 'paid'),
(80, 22, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:14', 'ownerefundtest', 'refunded', 'paid'),
(81, 23, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:22', NULL, 'not_requested', 'paid'),
(82, 24, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:28', NULL, 'not_requested', 'paid'),
(83, 25, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:34', NULL, 'not_requested', 'unsuccessful'),
(84, 26, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:42', NULL, 'not_requested', 'pending'),
(85, 27, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:48', NULL, 'not_requested', 'pending'),
(86, 28, 25, '20.00', 'ownerpledge', '2024-12-20 11:11:54', NULL, 'not_requested', 'pending'),
(87, 21, 30, '30.00', 'userpledge', '2024-12-20 11:12:40', 'userrefundtest', 'refunded', 'paid'),
(88, 22, 30, '30.00', 'userpledge', '2024-12-20 11:12:51', 'userrefundtest', 'denied', 'paid'),
(89, 23, 30, '30.00', 'userpledge', '2024-12-20 11:12:57', NULL, 'not_requested', 'paid'),
(90, 24, 30, '30.00', 'userpledge', '2024-12-20 11:13:07', NULL, 'not_requested', 'paid'),
(91, 25, 30, '30.00', 'userpledge', '2024-12-20 11:13:12', NULL, 'not_requested', 'unsuccessful'),
(92, 26, 30, '30.00', 'userpledge', '2024-12-20 11:13:17', NULL, 'not_requested', 'pending'),
(93, 27, 30, '30.00', 'userpledge', '2024-12-20 11:13:25', NULL, 'not_requested', 'pending'),
(94, 28, 30, '30.00', 'userpledge', '2024-12-20 11:13:31', NULL, 'not_requested', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`id`, `username`, `password`, `token`, `role`) VALUES
(25, 'xel', '$2y$10$MTQ0NTQzODdmN2IyODZmNOOBKbupCAfPR4t5iyrTsmo2GaKmXHz6u', 'ZWM4M2FhOGIzOGMzMjUzM2YxMDg4M2UxYzRkMTFkYmY2MmM3ZGJmMGVhYjNjZTcyN2I2NDhmZmY1MzAwZDNkZA==', 'campaign_owner'),
(26, 'sunoo', '$2y$10$Y2E0NjA5MGMxMDY0ZjRkZ.hg4CWfPaz1FRYxeLV0BeQVSzJ4SnduO', '', 'campaign_owner'),
(27, 'harua', '$2y$10$MDRjMTRhZDI1NmM1NGU0Nun8txhG/7CTdfhlVQ5ehiAkqXqRyVFrK', '', 'user'),
(28, 'ricky', '$2y$10$NDEwNDA3NjEwYTEwMjkwYu3jLbwtsTtEYvn9oX63HHfhAI1/SELdG', '', 'campaign_owner'),
(29, 'zhanghao', '$2y$10$OWU2YWE2ZmIzZTVhZjk0NeS4vXksNBv8rxuEvnGkA9guIrHtDU40K', 'MWExZWIwYTA1ZGExOWZhZjU3ZGY4YzY4MzUwOTdiOGRjMWFjZDg3NjAwNzgzMzhkNGIyNjU3ODgwOTlkYWZkZg==', 'admin'),
(30, 'jungwoo', '$2y$10$ZWI0YWFlZTQ4YjRlMGUxZOr.a1revp.Ox002M5N/6xqtcAT.ELPl2', 'ZWVjZGEyMDg3YWI4OTE4NTA2M2M5N2RlMTAwNDg3MDE4ODkzOGNmZjJmZjRlNWI1NDE2MTk2NzEwOGU0ZjRmZQ==', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `campaigns_tbl`
--
ALTER TABLE `campaigns_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pledges_tbl`
--
ALTER TABLE `pledges_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_id` (`campaign_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `campaigns_tbl`
--
ALTER TABLE `campaigns_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `pledges_tbl`
--
ALTER TABLE `pledges_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `campaigns_tbl`
--
ALTER TABLE `campaigns_tbl`
  ADD CONSTRAINT `campaigns_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`id`);

--
-- Constraints for table `pledges_tbl`
--
ALTER TABLE `pledges_tbl`
  ADD CONSTRAINT `pledges_tbl_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns_tbl` (`id`),
  ADD CONSTRAINT `pledges_tbl_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
