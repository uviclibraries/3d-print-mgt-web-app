-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 01, 2020 at 03:56 PM
-- Server version: 5.7.26
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `3d_print_mgt`
--
CREATE DATABASE IF NOT EXISTS `3d_print_mgt` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `3d_print_mgt`;

-- --------------------------------------------------------

--
-- Table structure for table `printer`
--

CREATE TABLE `printer` (
  `id` int(11) NOT NULL,
  `printer_name` varchar(60) NOT NULL,
  `make_model` varchar(100) NOT NULL,
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `printer`
--

INSERT INTO `printer` (`id`, `printer_name`, `make_model`, `comments`) VALUES
(1, 'Ada', 'Ultamaker 3', 'Dual extruder printer, supports dissolving filament.');

-- --------------------------------------------------------

--
-- Table structure for table `print_job`
--price decimal(4,2) = 9999.99 is maximum charge.

CREATE TABLE `print_job` (
  `id` int(11) NOT NULL,
  `netlink_id` varchar(100) NOT NULL,
  `job_name` varchar(100) NOT NULL,
  `model_name` varchar(250) NOT NULL,
  `model_name_2` varchar(250) DEFAULT NULL,
  `infill` int(11) NOT NULL,
  `scale` int(11) NOT NULL,
  `layer_height` decimal(1,2) NOT NULL,
  `supports` int(11) NOT NULL,
  `copies` int(11) NOT NULL,
  `material_type` varchar(100) NOT NULL,
  `comments` text,
  `staff_notes` text,
  `status` varchar(100) NOT NULL,
  `submission_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price` decimal(4,2) DEFAULT NULL,
  `priced_date` date DEFAULT NULL,
  `pending_pmt_date` date DEFAULT NULL,
  `ready_to_prnt_date` date DEFAULT NULL,
  `printing_date` date DEFAULT NULL,
  `complete_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `netlink_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `netlink_id`, `name`, `user_type`, 'email') VALUES
(1, 'rmccue', 'Rich McCue', 0, 'kenziewong@gmail.com'),
(2, 'libmedia', 'Music and Media desk', 1, 'kenziewong@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `printer`
--
ALTER TABLE `printer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `print_job`
--
ALTER TABLE `print_job`
  ADD PRIMARY KEY (`id`),
  ADD KEY `netlink_id` (`netlink_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `netlink_id` (`netlink_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `printer`
--
ALTER TABLE `printer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `print_job`
--
ALTER TABLE `print_job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;