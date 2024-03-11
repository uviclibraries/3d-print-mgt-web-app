-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: NA
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

-- --------------------------------------------------------

--
-- Table structure for table `printer`
-- Note: color2 not utilized in webapp

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

--
-- Table structure for table `users`
-- Note: name is the user’s preferred name

CREATE TABLE `users` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `netlink_id` varchar(100) NOT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `user_type` int(11) NOT NULL DEFAULT 1,
  `email` varchar(100) NOT NULL,
  `cron_report` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`netlink_id`, `name`, `user_type`, `email`) VALUES
('rmccue', 'Rich McCue', 0, 'rmccue@uvic.ca');

--
--


CREATE TABLE `moneris_fields` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `response_order_id` varchar(50),
  `netlink_id` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `response_code` int(3),
  `date_stamp` date,
  `time_stamp` time,
  `result` varchar(5),
  `trans_name` varchar(20),
  `cardholder` varchar(50),
  `card` varchar(2),
  `charge_total` decimal(9),
  `f4l4` varchar(16),
  `message` varchar(50),
  `iso_code` int(2),
  `bank_approval_code` varchar(8),
  `bank_transaction_id` varchar(20),
  `txn_num` varchar(20),
  `avs_response_code` varchar(1),
  `cavv_result_code` varchar(1),
  `INVOICE` varchar (50),
  `ISSCONF` varchar (50),
  `ISSNAME` varchar (50)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `web_job`
--

-- --------------------------------------------------------
-- table containing meta information about all jobs

CREATE TABLE web_job (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `netlink_id` varchar(100) NOT NULL,
  `job_name` varchar(100) NOT NULL,
  `parent_job_id` INT(11) NOT NULL DEFAULT 0,
  `is_parent` BIT DEFAULT FALSE NOT NULL,
  `job_purpose` VARCHAR(10) NOT NULL,
  `academic_code` VARCHAR(30) NOT NULL,
  `course_due_date` DATE NULL DEFAULT NULL,
  `staff_notes` text,
  `status` varchar(100) NOT NULL,
  `submission_date` date DEFAULT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `priced_date` date DEFAULT NULL,
  `priced_signer` VARCHAR(40) NOT NULL,
  `hold_date` date DEFAULT NULL,
  `hold_signer` VARCHAR(40) NOT NULL,
  `paid_date` date DEFAULT NULL,
  `paid_signer` VARCHAR(40) NOT NULL,
  `printing_date` date DEFAULT NULL,
  `printing_signer` VARCHAR(40) NOT NULL,
  `completed_date` date DEFAULT NULL,
  `completed_signer` VARCHAR(40) NOT NULL,
  `delivered_date` DATE NULL DEFAULT NULL,
  `delivered_signer` VARCHAR(40) NOT NULL,
  `cancelled_date` DATE NULL DEFAULT NULL,
  `cancelled_signer` VARCHAR(40) NOT NULL,
  `archived_date` DATE NULL DEFAULT NULL,
  `archived_signer` VARCHAR(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `3d_print_job`
--

-- --------------------------------------------------------
-- Weak entity table that extends web_job table with 3d printing information
CREATE TABLE 3d_print_job (
  `3d_print_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  FOREIGN KEY (`3d_print_id`) REFERENCES web_job(`id`),
  `model_name` varchar(250) NOT NULL,
  `model_name_2` varchar(250) DEFAULT NULL,
  `infill` int(11) NOT NULL,
  `scale` int(11) NOT NULL,
  `layer_height` decimal(2,2) NOT NULL,
  `supports` int(11) NOT NULL,
  `copies` int(11) NOT NULL,
  `duration` INT(11) NOT NULL,
  `material_type` varchar(100) NOT NULL,
  `comments` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `laser_cut_job`
--

-- --------------------------------------------------------
-- Weak entity table that extends web_job table with laser cutting information
CREATE TABLE laser_cut_job (
  `laser_cut_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  -- foreign key doesn't work with auto increment unfortunatly
  FOREIGN KEY (`laser_cut_id`) REFERENCES web_job(`id`),
  `model_name` varchar(250) NOT NULL,
  `model_name_2` varchar(250) NOT NULL,
  `copies` int(11) NOT NULL,
  `specifications` text,
  `duration` INT(11) NOT NULL,
  `material_type` varchar(100) NOT NULL,
  `comments` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `large_format_print_job`
--

-- --------------------------------------------------------
-- Weak entity table that extends web_job table with laser cutting information

CREATE TABLE `large_format_print_job`(
`large_format_print_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`model_name` varchar(250) NOT NULL,
`model_name_2` varchar(250) DEFAULT NULL,
`copies` int(11) NOT NULL,
`width_inches` float(7) NOT NULL DEFAULT 0,
`length_inches` float(7) NOT NULL DEFAULT 0,
`comments` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;