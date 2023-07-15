-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2023 at 06:04 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mysave`
--

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `dest_bank` varchar(100) NOT NULL,
  `dest_acct` varchar(100) NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `narration` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `amount`, `transaction_type`, `status`, `date`, `user_id`, `dest_bank`, `dest_acct`, `reason`, `narration`) VALUES
(1, '3000.00', 'credit', 'pending', '2022-11-23 02:08:09', 1, '', '', '', ''),
(2, '3000.00', 'credit', 'pending', '2022-11-23 02:09:46', 1, '', '', 'Ball', ''),
(3, '2000.00', 'debit', 'pending', '2022-11-23 02:11:30', 1, '', '', 'Ball', ''),
(4, '1000.00', 'debit', 'pending', '2022-11-23 02:13:24', 1, 'GTB', '1234567', 'Ball', 'Hey money'),
(5, '1000.00', 'debit', 'pending', '2022-11-23 02:20:54', 1, 'Zenith', '77234567', 'Ball', 'Hey Joe'),
(6, '22222.00', 'credit', 'pending', '2022-11-23 11:02:08', 1, '', '', 'Ball', ''),
(7, '50000.00', 'credit', 'pending', '2022-11-23 11:06:12', 1, '', '', 'Sapa', ''),
(8, '33.00', 'credit', 'pending', '2022-11-23 11:07:39', 1, '', '', 'Sapa', ''),
(9, '1000.00', 'credit', 'pending', '2022-11-23 11:08:07', 1, '', '', 'Ball', ''),
(10, '50000.00', 'credit', 'pending', '2022-11-23 11:58:05', 3, '', '', 'Ball', ''),
(11, '2000.00', 'debit', 'pending', '2022-11-24 23:44:51', 1, 'GTB', '081474738383', 'Food', 'hello'),
(12, '2000.00', 'debit', 'pending', '2022-11-24 23:45:15', 1, 'GTB', '49999', 'Food', 'reeer'),
(13, '30000.00', 'debit', 'pending', '2022-11-24 23:46:30', 1, 'GTB', '123423222', 'Bill', 'hello there'),
(14, '1255.00', 'debit', 'pending', '2022-11-25 02:46:42', 1, 'GTB', '081474738383', 'Food', 'hello'),
(15, '2000.00', 'credit', 'pending', '2022-11-25 03:35:42', 1, '', '', 'Sapa', ''),
(16, '2000.00', 'credit', 'pending', '2022-11-25 04:04:57', 1, '', '', 'Ball', ''),
(17, '0.00', 'credit', 'pending', '2022-11-25 04:09:01', 1, '', '', '', ''),
(18, '0.00', 'credit', 'pending', '2022-11-25 04:09:11', 1, '', '', '', ''),
(19, '0.00', 'credit', 'pending', '2022-11-25 04:09:12', 1, '', '', '', ''),
(20, '0.00', 'credit', 'pending', '2022-11-25 04:09:13', 1, '', '', '', ''),
(21, '3444.00', 'credit', 'pending', '2022-11-25 10:54:36', 1, '', '', 'Sapa', ''),
(22, '2000.00', 'debit', 'pending', '2022-11-27 15:47:22', 1, 'GTB', '125432233', 'Food', 'ereqee'),
(23, '23333.00', 'debit', 'pending', '2022-11-27 15:50:04', 1, 'GTB', '3333', 'Food', '333'),
(24, '1000.00', 'debit', 'pending', '2022-11-27 15:51:03', 1, 'GTB', '12221', 'Food', ''),
(25, '0.00', 'debit', 'pending', '2022-11-27 15:57:56', 1, 'Zenith', '3', '', ''),
(26, '200.00', 'debit', 'pending', '2022-11-27 16:04:49', 1, 'GTB', '2233333332323', 'Bill', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `password` varchar(100) NOT NULL,
  `how_did_your_hear` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `balance`, `password`, `how_did_your_hear`, `account_number`, `address`, `created`) VALUES
(1, 'janeto34433', 'Ade', 'grace@gmail.com', '08028233285', '20911.00', '$2y$10$rr8P9/flR6oAfFJbsVgDcu3OEub1Kjkqft48bYPJqSA/.sQlK/H4y', 'Whatsapp', '08028233285', '30 Member street', '2022-11-23 00:04:36'),
(2, 'Oluwatobi Adeyokunnu', 'wwww', 'adeyokunnuo@gmail.com', '09034613738', '0.00', '$2y$10$QbOWWChulCK7Uwmxqpv82..w7Z4IdHxRQfz6NIP0hkBml4qj0ZEOW', 'Facebook', '09034613738', NULL, '2022-11-23 10:27:51'),
(3, 'Segun', 'Ade', 'segunade@gmail.com', '081474738383', '50000.00', '$2y$10$tAS0EGpM0XbUPNg3Cn5mDOdQBYHRuok/ZyXjAMZ/ABtcqFAF9G4Ai', 'Facebook', '081474738383', NULL, '2022-11-23 11:57:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_ibfk_1` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
