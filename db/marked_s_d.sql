-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 27, 2024 at 06:01 PM
-- Server version: 5.7.34
-- PHP Version: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marked`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) NOT NULL,
  `s_id` int(10) NOT NULL,
  `s_lat` float(10,6) NOT NULL,
  `s_long` float(10,6) NOT NULL,
  `clg_lat` float(10,6) NOT NULL,
  `clg_long` float(10,6) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(45) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `s_id`, `s_lat`, `s_long`, `clg_lat`, `clg_long`, `date`, `ip`) VALUES
(5, 10, 25.627819, 85.102150, 25.630571, 85.103386, '2024-05-23 13:53:30', '127.0.0.1'),
(4, 7, 25.626589, 85.102409, 25.630571, 85.103386, '2024-05-23 06:43:55', '127.0.0.1'),
(6, 11, 25.629370, 85.103577, 25.630571, 85.103386, '2024-05-23 13:54:12', '127.0.0.1'),
(7, 12, 25.627804, 85.101997, 25.630571, 85.103386, '2024-05-23 13:54:42', '127.0.0.1'),
(8, 7, 25.627972, 85.102318, 25.630571, 85.103386, '2024-05-24 05:07:25', '127.0.0.1'),
(9, 14, 25.630449, 85.103630, 25.630571, 85.103386, '2024-05-24 05:51:58', '127.0.0.1'),
(10, 7, 25.630373, 85.103203, 25.630571, 85.103386, '2024-05-26 19:31:29', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(10) NOT NULL,
  `s_id` int(10) NOT NULL,
  `marks` int(5) DEFAULT NULL,
  `subject_id` varchar(255) NOT NULL,
  `resultType_id` varchar(50) NOT NULL,
  `f_id` int(10) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `s_id`, `marks`, `subject_id`, `resultType_id`, `f_id`, `ip`, `created`) VALUES
(97, 14, 64, '8', '1', 9, '127.0.0.1', '2024-05-27 15:01:22'),
(96, 14, 64, '3', '1', 9, '127.0.0.1', '2024-05-27 15:00:13'),
(95, 7, 88, '3', '1', 9, '127.0.0.1', '2024-05-27 15:00:13'),
(94, 14, 98, '9', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(93, 14, 98, '3', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(92, 12, 98, '9', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(91, 12, 98, '3', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(90, 11, 98, '9', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(89, 11, 98, '3', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(88, 10, 98, '9', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(87, 10, 98, '3', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(86, 7, 98, '9', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(85, 7, 98, '3', '9', 9, '127.0.0.1', '2024-05-27 13:52:50'),
(84, 7, 89, '8', '1', 9, '127.0.0.1', '2024-05-27 13:52:18'),
(83, 14, 23, '2', '1', 9, '127.0.0.1', '2024-05-27 12:41:53'),
(82, 12, 23, '2', '1', 9, '127.0.0.1', '2024-05-27 12:41:53'),
(81, 11, 23, '2', '1', 9, '127.0.0.1', '2024-05-27 12:41:53'),
(80, 10, 23, '2', '1', 9, '127.0.0.1', '2024-05-27 12:41:53'),
(79, 7, 23, '2', '1', 9, '127.0.0.1', '2024-05-27 12:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `result_types`
--

CREATE TABLE `result_types` (
  `id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `result_types`
--

INSERT INTO `result_types` (`id`, `type`, `created`) VALUES
(1, 'End-Term', '2024-05-23 08:11:33'),
(9, 'Mid-Term', '2024-05-23 13:44:02'),
(10, 'Internal', '2024-05-23 13:45:22');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject`, `created`) VALUES
(3, 'DBMS', '2024-05-23 10:19:26'),
(2, 'Python', '2024-05-23 08:11:04'),
(8, 'Operating System', '2024-05-23 13:46:06'),
(7, 'Computer Graphics', '2024-05-23 13:45:57'),
(9, 'DSA Using C', '2024-05-23 13:46:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student',
  `branch` varchar(255) DEFAULT NULL,
  `semester` int(2) DEFAULT NULL,
  `session` varchar(50) DEFAULT '2022-25',
  `roll` int(5) DEFAULT NULL,
  `pass` varchar(100) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `branch`, `semester`, `session`, `roll`, `pass`, `ip`, `created`, `updated`) VALUES
(12, 'Omprakash Kumar 4', 'a@g.cab', 'student', 'CE', 4, '2022-25', 25, '$2y$10$NZ20Jy2K6w2rJIG6Dmw4KOIsGWTQ6bEAvamiid0bK.emAhaaWx6EW', NULL, '2024-05-23 13:52:55', '2024-05-27 13:15:11'),
(11, 'Omprakash Kumar 3', 'a@g.ca', 'student', 'EE', 6, '2022-25', 56, '$2y$10$7379lRT5sJcmPPgNLatmk.XT7WX/mUKTosLucnexXztzgX3IKtDJi', NULL, '2024-05-23 13:52:37', '2024-05-27 13:15:15'),
(10, 'Omprakash Kumar 2', 'a@g.c', 'student', 'ECE', 3, '2022-25', 2, '$2y$10$s9FETIuDxmTET1D573Jcc.o5nvt1fJ403uIateHQ0cq6CxdV.I1N2', NULL, '2024-05-23 13:51:36', '2024-05-27 13:15:20'),
(9, 'Omprakash Kumar', 'om@a.b', 'faculty', NULL, NULL, NULL, NULL, '$2y$10$0sRUZzF7DguvBkNk.NRO4e6IXcKzXfED9of8fME4UOS4Q17PpxpE.', NULL, '2024-05-23 06:45:54', '2024-05-23 06:45:54'),
(7, 'Omprakash', 'omprxz@gmail.com', 'student', 'CSE', 4, '2023-26', 2, '$2y$10$irpOw7YMUNXSstBBS/I1E.IxPx/suzC9ekAWBsbg3ZlGgIdMzCf06', NULL, '2024-05-22 17:50:53', '2024-05-27 13:15:28'),
(13, 'Aditya', 'a@d.i', 'student', 'AUTOM', 5, '2023-26', 45, '$2y$10$dFW.uExb5QkLRvoKnmoTsOzyqGvPB.xb1AAWXYQY7a9OmWQdEKhjm', NULL, '2024-05-23 21:27:26', '2024-05-27 13:15:36'),
(14, 'Ankit Kumar', 'ankit@gmail.com', 'student', 'CSE', 3, '2022-25', 25, '$2y$10$5YOMRpnkTB90S2tg6vviUux0DkrMcvKxJveG6OUygPw9R7TKRPU7G', NULL, '2024-05-24 05:50:56', '2024-05-27 14:59:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `result_types`
--
ALTER TABLE `result_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `result_types`
--
ALTER TABLE `result_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
