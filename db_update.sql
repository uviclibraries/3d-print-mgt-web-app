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

USE `3d_print_jobs`;


-- table containing meta information about 3d printing and laser cutting jobs
CREATE TABLE web_job (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `netlink_id` varchar(100) NOT NULL,
  `job_name` varchar(100) NOT NULL,
  `staff_notes` text,
  `status` varchar(100) NOT NULL,
  `submission_date` date DEFAULT NULL,
  `price` decimal(4,2) DEFAULT NULL,
  `priced_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `printing_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
  `material_type` varchar(100) NOT NULL,
  `comments` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Weak entity table that extends web_job table with laser cutting information
CREATE TABLE laser_cut_job (
  `laser_cut_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  -- foreign key doesn't work with auto increment unfortunatly
  FOREIGN KEY (`laser_cut_id`) REFERENCES web_job(`id`),
  `model_name` varchar(250) NOT NULL,
  `model_name_2` varchar(250) NOT NULL,
  `copies` int(11) NOT NULL,
  `material_type` varchar(100) NOT NULL,
  `comments` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO web_job (`id`, `netlink_id`, `job_name`, `staff_notes`, `status`, `submission_date`, `price`, `priced_date`, `printing_date`, `completed_date`)
SELECT `id`, `netlink_id`, `job_name`, `staff_notes`, `status`, `submission_date`, `price`, `priced_date`, `printing_date`, `completed_date` 
FROM `print_job`;

INSERT INTO 3d_print_job (`3d_print_id`, `model_name`, `model_name_2`, `infill`, `scale`, `layer_height`, `supports`, `copies`, `material_type`, `comments`)
SELECT `id`, `model_name`, `model_name_2`, `infill`, `scale`, `layer_height`, `supports`, `copies`, `material_type`, `comments`
FROM  `print_job`;