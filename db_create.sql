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
CREATE DATABASE IF NOT EXISTS `3d_print_jobs` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `3d_print_jobs`;

-- --------------------------------------------------------

--
-- Table structure for table `printer`
--

CREATE TABLE `printer` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `printer_name` varchar(60) NOT NULL,
  `make_model` varchar(100) NOT NULL,
  `comments` text NOT NULL,
  `operational` boolean NOT NULL,
  `2extruder` boolean NOT NULL DEFAULT 0,
  `color` varchar(50),
  `color2` varchar(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `printer`
--

INSERT INTO `printer` (`id`, `printer_name`, `make_model`, `comments`, `operational`, `2extruder`, `color`,`color2`) VALUES
(1, 'Ada', 'Ultamaker 3', 'Dual extruder printer, supports dissolving filament.', 1,1, 'yellow','PVA'),
(2, 'Brunel', 'Ultamaker 3', 'Dual extruder printer, supports dissolving filament.', 1,1, 'green',NULL),
(3, 'Makerbot1', 'Replicator 5th gen', 'Single extruder printer. No heated build plate.', 1,0, 'orange',NULL),
(4, 'Makerbot2', 'Replicator 5th gen', 'Single extruder printer. No heated build plate.', 1,0, 'green',NULL);

-- --------------------------------------------------------


CREATE TABLE `print_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `netlink_id` varchar(100) NOT NULL UNIQUE,
  `job_name` varchar(100) NOT NULL,
  `model_name` varchar(250) NOT NULL,
  `model_name_2` varchar(250) DEFAULT NULL,
  `infill` int(11) NOT NULL,
  `scale` int(11) NOT NULL,
  `layer_height` decimal(2,2) NOT NULL,
  `supports` int(11) NOT NULL,
  `copies` int(11) NOT NULL,
  `material_type` varchar(100) NOT NULL,
  `comments` text,
  `staff_notes` text,
  `status` varchar(100) NOT NULL,
  `submission_date` date DEFAULT NULL,
  `price` decimal(4,2) DEFAULT NULL,
  `priced_date` date DEFAULT NULL,
  `ready_to_prnt_date` date DEFAULT NULL,
  `printing_date` date DEFAULT NULL,
  `complete_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `netlink_id` varchar(100) NOT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `user_type` int(11) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `netlink_id`, `name`, `user_type`, `email`) VALUES
(1, 'kenziewo', 'Kenzie Wong', 0, 'kenziewong+fromcreate@gmail.com');

--
--
