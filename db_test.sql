-- Test of lasercutter database update

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

USE `3d_print_jobs`;

INSERT INTO web_job VALUES 
(NULL, 'test_id', 'laser_job_1', 'db test', 'submitted', '2022-08-26', 0.0, '2022-08-26', '2022-08-26', NULL, NULL),
(NULL, 'test_id', 'laser_job_2', 'db test', 'submitted', '2022-08-26', 0.0, '2022-08-26', '2022-08-26', NULL, NULL);

INSERT INTO laser_cut_job VALUES
(NULL, 'laser model 1', 'laser model 1', 1, 'mdf', 'test of database'),
(NULL, 'laser model 1', 'laser model 1', 1, 'mdf', 'test of database');