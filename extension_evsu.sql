-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2026 at 06:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `extension_evsu`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(180) DEFAULT NULL,
  `username` varchar(120) DEFAULT NULL,
  `action` varchar(180) DEFAULT NULL,
  `module` varchar(180) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(80) DEFAULT NULL,
  `log_category` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `username`, `action`, `module`, `description`, `details`, `ip_address`, `log_category`, `created_at`) VALUES
(1, 1, NULL, NULL, 'Update Account', 'User Management', 'ID ', NULL, '::1', NULL, '2026-05-19 08:07:31');

-- --------------------------------------------------------

--
-- Table structure for table `approval_history`
--

CREATE TABLE `approval_history` (
  `id` int(11) NOT NULL,
  `document_type` varchar(80) NOT NULL,
  `document_id` int(11) NOT NULL,
  `action_by` int(11) DEFAULT NULL,
  `action_role` varchar(120) DEFAULT NULL,
  `action_taken` varchar(120) NOT NULL,
  `previous_status` varchar(120) DEFAULT NULL,
  `new_status` varchar(120) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangay_locations`
--

CREATE TABLE `barangay_locations` (
  `id` int(11) NOT NULL,
  `province` varchar(150) NOT NULL,
  `municipality` varchar(150) NOT NULL,
  `barangay` varchar(150) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_locations`
--

INSERT INTO `barangay_locations` (`id`, `province`, `municipality`, `barangay`, `latitude`, `longitude`) VALUES
(1, 'Leyte', 'Tacloban City', 'Barangay 88 San Jose', 11.228900, 125.003700),
(2, 'Leyte', 'Tacloban City', 'Barangay 95 Caibaan', 11.231200, 125.014200),
(3, 'Leyte', 'Palo', 'Baras', 11.156900, 124.999300),
(4, 'Leyte', 'Palo', 'Pawing', 11.180300, 124.984500),
(5, 'Leyte', 'Ormoc City', 'Cogon', 11.005400, 124.608400),
(6, 'Leyte', 'Dulag', 'Market Site', 10.954000, 125.031100),
(7, 'Southern Leyte', 'Maasin City', 'Tagnipa', 10.145900, 124.859500),
(8, 'Samar', 'Catbalogan City', 'Mercedes', 11.777800, 124.889100),
(9, 'Eastern Samar', 'Borongan City', 'Maypangdan', 11.594800, 125.433500),
(10, 'Northern Samar', 'Catarman', 'Dalakit', 12.504000, 124.640000),
(11, 'Biliran', 'Naval', 'Calumpang', 11.557800, 124.397800),
(12, 'Leyte', 'Tacloban City', 'Barangay 88 San Jose', 11.228900, 125.003700),
(13, 'Leyte', 'Tacloban City', 'Barangay 95 Caibaan', 11.231200, 125.014200),
(14, 'Leyte', 'Palo', 'Baras', 11.156900, 124.999300),
(15, 'Leyte', 'Palo', 'Pawing', 11.180300, 124.984500),
(16, 'Leyte', 'Ormoc City', 'Cogon', 11.005400, 124.608400),
(17, 'Leyte', 'Ormoc City', 'Can-adieng', 11.011500, 124.612000),
(18, 'Leyte', 'Baybay City', 'Pangasugan', 10.742300, 124.799900),
(19, 'Leyte', 'Dulag', 'Market Site', 10.954000, 125.031100),
(20, 'Leyte', 'Tanauan', 'Canramos', 11.105700, 125.014700),
(21, 'Southern Leyte', 'Maasin City', 'Tagnipa', 10.145900, 124.859500),
(22, 'Southern Leyte', 'Sogod', 'Zone III', 10.384000, 124.982200),
(23, 'Samar', 'Catbalogan City', 'Mercedes', 11.777800, 124.889100),
(24, 'Samar', 'Basey', 'Buscada', 11.282100, 125.068400),
(25, 'Samar', 'Calbayog City', 'Aguit-itan', 12.067400, 124.598700),
(26, 'Eastern Samar', 'Borongan City', 'Maypangdan', 11.594800, 125.433500),
(27, 'Eastern Samar', 'Guiuan', 'Surok', 11.030800, 125.724000),
(28, 'Northern Samar', 'Catarman', 'Dalakit', 12.504000, 124.640000),
(29, 'Northern Samar', 'Allen', 'Sabang', 12.505800, 124.284700),
(30, 'Biliran', 'Naval', 'Calumpang', 11.557800, 124.397800),
(31, 'Biliran', 'Caibiran', 'Tomalistis', 11.570900, 124.582300),
(32, 'Leyte', 'Tacloban City', 'Barangay 88 San Jose', 11.228900, 125.003700),
(33, 'Leyte', 'Tacloban City', 'Barangay 95 Caibaan', 11.231200, 125.014200),
(34, 'Leyte', 'Palo', 'Baras', 11.156900, 124.999300),
(35, 'Leyte', 'Palo', 'Pawing', 11.180300, 124.984500),
(36, 'Leyte', 'Ormoc City', 'Cogon', 11.005400, 124.608400),
(37, 'Leyte', 'Ormoc City', 'Can-adieng', 11.011500, 124.612000),
(38, 'Leyte', 'Baybay City', 'Pangasugan', 10.742300, 124.799900),
(39, 'Leyte', 'Dulag', 'Market Site', 10.954000, 125.031100),
(40, 'Leyte', 'Tanauan', 'Canramos', 11.105700, 125.014700),
(41, 'Southern Leyte', 'Maasin City', 'Tagnipa', 10.145900, 124.859500),
(42, 'Southern Leyte', 'Sogod', 'Zone III', 10.384000, 124.982200),
(43, 'Samar', 'Catbalogan City', 'Mercedes', 11.777800, 124.889100),
(44, 'Samar', 'Basey', 'Buscada', 11.282100, 125.068400),
(45, 'Samar', 'Calbayog City', 'Aguit-itan', 12.067400, 124.598700),
(46, 'Eastern Samar', 'Borongan City', 'Maypangdan', 11.594800, 125.433500),
(47, 'Eastern Samar', 'Guiuan', 'Surok', 11.030800, 125.724000),
(48, 'Northern Samar', 'Catarman', 'Dalakit', 12.504000, 124.640000),
(49, 'Northern Samar', 'Allen', 'Sabang', 12.505800, 124.284700),
(50, 'Biliran', 'Naval', 'Calumpang', 11.557800, 124.397800),
(51, 'Biliran', 'Caibiran', 'Tomalistis', 11.570900, 124.582300);

-- --------------------------------------------------------

--
-- Table structure for table `document_approvals`
--

CREATE TABLE `document_approvals` (
  `id` int(11) NOT NULL,
  `document_type` varchar(80) NOT NULL,
  `document_id` int(11) NOT NULL,
  `approval_level` int(11) NOT NULL,
  `approval_role` varchar(120) NOT NULL,
  `approver_user_id` int(11) DEFAULT NULL,
  `approver_name` varchar(180) DEFAULT NULL,
  `approver_signature_image` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Not Approved','For Revision','Recalled') DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `province` varchar(150) NOT NULL,
  `municipality` varchar(150) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `province`, `municipality`, `latitude`, `longitude`) VALUES
(1, 'Leyte', 'Tacloban City', 11.244800, 125.003900),
(2, 'Leyte', 'Palo', 11.157500, 124.990300),
(3, 'Leyte', 'Ormoc City', 11.005000, 124.607500),
(4, 'Leyte', 'Dulag', 10.952500, 125.031100),
(5, 'Southern Leyte', 'Maasin City', 10.133600, 124.844700),
(6, 'Samar', 'Catbalogan City', 11.775300, 124.886100),
(7, 'Eastern Samar', 'Borongan City', 11.607300, 125.431900),
(8, 'Northern Samar', 'Catarman', 12.498900, 124.637700),
(9, 'Biliran', 'Naval', 11.560500, 124.397200),
(10, 'Leyte', 'Tacloban City', 11.231200, 125.014200),
(11, 'Leyte', 'Palo', 11.180300, 124.984500),
(12, 'Leyte', 'Ormoc City', 11.011500, 124.612000),
(13, 'Leyte', 'Baybay City', 10.742300, 124.799900),
(14, 'Leyte', 'Dulag', 10.954000, 125.031100),
(15, 'Leyte', 'Tanauan', 11.105700, 125.014700),
(16, 'Southern Leyte', 'Maasin City', 10.145900, 124.859500),
(17, 'Southern Leyte', 'Sogod', 10.384000, 124.982200),
(18, 'Samar', 'Catbalogan City', 11.777800, 124.889100),
(19, 'Samar', 'Basey', 11.282100, 125.068400),
(20, 'Samar', 'Calbayog City', 12.067400, 124.598700),
(21, 'Eastern Samar', 'Borongan City', 11.594800, 125.433500),
(22, 'Eastern Samar', 'Guiuan', 11.030800, 125.724000),
(23, 'Northern Samar', 'Catarman', 12.504000, 124.640000),
(24, 'Northern Samar', 'Allen', 12.505800, 124.284700),
(25, 'Biliran', 'Naval', 11.557800, 124.397800),
(26, 'Biliran', 'Caibiran', 11.570900, 124.582300);

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_entries`
--

CREATE TABLE `monitoring_entries` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `activity_title` varchar(255) DEFAULT NULL,
  `monitoring_date` date DEFAULT NULL,
  `source_of_fund` varchar(255) DEFAULT NULL,
  `status` enum('On-going','Completed','Inactive','Expired','Terminated') DEFAULT 'On-going',
  `terminal_report_date` date DEFAULT NULL,
  `activity_description` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `evsu_campus` varchar(150) DEFAULT NULL,
  `campus_school` varchar(50) DEFAULT NULL,
  `barangay` varchar(150) DEFAULT NULL,
  `municipality` varchar(150) DEFAULT NULL,
  `province` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitoring_entries`
--

INSERT INTO `monitoring_entries` (`id`, `project_id`, `activity_title`, `monitoring_date`, `source_of_fund`, `status`, `terminal_report_date`, `activity_description`, `remarks`, `barangay`, `municipality`, `province`, `created_at`) VALUES
(1, 1, 'Initial coordination and beneficiary profiling', '2026-01-20', 'IGF', 'On-going', NULL, 'Initial coordination and beneficiary profiling', 'Initial implementation completed.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-19 08:03:58'),
(2, 2, 'Final implementation activity', '2026-04-20', 'IGF', 'Completed', NULL, 'Final implementation activity', 'Terminal report still required.', 'Baras', 'Palo', 'Leyte', '2026-05-19 08:03:58'),
(3, 3, 'Needs assessment and profiling', '2026-01-01', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Tacloban City 01', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(4, 3, 'Coordination with partner LGU', '2026-02-02', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Tacloban City 01', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(5, 3, 'Training implementation', '2026-03-03', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project PLAYSCAPE - Tacloban City 01', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(6, 3, 'Monitoring and validation visit', '2026-04-04', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Tacloban City 01', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(7, 4, 'Needs assessment and profiling', '2026-02-02', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Tacloban City 02', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(8, 4, 'Coordination with partner LGU', '2026-03-03', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Tacloban City 02', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(9, 4, 'Training implementation', '2026-04-04', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project KAHIMSOG - Tacloban City 02', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(10, 4, 'Monitoring and validation visit', '2026-05-05', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Tacloban City 02', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(11, 5, 'Needs assessment and profiling', '2026-03-03', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Palo 03', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(12, 5, 'Coordination with partner LGU', '2026-04-04', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Palo 03', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(13, 5, 'Training implementation', '2026-05-05', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project KABUHAYAN - Palo 03', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(14, 5, 'Monitoring and validation visit', '2026-06-06', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Palo 03', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(15, 6, 'Needs assessment and profiling', '2026-04-04', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Palo 04', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(16, 6, 'Coordination with partner LGU', '2026-05-05', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Palo 04', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(17, 6, 'Training implementation', '2026-06-06', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project LUNTIAN - Palo 04', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(18, 6, 'Monitoring and validation visit', '2026-07-07', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Palo 04', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(19, 7, 'Needs assessment and profiling', '2026-05-05', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Ormoc City 05', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(20, 7, 'Coordination with partner LGU', '2026-06-06', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Ormoc City 05', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(21, 7, 'Training implementation', '2026-07-07', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project TECH-READY - Ormoc City 05', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(22, 7, 'Monitoring and validation visit', '2026-08-08', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Ormoc City 05', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(23, 8, 'Needs assessment and profiling', '2026-06-06', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project ALERT - Ormoc City 06', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(24, 8, 'Coordination with partner LGU', '2026-07-07', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project ALERT - Ormoc City 06', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(25, 8, 'Training implementation', '2026-08-08', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project ALERT - Ormoc City 06', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(26, 8, 'Monitoring and validation visit', '2026-09-09', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project ALERT - Ormoc City 06', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(27, 9, 'Needs assessment and profiling', '2026-01-07', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Baybay City 07', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(28, 9, 'Coordination with partner LGU', '2026-02-08', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Baybay City 07', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(29, 9, 'Training implementation', '2026-03-09', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project PLAYSCAPE - Baybay City 07', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(30, 9, 'Monitoring and validation visit', '2026-04-10', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Baybay City 07', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(31, 10, 'Needs assessment and profiling', '2026-02-08', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Dulag 08', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(32, 10, 'Coordination with partner LGU', '2026-03-09', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Dulag 08', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(33, 10, 'Training implementation', '2026-04-10', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project KAHIMSOG - Dulag 08', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(34, 10, 'Monitoring and validation visit', '2026-05-11', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Dulag 08', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(35, 11, 'Needs assessment and profiling', '2026-03-09', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Tanauan 09', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(36, 11, 'Coordination with partner LGU', '2026-04-10', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Tanauan 09', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(37, 11, 'Training implementation', '2026-05-11', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project KABUHAYAN - Tanauan 09', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(38, 11, 'Monitoring and validation visit', '2026-06-12', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Tanauan 09', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(39, 12, 'Needs assessment and profiling', '2026-04-10', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Maasin City 10', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(40, 12, 'Coordination with partner LGU', '2026-05-11', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Maasin City 10', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(41, 12, 'Training implementation', '2026-06-12', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project LUNTIAN - Maasin City 10', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(42, 12, 'Monitoring and validation visit', '2026-07-13', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Maasin City 10', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(43, 13, 'Needs assessment and profiling', '2026-05-11', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Sogod 11', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(44, 13, 'Coordination with partner LGU', '2026-06-12', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Sogod 11', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(45, 13, 'Training implementation', '2026-07-13', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project TECH-READY - Sogod 11', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(46, 13, 'Monitoring and validation visit', '2026-08-14', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Sogod 11', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(47, 14, 'Needs assessment and profiling', '2026-06-12', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project ALERT - Catbalogan City 12', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(48, 14, 'Coordination with partner LGU', '2026-07-13', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project ALERT - Catbalogan City 12', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(49, 14, 'Training implementation', '2026-08-14', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project ALERT - Catbalogan City 12', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(50, 14, 'Monitoring and validation visit', '2026-09-15', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project ALERT - Catbalogan City 12', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(51, 15, 'Needs assessment and profiling', '2026-01-13', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Basey 13', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(52, 15, 'Coordination with partner LGU', '2026-02-14', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Basey 13', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(53, 15, 'Training implementation', '2026-03-15', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project PLAYSCAPE - Basey 13', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(54, 15, 'Monitoring and validation visit', '2026-04-16', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Basey 13', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(55, 16, 'Needs assessment and profiling', '2026-02-14', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Calbayog City 14', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(56, 16, 'Coordination with partner LGU', '2026-03-15', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Calbayog City 14', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(57, 16, 'Training implementation', '2026-04-16', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project KAHIMSOG - Calbayog City 14', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(58, 16, 'Monitoring and validation visit', '2026-05-17', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Calbayog City 14', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(59, 17, 'Needs assessment and profiling', '2026-03-15', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Borongan City 15', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(60, 17, 'Coordination with partner LGU', '2026-04-16', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Borongan City 15', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(61, 17, 'Training implementation', '2026-05-17', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project KABUHAYAN - Borongan City 15', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(62, 17, 'Monitoring and validation visit', '2026-06-18', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Borongan City 15', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(63, 18, 'Needs assessment and profiling', '2026-04-16', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Guiuan 16', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(64, 18, 'Coordination with partner LGU', '2026-05-17', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Guiuan 16', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(65, 18, 'Training implementation', '2026-06-18', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project LUNTIAN - Guiuan 16', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(66, 18, 'Monitoring and validation visit', '2026-07-19', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Guiuan 16', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(67, 19, 'Needs assessment and profiling', '2026-05-17', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Catarman 17', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(68, 19, 'Coordination with partner LGU', '2026-06-18', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Catarman 17', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(69, 19, 'Training implementation', '2026-07-19', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project TECH-READY - Catarman 17', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(70, 19, 'Monitoring and validation visit', '2026-08-20', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Catarman 17', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(71, 20, 'Needs assessment and profiling', '2026-06-18', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project ALERT - Allen 18', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(72, 20, 'Coordination with partner LGU', '2026-07-19', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project ALERT - Allen 18', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(73, 20, 'Training implementation', '2026-08-20', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project ALERT - Allen 18', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(74, 20, 'Monitoring and validation visit', '2026-09-21', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project ALERT - Allen 18', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(75, 21, 'Needs assessment and profiling', '2026-01-19', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Naval 19', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(76, 21, 'Coordination with partner LGU', '2026-02-20', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Naval 19', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(77, 21, 'Training implementation', '2026-03-21', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project PLAYSCAPE - Naval 19', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(78, 21, 'Monitoring and validation visit', '2026-04-22', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Naval 19', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(79, 22, 'Needs assessment and profiling', '2026-02-20', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Caibiran 20', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(80, 22, 'Coordination with partner LGU', '2026-03-21', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Caibiran 20', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(81, 22, 'Training implementation', '2026-04-22', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project KAHIMSOG - Caibiran 20', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(82, 22, 'Monitoring and validation visit', '2026-05-23', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Caibiran 20', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(83, 23, 'Needs assessment and profiling', '2026-03-21', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Tacloban City 21', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(84, 23, 'Coordination with partner LGU', '2026-04-22', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Tacloban City 21', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(85, 23, 'Training implementation', '2026-05-23', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project KABUHAYAN - Tacloban City 21', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(86, 23, 'Monitoring and validation visit', '2026-06-24', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Tacloban City 21', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(87, 24, 'Needs assessment and profiling', '2026-04-22', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Tacloban City 22', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(88, 24, 'Coordination with partner LGU', '2026-05-23', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Tacloban City 22', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(89, 24, 'Training implementation', '2026-06-24', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project LUNTIAN - Tacloban City 22', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(90, 24, 'Monitoring and validation visit', '2026-07-25', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Tacloban City 22', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(91, 25, 'Needs assessment and profiling', '2026-05-23', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Palo 23', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(92, 25, 'Coordination with partner LGU', '2026-06-24', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Palo 23', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(93, 25, 'Training implementation', '2026-07-25', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project TECH-READY - Palo 23', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(94, 25, 'Monitoring and validation visit', '2026-08-26', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Palo 23', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(95, 26, 'Needs assessment and profiling', '2026-06-24', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project ALERT - Palo 24', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(96, 26, 'Coordination with partner LGU', '2026-07-25', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project ALERT - Palo 24', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(97, 26, 'Training implementation', '2026-08-26', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project ALERT - Palo 24', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(98, 26, 'Monitoring and validation visit', '2026-09-27', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project ALERT - Palo 24', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(99, 27, 'Needs assessment and profiling', '2026-01-25', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Ormoc City 25', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(100, 27, 'Coordination with partner LGU', '2026-02-26', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Ormoc City 25', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(101, 27, 'Training implementation', '2026-03-27', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project PLAYSCAPE - Ormoc City 25', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(102, 27, 'Monitoring and validation visit', '2026-04-28', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Ormoc City 25', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(103, 28, 'Needs assessment and profiling', '2026-02-01', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Ormoc City 26', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(104, 28, 'Coordination with partner LGU', '2026-03-02', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Ormoc City 26', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(105, 28, 'Training implementation', '2026-04-03', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project KAHIMSOG - Ormoc City 26', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(106, 28, 'Monitoring and validation visit', '2026-05-04', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Ormoc City 26', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(107, 29, 'Needs assessment and profiling', '2026-03-02', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Baybay City 27', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(108, 29, 'Coordination with partner LGU', '2026-04-03', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Baybay City 27', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(109, 29, 'Training implementation', '2026-05-04', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project KABUHAYAN - Baybay City 27', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(110, 29, 'Monitoring and validation visit', '2026-06-05', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Baybay City 27', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(111, 30, 'Needs assessment and profiling', '2026-04-03', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Dulag 28', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(112, 30, 'Coordination with partner LGU', '2026-05-04', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Dulag 28', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(113, 30, 'Training implementation', '2026-06-05', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project LUNTIAN - Dulag 28', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(114, 30, 'Monitoring and validation visit', '2026-07-06', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Dulag 28', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(115, 31, 'Needs assessment and profiling', '2026-05-04', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Tanauan 29', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(116, 31, 'Coordination with partner LGU', '2026-06-05', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Tanauan 29', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(117, 31, 'Training implementation', '2026-07-06', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project TECH-READY - Tanauan 29', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(118, 31, 'Monitoring and validation visit', '2026-08-07', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Tanauan 29', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(119, 32, 'Needs assessment and profiling', '2026-06-05', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project ALERT - Maasin City 30', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(120, 32, 'Coordination with partner LGU', '2026-07-06', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project ALERT - Maasin City 30', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(121, 32, 'Training implementation', '2026-08-07', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project ALERT - Maasin City 30', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(122, 32, 'Monitoring and validation visit', '2026-09-08', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project ALERT - Maasin City 30', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(123, 33, 'Needs assessment and profiling', '2026-01-06', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Sogod 31', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(124, 33, 'Coordination with partner LGU', '2026-02-07', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Sogod 31', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(125, 33, 'Training implementation', '2026-03-08', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project PLAYSCAPE - Sogod 31', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(126, 33, 'Monitoring and validation visit', '2026-04-09', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Sogod 31', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(127, 34, 'Needs assessment and profiling', '2026-02-07', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Catbalogan City 32', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(128, 34, 'Coordination with partner LGU', '2026-03-08', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Catbalogan City 32', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(129, 34, 'Training implementation', '2026-04-09', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project KAHIMSOG - Catbalogan City 32', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(130, 34, 'Monitoring and validation visit', '2026-05-10', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Catbalogan City 32', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(131, 35, 'Needs assessment and profiling', '2026-03-08', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Basey 33', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(132, 35, 'Coordination with partner LGU', '2026-04-09', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Basey 33', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(133, 35, 'Training implementation', '2026-05-10', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project KABUHAYAN - Basey 33', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(134, 35, 'Monitoring and validation visit', '2026-06-11', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Basey 33', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(135, 36, 'Needs assessment and profiling', '2026-04-09', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Calbayog City 34', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(136, 36, 'Coordination with partner LGU', '2026-05-10', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Calbayog City 34', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(137, 36, 'Training implementation', '2026-06-11', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project LUNTIAN - Calbayog City 34', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(138, 36, 'Monitoring and validation visit', '2026-07-12', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Calbayog City 34', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(139, 37, 'Needs assessment and profiling', '2026-05-10', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Borongan City 35', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(140, 37, 'Coordination with partner LGU', '2026-06-11', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Borongan City 35', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(141, 37, 'Training implementation', '2026-07-12', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project TECH-READY - Borongan City 35', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(142, 37, 'Monitoring and validation visit', '2026-08-13', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Borongan City 35', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(143, 38, 'Needs assessment and profiling', '2026-06-11', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project ALERT - Guiuan 36', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(144, 38, 'Coordination with partner LGU', '2026-07-12', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project ALERT - Guiuan 36', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(145, 38, 'Training implementation', '2026-08-13', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project ALERT - Guiuan 36', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(146, 38, 'Monitoring and validation visit', '2026-09-14', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project ALERT - Guiuan 36', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(147, 39, 'Needs assessment and profiling', '2026-01-12', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Catarman 37', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(148, 39, 'Coordination with partner LGU', '2026-02-13', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Catarman 37', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(149, 39, 'Training implementation', '2026-03-14', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project PLAYSCAPE - Catarman 37', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(150, 39, 'Monitoring and validation visit', '2026-04-15', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Catarman 37', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(151, 40, 'Needs assessment and profiling', '2026-02-13', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Allen 38', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(152, 40, 'Coordination with partner LGU', '2026-03-14', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Allen 38', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(153, 40, 'Training implementation', '2026-04-15', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project KAHIMSOG - Allen 38', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(154, 40, 'Monitoring and validation visit', '2026-05-16', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Allen 38', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(155, 41, 'Needs assessment and profiling', '2026-03-14', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Naval 39', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(156, 41, 'Coordination with partner LGU', '2026-04-15', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Naval 39', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(157, 41, 'Training implementation', '2026-05-16', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project KABUHAYAN - Naval 39', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(158, 41, 'Monitoring and validation visit', '2026-06-17', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Naval 39', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(159, 42, 'Needs assessment and profiling', '2026-04-15', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Caibiran 40', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(160, 42, 'Coordination with partner LGU', '2026-05-16', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Caibiran 40', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(161, 42, 'Training implementation', '2026-06-17', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project LUNTIAN - Caibiran 40', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(162, 42, 'Monitoring and validation visit', '2026-07-18', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Caibiran 40', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(163, 43, 'Needs assessment and profiling', '2026-05-16', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Tacloban City 41', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(164, 43, 'Coordination with partner LGU', '2026-06-17', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Tacloban City 41', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(165, 43, 'Training implementation', '2026-07-18', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project TECH-READY - Tacloban City 41', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(166, 43, 'Monitoring and validation visit', '2026-08-19', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Tacloban City 41', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 88 San Jose', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(167, 44, 'Needs assessment and profiling', '2026-06-17', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project ALERT - Tacloban City 42', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(168, 44, 'Coordination with partner LGU', '2026-07-18', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project ALERT - Tacloban City 42', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(169, 44, 'Training implementation', '2026-08-19', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project ALERT - Tacloban City 42', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(170, 44, 'Monitoring and validation visit', '2026-09-20', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project ALERT - Tacloban City 42', 'Demo monitoring record for Chapter IV/V output.', 'Barangay 95 Caibaan', 'Tacloban City', 'Leyte', '2026-05-22 03:50:14'),
(171, 45, 'Needs assessment and profiling', '2026-01-18', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Palo 43', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(172, 45, 'Coordination with partner LGU', '2026-02-19', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Palo 43', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(173, 45, 'Training implementation', '2026-03-20', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project PLAYSCAPE - Palo 43', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(174, 45, 'Monitoring and validation visit', '2026-04-21', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Palo 43', 'Demo monitoring record for Chapter IV/V output.', 'Baras', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(175, 46, 'Needs assessment and profiling', '2026-02-19', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Palo 44', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(176, 46, 'Coordination with partner LGU', '2026-03-20', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Palo 44', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(177, 46, 'Training implementation', '2026-04-21', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project KAHIMSOG - Palo 44', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(178, 46, 'Monitoring and validation visit', '2026-05-22', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Palo 44', 'Demo monitoring record for Chapter IV/V output.', 'Pawing', 'Palo', 'Leyte', '2026-05-22 03:50:14'),
(179, 47, 'Needs assessment and profiling', '2026-03-20', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Ormoc City 45', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(180, 47, 'Coordination with partner LGU', '2026-04-21', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Ormoc City 45', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(181, 47, 'Training implementation', '2026-05-22', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project KABUHAYAN - Ormoc City 45', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(182, 47, 'Monitoring and validation visit', '2026-06-23', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Ormoc City 45', 'Demo monitoring record for Chapter IV/V output.', 'Cogon', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(183, 48, 'Needs assessment and profiling', '2026-04-21', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Ormoc City 46', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(184, 48, 'Coordination with partner LGU', '2026-05-22', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Ormoc City 46', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(185, 48, 'Training implementation', '2026-06-23', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project LUNTIAN - Ormoc City 46', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14');
INSERT INTO `monitoring_entries` (`id`, `project_id`, `activity_title`, `monitoring_date`, `source_of_fund`, `status`, `terminal_report_date`, `activity_description`, `remarks`, `barangay`, `municipality`, `province`, `created_at`) VALUES
(186, 48, 'Monitoring and validation visit', '2026-07-24', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Ormoc City 46', 'Demo monitoring record for Chapter IV/V output.', 'Can-adieng', 'Ormoc City', 'Leyte', '2026-05-22 03:50:14'),
(187, 49, 'Needs assessment and profiling', '2026-05-22', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Baybay City 47', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(188, 49, 'Coordination with partner LGU', '2026-06-23', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Baybay City 47', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(189, 49, 'Training implementation', '2026-07-24', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project TECH-READY - Baybay City 47', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(190, 49, 'Monitoring and validation visit', '2026-08-25', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Baybay City 47', 'Demo monitoring record for Chapter IV/V output.', 'Pangasugan', 'Baybay City', 'Leyte', '2026-05-22 03:50:14'),
(191, 50, 'Needs assessment and profiling', '2026-06-23', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project ALERT - Dulag 48', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(192, 50, 'Coordination with partner LGU', '2026-07-24', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project ALERT - Dulag 48', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(193, 50, 'Training implementation', '2026-08-25', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project ALERT - Dulag 48', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(194, 50, 'Monitoring and validation visit', '2026-09-26', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project ALERT - Dulag 48', 'Demo monitoring record for Chapter IV/V output.', 'Market Site', 'Dulag', 'Leyte', '2026-05-22 03:50:14'),
(195, 51, 'Needs assessment and profiling', '2026-01-24', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Tanauan 49', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(196, 51, 'Coordination with partner LGU', '2026-02-25', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Tanauan 49', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(197, 51, 'Training implementation', '2026-03-26', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project PLAYSCAPE - Tanauan 49', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(198, 51, 'Monitoring and validation visit', '2026-04-27', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Tanauan 49', 'Demo monitoring record for Chapter IV/V output.', 'Canramos', 'Tanauan', 'Leyte', '2026-05-22 03:50:14'),
(199, 52, 'Needs assessment and profiling', '2026-02-25', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Maasin City 50', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(200, 52, 'Coordination with partner LGU', '2026-03-26', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Maasin City 50', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(201, 52, 'Training implementation', '2026-04-27', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project KAHIMSOG - Maasin City 50', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(202, 52, 'Monitoring and validation visit', '2026-05-28', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Maasin City 50', 'Demo monitoring record for Chapter IV/V output.', 'Tagnipa', 'Maasin City', 'Southern Leyte', '2026-05-22 03:50:14'),
(203, 53, 'Needs assessment and profiling', '2026-03-01', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Sogod 51', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(204, 53, 'Coordination with partner LGU', '2026-04-02', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Sogod 51', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(205, 53, 'Training implementation', '2026-05-03', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project KABUHAYAN - Sogod 51', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(206, 53, 'Monitoring and validation visit', '2026-06-04', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Sogod 51', 'Demo monitoring record for Chapter IV/V output.', 'Zone III', 'Sogod', 'Southern Leyte', '2026-05-22 03:50:14'),
(207, 54, 'Needs assessment and profiling', '2026-04-02', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Catbalogan City 52', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(208, 54, 'Coordination with partner LGU', '2026-05-03', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Catbalogan City 52', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(209, 54, 'Training implementation', '2026-06-04', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project LUNTIAN - Catbalogan City 52', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(210, 54, 'Monitoring and validation visit', '2026-07-05', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Catbalogan City 52', 'Demo monitoring record for Chapter IV/V output.', 'Mercedes', 'Catbalogan City', 'Samar', '2026-05-22 03:50:14'),
(211, 55, 'Needs assessment and profiling', '2026-05-03', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Basey 53', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(212, 55, 'Coordination with partner LGU', '2026-06-04', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Basey 53', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(213, 55, 'Training implementation', '2026-07-05', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project TECH-READY - Basey 53', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(214, 55, 'Monitoring and validation visit', '2026-08-06', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Basey 53', 'Demo monitoring record for Chapter IV/V output.', 'Buscada', 'Basey', 'Samar', '2026-05-22 03:50:14'),
(215, 56, 'Needs assessment and profiling', '2026-06-04', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project ALERT - Calbayog City 54', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(216, 56, 'Coordination with partner LGU', '2026-07-05', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project ALERT - Calbayog City 54', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(217, 56, 'Training implementation', '2026-08-06', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project ALERT - Calbayog City 54', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(218, 56, 'Monitoring and validation visit', '2026-09-07', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project ALERT - Calbayog City 54', 'Demo monitoring record for Chapter IV/V output.', 'Aguit-itan', 'Calbayog City', 'Samar', '2026-05-22 03:50:14'),
(219, 57, 'Needs assessment and profiling', '2026-01-05', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project PLAYSCAPE - Borongan City 55', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(220, 57, 'Coordination with partner LGU', '2026-02-06', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project PLAYSCAPE - Borongan City 55', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(221, 57, 'Training implementation', '2026-03-07', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project PLAYSCAPE - Borongan City 55', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(222, 57, 'Monitoring and validation visit', '2026-04-08', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project PLAYSCAPE - Borongan City 55', 'Demo monitoring record for Chapter IV/V output.', 'Maypangdan', 'Borongan City', 'Eastern Samar', '2026-05-22 03:50:14'),
(223, 58, 'Needs assessment and profiling', '2026-02-06', 'IGF', 'On-going', NULL, 'Needs assessment and profiling conducted for Project KAHIMSOG - Guiuan 56', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(224, 58, 'Coordination with partner LGU', '2026-03-07', 'IGF', 'On-going', NULL, 'Coordination with partner LGU conducted for Project KAHIMSOG - Guiuan 56', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(225, 58, 'Training implementation', '2026-04-08', 'IGF', 'On-going', NULL, 'Training implementation conducted for Project KAHIMSOG - Guiuan 56', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(226, 58, 'Monitoring and validation visit', '2026-05-09', 'IGF', 'On-going', NULL, 'Monitoring and validation visit conducted for Project KAHIMSOG - Guiuan 56', 'Demo monitoring record for Chapter IV/V output.', 'Surok', 'Guiuan', 'Eastern Samar', '2026-05-22 03:50:14'),
(227, 59, 'Needs assessment and profiling', '2026-03-07', 'IGF', 'Completed', NULL, 'Needs assessment and profiling conducted for Project KABUHAYAN - Catarman 57', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(228, 59, 'Coordination with partner LGU', '2026-04-08', 'IGF', 'Completed', NULL, 'Coordination with partner LGU conducted for Project KABUHAYAN - Catarman 57', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(229, 59, 'Training implementation', '2026-05-09', 'IGF', 'Completed', NULL, 'Training implementation conducted for Project KABUHAYAN - Catarman 57', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(230, 59, 'Monitoring and validation visit', '2026-06-10', 'IGF', 'Completed', NULL, 'Monitoring and validation visit conducted for Project KABUHAYAN - Catarman 57', 'Demo monitoring record for Chapter IV/V output.', 'Dalakit', 'Catarman', 'Northern Samar', '2026-05-22 03:50:14'),
(231, 60, 'Needs assessment and profiling', '2026-04-08', 'IGF', 'Inactive', NULL, 'Needs assessment and profiling conducted for Project LUNTIAN - Allen 58', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(232, 60, 'Coordination with partner LGU', '2026-05-09', 'IGF', 'Inactive', NULL, 'Coordination with partner LGU conducted for Project LUNTIAN - Allen 58', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(233, 60, 'Training implementation', '2026-06-10', 'IGF', 'Inactive', NULL, 'Training implementation conducted for Project LUNTIAN - Allen 58', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(234, 60, 'Monitoring and validation visit', '2026-07-11', 'IGF', 'Inactive', NULL, 'Monitoring and validation visit conducted for Project LUNTIAN - Allen 58', 'Demo monitoring record for Chapter IV/V output.', 'Sabang', 'Allen', 'Northern Samar', '2026-05-22 03:50:14'),
(235, 61, 'Needs assessment and profiling', '2026-05-09', 'IGF', 'Expired', NULL, 'Needs assessment and profiling conducted for Project TECH-READY - Naval 59', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(236, 61, 'Coordination with partner LGU', '2026-06-10', 'IGF', 'Expired', NULL, 'Coordination with partner LGU conducted for Project TECH-READY - Naval 59', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(237, 61, 'Training implementation', '2026-07-11', 'IGF', 'Expired', NULL, 'Training implementation conducted for Project TECH-READY - Naval 59', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(238, 61, 'Monitoring and validation visit', '2026-08-12', 'IGF', 'Expired', NULL, 'Monitoring and validation visit conducted for Project TECH-READY - Naval 59', 'Demo monitoring record for Chapter IV/V output.', 'Calumpang', 'Naval', 'Biliran', '2026-05-22 03:50:14'),
(239, 62, 'Needs assessment and profiling', '2026-06-10', 'IGF', 'Terminated', NULL, 'Needs assessment and profiling conducted for Project ALERT - Caibiran 60', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(240, 62, 'Coordination with partner LGU', '2026-07-11', 'IGF', 'Terminated', NULL, 'Coordination with partner LGU conducted for Project ALERT - Caibiran 60', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(241, 62, 'Training implementation', '2026-08-12', 'IGF', 'Terminated', NULL, 'Training implementation conducted for Project ALERT - Caibiran 60', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14'),
(242, 62, 'Monitoring and validation visit', '2026-09-13', 'IGF', 'Terminated', NULL, 'Monitoring and validation visit conducted for Project ALERT - Caibiran 60', 'Demo monitoring record for Chapter IV/V output.', 'Tomalistis', 'Caibiran', 'Biliran', '2026-05-22 03:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `program_title` varchar(255) NOT NULL,
  `program_description` text DEFAULT NULL,
  `leader` varchar(180) DEFAULT NULL,
  `assistant_leader` varchar(180) DEFAULT NULL,
  `members` text DEFAULT NULL,
  `project_cost` decimal(12,2) DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `special_order_no` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `program_title`, `program_description`, `leader`, `assistant_leader`, `members`, `project_cost`, `start_date`, `end_date`, `special_order_no`, `created_at`) VALUES
(1, 'PAGHIMANGNO: Community Extension Program', 'Community-based extension services program.', 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'CICT Faculty Members', 25000.00, '2026-01-20', '2026-12-30', 'SO-2026-011', '2026-05-19 08:03:58'),
(2, 'Community Education and Literacy Extension Program', 'Demo extension program for Region 8 coverage.', 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 60000.00, '2026-01-01', '2026-12-31', 'SO-PROG-BULK-001', '2026-05-22 03:50:14'),
(3, 'Health and Disaster Preparedness Program', 'Demo extension program for Region 8 coverage.', 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 70000.00, '2026-01-01', '2026-12-31', 'SO-PROG-BULK-002', '2026-05-22 03:50:14'),
(4, 'Livelihood and Skills Development Program', 'Demo extension program for Region 8 coverage.', 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 80000.00, '2026-01-01', '2026-12-31', 'SO-PROG-BULK-003', '2026-05-22 03:50:14'),
(5, 'Agriculture and Environment Program', 'Demo extension program for Region 8 coverage.', 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 90000.00, '2026-01-01', '2026-12-31', 'SO-PROG-BULK-004', '2026-05-22 03:50:14'),
(6, 'Technology Training and Digital Governance Program', 'Demo extension program for Region 8 coverage.', 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 100000.00, '2026-01-01', '2026-12-31', 'SO-PROG-BULK-005', '2026-05-22 03:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `project_title` varchar(255) NOT NULL,
  `sdg` varchar(180) DEFAULT NULL,
  `partners` varchar(255) DEFAULT NULL,
  `partner` varchar(255) DEFAULT NULL,
  `type_of_clientele` varchar(180) DEFAULT NULL,
  `clientele_type` varchar(180) DEFAULT NULL,
  `leader` varchar(180) DEFAULT NULL,
  `assistant_leader` varchar(180) DEFAULT NULL,
  `members` text DEFAULT NULL,
  `participants` int(11) DEFAULT 0,
  `project_cost` decimal(12,2) DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `special_order_no` varchar(120) DEFAULT NULL,
  `province` varchar(150) DEFAULT NULL,
  `municipality` varchar(150) DEFAULT NULL,
  `barangay` varchar(150) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `barangay_latitude` decimal(10,6) DEFAULT NULL,
  `barangay_longitude` decimal(10,6) DEFAULT NULL,
  `status` enum('On-going','Completed','Inactive','Expired','Terminated') DEFAULT 'On-going',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `program_id`, `project_title`, `sdg`, `partners`, `partner`, `type_of_clientele`, `clientele_type`, `leader`, `assistant_leader`, `members`, `participants`, `project_cost`, `start_date`, `end_date`, `special_order_no`, `province`, `municipality`, `barangay`, `latitude`, `longitude`, `barangay_latitude`, `barangay_longitude`, `status`, `created_at`) VALUES
(1, 1, 'Project PLAYSCAPE', 'SDG 4: Quality Education', 'DepEd; Barangay Council', NULL, 'Students and Teachers', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'CICT Faculty Members', 60, 25000.00, '2026-01-20', '2026-12-30', 'SO-2026-011', 'Leyte', 'Tacloban City', 'Barangay 88 San Jose', 11.228900, 125.003700, NULL, NULL, 'On-going', '2026-05-19 08:03:58'),
(2, 1, 'Project LAKAS', 'SDG 3: Good Health and Well-being', 'Barangay Health Workers', NULL, 'Community Residents', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 45, 30000.00, '2026-04-20', '2026-12-30', 'SO-2026-012', 'Leyte', 'Palo', 'Baras', 11.156900, 124.999300, NULL, NULL, 'Completed', '2026-05-19 08:03:58'),
(3, 2, 'Project PLAYSCAPE - Tacloban City 01', 'SDG 4: Quality Education', 'DepEd; Barangay Council', NULL, 'Students and Teachers', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 30, 25000.00, '2026-01-01', '2026-12-01', 'SO-BULK-2026-001', 'Leyte', 'Tacloban City', 'Barangay 88 San Jose', 11.228900, 125.003700, 11.228900, 125.003700, 'On-going', '2026-05-22 03:50:14'),
(4, 3, 'Project KAHIMSOG - Tacloban City 02', 'SDG 3: Good Health and Well-being', 'TESDA; LGU', NULL, 'Community Residents', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 40, 35000.00, '2026-02-02', '2026-12-02', 'SO-BULK-2026-002', 'Leyte', 'Tacloban City', 'Barangay 95 Caibaan', 11.231200, 125.014200, 11.231200, 125.014200, 'Completed', '2026-05-22 03:50:14'),
(5, 4, 'Project KABUHAYAN - Palo 03', 'SDG 8: Decent Work and Economic Growth', 'DOH; BHW', NULL, 'Women Association', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 50, 45000.00, '2026-03-03', '2026-12-03', 'SO-BULK-2026-003', 'Leyte', 'Palo', 'Baras', 11.156900, 124.999300, 11.156900, 124.999300, 'Inactive', '2026-05-22 03:50:14'),
(6, 5, 'Project LUNTIAN - Palo 04', 'SDG 13: Climate Action', 'DA; Farmers Association', NULL, 'Youth Volunteers', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 60, 55000.00, '2026-04-04', '2026-12-04', 'SO-BULK-2026-004', 'Leyte', 'Palo', 'Pawing', 11.180300, 124.984500, 11.180300, 124.984500, 'Expired', '2026-05-22 03:50:14'),
(7, 6, 'Project TECH-READY - Ormoc City 05', 'SDG 9: Industry, Innovation and Infrastructure', 'LGU; Barangay Council', NULL, 'Barangay Officials', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 70, 65000.00, '2026-05-05', '2026-12-05', 'SO-BULK-2026-005', 'Leyte', 'Ormoc City', 'Cogon', 11.005400, 124.608400, 11.005400, 124.608400, 'Terminated', '2026-05-22 03:50:14'),
(8, 2, 'Project ALERT - Ormoc City 06', 'SDG 11: Sustainable Cities and Communities', 'DepEd; Barangay Council', NULL, 'DRRM Volunteers', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 80, 75000.00, '2026-06-06', '2026-12-06', 'SO-BULK-2026-006', 'Leyte', 'Ormoc City', 'Can-adieng', 11.011500, 124.612000, 11.011500, 124.612000, 'On-going', '2026-05-22 03:50:14'),
(9, 3, 'Project PLAYSCAPE - Baybay City 07', 'SDG 4: Quality Education', 'TESDA; LGU', NULL, 'Students and Teachers', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 90, 85000.00, '2026-01-07', '2026-12-07', 'SO-BULK-2026-007', 'Leyte', 'Baybay City', 'Pangasugan', 10.742300, 124.799900, 10.742300, 124.799900, 'Completed', '2026-05-22 03:50:14'),
(10, 4, 'Project KAHIMSOG - Dulag 08', 'SDG 3: Good Health and Well-being', 'DOH; BHW', NULL, 'Community Residents', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 100, 95000.00, '2026-02-08', '2026-12-08', 'SO-BULK-2026-008', 'Leyte', 'Dulag', 'Market Site', 10.954000, 125.031100, 10.954000, 125.031100, 'Inactive', '2026-05-22 03:50:14'),
(11, 5, 'Project KABUHAYAN - Tanauan 09', 'SDG 8: Decent Work and Economic Growth', 'DA; Farmers Association', NULL, 'Women Association', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 110, 15000.00, '2026-03-09', '2026-12-09', 'SO-BULK-2026-009', 'Leyte', 'Tanauan', 'Canramos', 11.105700, 125.014700, 11.105700, 125.014700, 'Expired', '2026-05-22 03:50:14'),
(12, 6, 'Project LUNTIAN - Maasin City 10', 'SDG 13: Climate Action', 'LGU; Barangay Council', NULL, 'Youth Volunteers', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 20, 25000.00, '2026-04-10', '2026-12-10', 'SO-BULK-2026-010', 'Southern Leyte', 'Maasin City', 'Tagnipa', 10.145900, 124.859500, 10.145900, 124.859500, 'Terminated', '2026-05-22 03:50:14'),
(13, 2, 'Project TECH-READY - Sogod 11', 'SDG 9: Industry, Innovation and Infrastructure', 'DepEd; Barangay Council', NULL, 'Barangay Officials', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 30, 35000.00, '2026-05-11', '2026-12-11', 'SO-BULK-2026-011', 'Southern Leyte', 'Sogod', 'Zone III', 10.384000, 124.982200, 10.384000, 124.982200, 'On-going', '2026-05-22 03:50:14'),
(14, 3, 'Project ALERT - Catbalogan City 12', 'SDG 11: Sustainable Cities and Communities', 'TESDA; LGU', NULL, 'DRRM Volunteers', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 40, 45000.00, '2026-06-12', '2026-12-12', 'SO-BULK-2026-012', 'Samar', 'Catbalogan City', 'Mercedes', 11.777800, 124.889100, 11.777800, 124.889100, 'Completed', '2026-05-22 03:50:14'),
(15, 4, 'Project PLAYSCAPE - Basey 13', 'SDG 4: Quality Education', 'DOH; BHW', NULL, 'Students and Teachers', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 50, 55000.00, '2026-01-13', '2026-12-13', 'SO-BULK-2026-013', 'Samar', 'Basey', 'Buscada', 11.282100, 125.068400, 11.282100, 125.068400, 'Inactive', '2026-05-22 03:50:14'),
(16, 5, 'Project KAHIMSOG - Calbayog City 14', 'SDG 3: Good Health and Well-being', 'DA; Farmers Association', NULL, 'Community Residents', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 60, 65000.00, '2026-02-14', '2026-12-14', 'SO-BULK-2026-014', 'Samar', 'Calbayog City', 'Aguit-itan', 12.067400, 124.598700, 12.067400, 124.598700, 'Expired', '2026-05-22 03:50:14'),
(17, 6, 'Project KABUHAYAN - Borongan City 15', 'SDG 8: Decent Work and Economic Growth', 'LGU; Barangay Council', NULL, 'Women Association', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 70, 75000.00, '2026-03-15', '2026-12-15', 'SO-BULK-2026-015', 'Eastern Samar', 'Borongan City', 'Maypangdan', 11.594800, 125.433500, 11.594800, 125.433500, 'Terminated', '2026-05-22 03:50:14'),
(18, 2, 'Project LUNTIAN - Guiuan 16', 'SDG 13: Climate Action', 'DepEd; Barangay Council', NULL, 'Youth Volunteers', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 80, 85000.00, '2026-04-16', '2026-12-16', 'SO-BULK-2026-016', 'Eastern Samar', 'Guiuan', 'Surok', 11.030800, 125.724000, 11.030800, 125.724000, 'On-going', '2026-05-22 03:50:14'),
(19, 3, 'Project TECH-READY - Catarman 17', 'SDG 9: Industry, Innovation and Infrastructure', 'TESDA; LGU', NULL, 'Barangay Officials', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 90, 95000.00, '2026-05-17', '2026-12-17', 'SO-BULK-2026-017', 'Northern Samar', 'Catarman', 'Dalakit', 12.504000, 124.640000, 12.504000, 124.640000, 'Completed', '2026-05-22 03:50:14'),
(20, 4, 'Project ALERT - Allen 18', 'SDG 11: Sustainable Cities and Communities', 'DOH; BHW', NULL, 'DRRM Volunteers', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 100, 15000.00, '2026-06-18', '2026-12-18', 'SO-BULK-2026-018', 'Northern Samar', 'Allen', 'Sabang', 12.505800, 124.284700, 12.505800, 124.284700, 'Inactive', '2026-05-22 03:50:14'),
(21, 5, 'Project PLAYSCAPE - Naval 19', 'SDG 4: Quality Education', 'DA; Farmers Association', NULL, 'Students and Teachers', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 110, 25000.00, '2026-01-19', '2026-12-19', 'SO-BULK-2026-019', 'Biliran', 'Naval', 'Calumpang', 11.557800, 124.397800, 11.557800, 124.397800, 'Expired', '2026-05-22 03:50:14'),
(22, 6, 'Project KAHIMSOG - Caibiran 20', 'SDG 3: Good Health and Well-being', 'LGU; Barangay Council', NULL, 'Community Residents', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 20, 35000.00, '2026-02-20', '2026-12-20', 'SO-BULK-2026-020', 'Biliran', 'Caibiran', 'Tomalistis', 11.570900, 124.582300, 11.570900, 124.582300, 'Terminated', '2026-05-22 03:50:14'),
(23, 2, 'Project KABUHAYAN - Tacloban City 21', 'SDG 8: Decent Work and Economic Growth', 'DepEd; Barangay Council', NULL, 'Women Association', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 30, 45000.00, '2026-03-21', '2026-12-01', 'SO-BULK-2026-021', 'Leyte', 'Tacloban City', 'Barangay 88 San Jose', 11.228900, 125.003700, 11.228900, 125.003700, 'On-going', '2026-05-22 03:50:14'),
(24, 3, 'Project LUNTIAN - Tacloban City 22', 'SDG 13: Climate Action', 'TESDA; LGU', NULL, 'Youth Volunteers', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 40, 55000.00, '2026-04-22', '2026-12-02', 'SO-BULK-2026-022', 'Leyte', 'Tacloban City', 'Barangay 95 Caibaan', 11.231200, 125.014200, 11.231200, 125.014200, 'Completed', '2026-05-22 03:50:14'),
(25, 4, 'Project TECH-READY - Palo 23', 'SDG 9: Industry, Innovation and Infrastructure', 'DOH; BHW', NULL, 'Barangay Officials', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 50, 65000.00, '2026-05-23', '2026-12-03', 'SO-BULK-2026-023', 'Leyte', 'Palo', 'Baras', 11.156900, 124.999300, 11.156900, 124.999300, 'Inactive', '2026-05-22 03:50:14'),
(26, 5, 'Project ALERT - Palo 24', 'SDG 11: Sustainable Cities and Communities', 'DA; Farmers Association', NULL, 'DRRM Volunteers', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 60, 75000.00, '2026-06-24', '2026-12-04', 'SO-BULK-2026-024', 'Leyte', 'Palo', 'Pawing', 11.180300, 124.984500, 11.180300, 124.984500, 'Expired', '2026-05-22 03:50:14'),
(27, 6, 'Project PLAYSCAPE - Ormoc City 25', 'SDG 4: Quality Education', 'LGU; Barangay Council', NULL, 'Students and Teachers', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 70, 85000.00, '2026-01-25', '2026-12-05', 'SO-BULK-2026-025', 'Leyte', 'Ormoc City', 'Cogon', 11.005400, 124.608400, 11.005400, 124.608400, 'Terminated', '2026-05-22 03:50:14'),
(28, 2, 'Project KAHIMSOG - Ormoc City 26', 'SDG 3: Good Health and Well-being', 'DepEd; Barangay Council', NULL, 'Community Residents', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 80, 95000.00, '2026-02-01', '2026-12-06', 'SO-BULK-2026-026', 'Leyte', 'Ormoc City', 'Can-adieng', 11.011500, 124.612000, 11.011500, 124.612000, 'On-going', '2026-05-22 03:50:14'),
(29, 3, 'Project KABUHAYAN - Baybay City 27', 'SDG 8: Decent Work and Economic Growth', 'TESDA; LGU', NULL, 'Women Association', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 90, 15000.00, '2026-03-02', '2026-12-07', 'SO-BULK-2026-027', 'Leyte', 'Baybay City', 'Pangasugan', 10.742300, 124.799900, 10.742300, 124.799900, 'Completed', '2026-05-22 03:50:14'),
(30, 4, 'Project LUNTIAN - Dulag 28', 'SDG 13: Climate Action', 'DOH; BHW', NULL, 'Youth Volunteers', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 100, 25000.00, '2026-04-03', '2026-12-08', 'SO-BULK-2026-028', 'Leyte', 'Dulag', 'Market Site', 10.954000, 125.031100, 10.954000, 125.031100, 'Inactive', '2026-05-22 03:50:14'),
(31, 5, 'Project TECH-READY - Tanauan 29', 'SDG 9: Industry, Innovation and Infrastructure', 'DA; Farmers Association', NULL, 'Barangay Officials', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 110, 35000.00, '2026-05-04', '2026-12-09', 'SO-BULK-2026-029', 'Leyte', 'Tanauan', 'Canramos', 11.105700, 125.014700, 11.105700, 125.014700, 'Expired', '2026-05-22 03:50:14'),
(32, 6, 'Project ALERT - Maasin City 30', 'SDG 11: Sustainable Cities and Communities', 'LGU; Barangay Council', NULL, 'DRRM Volunteers', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 20, 45000.00, '2026-06-05', '2026-12-10', 'SO-BULK-2026-030', 'Southern Leyte', 'Maasin City', 'Tagnipa', 10.145900, 124.859500, 10.145900, 124.859500, 'Terminated', '2026-05-22 03:50:14'),
(33, 2, 'Project PLAYSCAPE - Sogod 31', 'SDG 4: Quality Education', 'DepEd; Barangay Council', NULL, 'Students and Teachers', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 30, 55000.00, '2026-01-06', '2026-12-11', 'SO-BULK-2026-031', 'Southern Leyte', 'Sogod', 'Zone III', 10.384000, 124.982200, 10.384000, 124.982200, 'On-going', '2026-05-22 03:50:14'),
(34, 3, 'Project KAHIMSOG - Catbalogan City 32', 'SDG 3: Good Health and Well-being', 'TESDA; LGU', NULL, 'Community Residents', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 40, 65000.00, '2026-02-07', '2026-12-12', 'SO-BULK-2026-032', 'Samar', 'Catbalogan City', 'Mercedes', 11.777800, 124.889100, 11.777800, 124.889100, 'Completed', '2026-05-22 03:50:14'),
(35, 4, 'Project KABUHAYAN - Basey 33', 'SDG 8: Decent Work and Economic Growth', 'DOH; BHW', NULL, 'Women Association', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 50, 75000.00, '2026-03-08', '2026-12-13', 'SO-BULK-2026-033', 'Samar', 'Basey', 'Buscada', 11.282100, 125.068400, 11.282100, 125.068400, 'Inactive', '2026-05-22 03:50:14'),
(36, 5, 'Project LUNTIAN - Calbayog City 34', 'SDG 13: Climate Action', 'DA; Farmers Association', NULL, 'Youth Volunteers', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 60, 85000.00, '2026-04-09', '2026-12-14', 'SO-BULK-2026-034', 'Samar', 'Calbayog City', 'Aguit-itan', 12.067400, 124.598700, 12.067400, 124.598700, 'Expired', '2026-05-22 03:50:14'),
(37, 6, 'Project TECH-READY - Borongan City 35', 'SDG 9: Industry, Innovation and Infrastructure', 'LGU; Barangay Council', NULL, 'Barangay Officials', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 70, 95000.00, '2026-05-10', '2026-12-15', 'SO-BULK-2026-035', 'Eastern Samar', 'Borongan City', 'Maypangdan', 11.594800, 125.433500, 11.594800, 125.433500, 'Terminated', '2026-05-22 03:50:14'),
(38, 2, 'Project ALERT - Guiuan 36', 'SDG 11: Sustainable Cities and Communities', 'DepEd; Barangay Council', NULL, 'DRRM Volunteers', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 80, 15000.00, '2026-06-11', '2026-12-16', 'SO-BULK-2026-036', 'Eastern Samar', 'Guiuan', 'Surok', 11.030800, 125.724000, 11.030800, 125.724000, 'On-going', '2026-05-22 03:50:14'),
(39, 3, 'Project PLAYSCAPE - Catarman 37', 'SDG 4: Quality Education', 'TESDA; LGU', NULL, 'Students and Teachers', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 90, 25000.00, '2026-01-12', '2026-12-17', 'SO-BULK-2026-037', 'Northern Samar', 'Catarman', 'Dalakit', 12.504000, 124.640000, 12.504000, 124.640000, 'Completed', '2026-05-22 03:50:14'),
(40, 4, 'Project KAHIMSOG - Allen 38', 'SDG 3: Good Health and Well-being', 'DOH; BHW', NULL, 'Community Residents', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 100, 35000.00, '2026-02-13', '2026-12-18', 'SO-BULK-2026-038', 'Northern Samar', 'Allen', 'Sabang', 12.505800, 124.284700, 12.505800, 124.284700, 'Inactive', '2026-05-22 03:50:14'),
(41, 5, 'Project KABUHAYAN - Naval 39', 'SDG 8: Decent Work and Economic Growth', 'DA; Farmers Association', NULL, 'Women Association', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 110, 45000.00, '2026-03-14', '2026-12-19', 'SO-BULK-2026-039', 'Biliran', 'Naval', 'Calumpang', 11.557800, 124.397800, 11.557800, 124.397800, 'Expired', '2026-05-22 03:50:14'),
(42, 6, 'Project LUNTIAN - Caibiran 40', 'SDG 13: Climate Action', 'LGU; Barangay Council', NULL, 'Youth Volunteers', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 20, 55000.00, '2026-04-15', '2026-12-20', 'SO-BULK-2026-040', 'Biliran', 'Caibiran', 'Tomalistis', 11.570900, 124.582300, 11.570900, 124.582300, 'Terminated', '2026-05-22 03:50:14'),
(43, 2, 'Project TECH-READY - Tacloban City 41', 'SDG 9: Industry, Innovation and Infrastructure', 'DepEd; Barangay Council', NULL, 'Barangay Officials', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 30, 65000.00, '2026-05-16', '2026-12-01', 'SO-BULK-2026-041', 'Leyte', 'Tacloban City', 'Barangay 88 San Jose', 11.228900, 125.003700, 11.228900, 125.003700, 'On-going', '2026-05-22 03:50:14'),
(44, 3, 'Project ALERT - Tacloban City 42', 'SDG 11: Sustainable Cities and Communities', 'TESDA; LGU', NULL, 'DRRM Volunteers', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 40, 75000.00, '2026-06-17', '2026-12-02', 'SO-BULK-2026-042', 'Leyte', 'Tacloban City', 'Barangay 95 Caibaan', 11.231200, 125.014200, 11.231200, 125.014200, 'Completed', '2026-05-22 03:50:14'),
(45, 4, 'Project PLAYSCAPE - Palo 43', 'SDG 4: Quality Education', 'DOH; BHW', NULL, 'Students and Teachers', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 50, 85000.00, '2026-01-18', '2026-12-03', 'SO-BULK-2026-043', 'Leyte', 'Palo', 'Baras', 11.156900, 124.999300, 11.156900, 124.999300, 'Inactive', '2026-05-22 03:50:14'),
(46, 5, 'Project KAHIMSOG - Palo 44', 'SDG 3: Good Health and Well-being', 'DA; Farmers Association', NULL, 'Community Residents', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 60, 95000.00, '2026-02-19', '2026-12-04', 'SO-BULK-2026-044', 'Leyte', 'Palo', 'Pawing', 11.180300, 124.984500, 11.180300, 124.984500, 'Expired', '2026-05-22 03:50:14'),
(47, 6, 'Project KABUHAYAN - Ormoc City 45', 'SDG 8: Decent Work and Economic Growth', 'LGU; Barangay Council', NULL, 'Women Association', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 70, 15000.00, '2026-03-20', '2026-12-05', 'SO-BULK-2026-045', 'Leyte', 'Ormoc City', 'Cogon', 11.005400, 124.608400, 11.005400, 124.608400, 'Terminated', '2026-05-22 03:50:14'),
(48, 2, 'Project LUNTIAN - Ormoc City 46', 'SDG 13: Climate Action', 'DepEd; Barangay Council', NULL, 'Youth Volunteers', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 80, 25000.00, '2026-04-21', '2026-12-06', 'SO-BULK-2026-046', 'Leyte', 'Ormoc City', 'Can-adieng', 11.011500, 124.612000, 11.011500, 124.612000, 'On-going', '2026-05-22 03:50:14'),
(49, 3, 'Project TECH-READY - Baybay City 47', 'SDG 9: Industry, Innovation and Infrastructure', 'TESDA; LGU', NULL, 'Barangay Officials', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 90, 35000.00, '2026-05-22', '2026-12-07', 'SO-BULK-2026-047', 'Leyte', 'Baybay City', 'Pangasugan', 10.742300, 124.799900, 10.742300, 124.799900, 'Completed', '2026-05-22 03:50:14'),
(50, 4, 'Project ALERT - Dulag 48', 'SDG 11: Sustainable Cities and Communities', 'DOH; BHW', NULL, 'DRRM Volunteers', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 100, 45000.00, '2026-06-23', '2026-12-08', 'SO-BULK-2026-048', 'Leyte', 'Dulag', 'Market Site', 10.954000, 125.031100, 10.954000, 125.031100, 'Inactive', '2026-05-22 03:50:14'),
(51, 5, 'Project PLAYSCAPE - Tanauan 49', 'SDG 4: Quality Education', 'DA; Farmers Association', NULL, 'Students and Teachers', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 110, 55000.00, '2026-01-24', '2026-12-09', 'SO-BULK-2026-049', 'Leyte', 'Tanauan', 'Canramos', 11.105700, 125.014700, 11.105700, 125.014700, 'Expired', '2026-05-22 03:50:14'),
(52, 6, 'Project KAHIMSOG - Maasin City 50', 'SDG 3: Good Health and Well-being', 'LGU; Barangay Council', NULL, 'Community Residents', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 20, 65000.00, '2026-02-25', '2026-12-10', 'SO-BULK-2026-050', 'Southern Leyte', 'Maasin City', 'Tagnipa', 10.145900, 124.859500, 10.145900, 124.859500, 'Terminated', '2026-05-22 03:50:14'),
(53, 2, 'Project KABUHAYAN - Sogod 51', 'SDG 8: Decent Work and Economic Growth', 'DepEd; Barangay Council', NULL, 'Women Association', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 30, 75000.00, '2026-03-01', '2026-12-11', 'SO-BULK-2026-051', 'Southern Leyte', 'Sogod', 'Zone III', 10.384000, 124.982200, 10.384000, 124.982200, 'On-going', '2026-05-22 03:50:14'),
(54, 3, 'Project LUNTIAN - Catbalogan City 52', 'SDG 13: Climate Action', 'TESDA; LGU', NULL, 'Youth Volunteers', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 40, 85000.00, '2026-04-02', '2026-12-12', 'SO-BULK-2026-052', 'Samar', 'Catbalogan City', 'Mercedes', 11.777800, 124.889100, 11.777800, 124.889100, 'Completed', '2026-05-22 03:50:14'),
(55, 4, 'Project TECH-READY - Basey 53', 'SDG 9: Industry, Innovation and Infrastructure', 'DOH; BHW', NULL, 'Barangay Officials', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 50, 95000.00, '2026-05-03', '2026-12-13', 'SO-BULK-2026-053', 'Samar', 'Basey', 'Buscada', 11.282100, 125.068400, 11.282100, 125.068400, 'Inactive', '2026-05-22 03:50:14'),
(56, 5, 'Project ALERT - Calbayog City 54', 'SDG 11: Sustainable Cities and Communities', 'DA; Farmers Association', NULL, 'DRRM Volunteers', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 60, 15000.00, '2026-06-04', '2026-12-14', 'SO-BULK-2026-054', 'Samar', 'Calbayog City', 'Aguit-itan', 12.067400, 124.598700, 12.067400, 124.598700, 'Expired', '2026-05-22 03:50:14'),
(57, 6, 'Project PLAYSCAPE - Borongan City 55', 'SDG 4: Quality Education', 'LGU; Barangay Council', NULL, 'Students and Teachers', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 70, 25000.00, '2026-01-05', '2026-12-15', 'SO-BULK-2026-055', 'Eastern Samar', 'Borongan City', 'Maypangdan', 11.594800, 125.433500, 11.594800, 125.433500, 'Terminated', '2026-05-22 03:50:14'),
(58, 2, 'Project KAHIMSOG - Guiuan 56', 'SDG 3: Good Health and Well-being', 'DepEd; Barangay Council', NULL, 'Community Residents', NULL, 'Dr. Alma Cruz', 'Prof. Ramon Hidalgo', 'Education Extension Team', 80, 35000.00, '2026-02-06', '2026-12-16', 'SO-BULK-2026-056', 'Eastern Samar', 'Guiuan', 'Surok', 11.030800, 125.724000, 11.030800, 125.724000, 'On-going', '2026-05-22 03:50:14'),
(59, 3, 'Project KABUHAYAN - Catarman 57', 'SDG 8: Decent Work and Economic Growth', 'TESDA; LGU', NULL, 'Women Association', NULL, 'Dr. Aileen Gomez', 'Prof. Mark Torres', 'Health Extension Team', 90, 45000.00, '2026-03-07', '2026-12-17', 'SO-BULK-2026-057', 'Northern Samar', 'Catarman', 'Dalakit', 12.504000, 124.640000, 12.504000, 124.640000, 'Completed', '2026-05-22 03:50:14'),
(60, 4, 'Project LUNTIAN - Allen 58', 'SDG 13: Climate Action', 'DOH; BHW', NULL, 'Youth Volunteers', NULL, 'Dr. Maria Santos', 'Prof. Allen Reyes', 'Livelihood Extension Team', 100, 55000.00, '2026-04-08', '2026-12-18', 'SO-BULK-2026-058', 'Northern Samar', 'Allen', 'Sabang', 12.505800, 124.284700, 12.505800, 124.284700, 'Inactive', '2026-05-22 03:50:14'),
(61, 5, 'Project TECH-READY - Naval 59', 'SDG 9: Industry, Innovation and Infrastructure', 'DA; Farmers Association', NULL, 'Barangay Officials', NULL, 'Dr. Roberto Lim', 'Prof. Grace dela Peña', 'Environment Extension Team', 110, 65000.00, '2026-05-09', '2026-12-19', 'SO-BULK-2026-059', 'Biliran', 'Naval', 'Calumpang', 11.557800, 124.397800, 11.557800, 124.397800, 'Expired', '2026-05-22 03:50:14'),
(62, 6, 'Project ALERT - Caibiran 60', 'SDG 11: Sustainable Cities and Communities', 'LGU; Barangay Council', NULL, 'DRRM Volunteers', NULL, 'Dr. Joleco Agullo', 'Prof. Karen Montejo', 'ICT Extension Team', 20, 75000.00, '2026-06-10', '2026-12-20', 'SO-BULK-2026-060', 'Biliran', 'Caibiran', 'Tomalistis', 11.570900, 124.582300, 11.570900, 124.582300, 'Terminated', '2026-05-22 03:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `quarterly_reports`
--

CREATE TABLE `quarterly_reports` (
  `id` int(11) NOT NULL,
  `college` varchar(150) DEFAULT NULL,
  `campus` varchar(150) DEFAULT NULL,
  `department` varchar(150) DEFAULT NULL,
  `period_covered` varchar(150) DEFAULT NULL,
  `control_no` varchar(100) DEFAULT NULL,
  `revision_no` varchar(100) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `prepared_by` varchar(180) DEFAULT NULL,
  `prepared_title` varchar(180) DEFAULT NULL,
  `noted_by_dean` varchar(180) DEFAULT NULL,
  `noted_by_dean_title` varchar(180) DEFAULT NULL,
  `noted_by_extension_director` varchar(180) DEFAULT NULL,
  `noted_by_extension_director_title` varchar(180) DEFAULT NULL,
  `approved_by` varchar(180) DEFAULT NULL,
  `approved_title` varchar(180) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `submission_status` enum('Draft','Submitted','Under Review','Recalled','For Revision','Not Approved','Department Coordinator Approved','School Coordinator Approved','Campus Director Approved','Extension Office Approved','VP ORIES Approved','Approved','Archived') DEFAULT 'Draft',
  `submitted_by` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `recalled_by` int(11) DEFAULT NULL,
  `recalled_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `revision_notes` text DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quarterly_report_items`
--

CREATE TABLE `quarterly_report_items` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `title_of_extension_project` text DEFAULT NULL,
  `proponents` text DEFAULT NULL,
  `date_conducted` varchar(150) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `source_of_fund` text DEFAULT NULL,
  `total_project_cost` decimal(12,2) DEFAULT 0.00,
  `project_phase` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(180) NOT NULL,
  `username` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(120) NOT NULL,
  `college` varchar(150) DEFAULT NULL,
  `department` varchar(150) DEFAULT NULL,
  `campus` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `signatory_title` varchar(150) DEFAULT NULL,
  `has_extension_services` tinyint(1) DEFAULT 0,
  `routing_group` varchar(180) DEFAULT NULL,
  `signature_image` varchar(255) DEFAULT NULL,
  `account_status` varchar(50) DEFAULT 'Active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `role`, `college`, `department`, `campus`, `email`, `signatory_title`, `has_extension_services`, `routing_group`, `signature_image`, `account_status`, `created_by`, `created_at`) VALUES
(1, 'System Super Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'Super Admin', 'Extension Office', 'Administration', 'Main Campus', NULL, 'Extension Office', 0, 'MAIN', NULL, 'Active', NULL, '2026-05-19 08:03:58'),
(2, 'Department Extension Coordinator', 'staff', 'de9bf5643eabf80f4a56fda3bbb84483', 'Department Coordinator', 'School of Engineering', 'Information Technology', 'Main Campus', NULL, 'Extension Coordinator', 0, 'SOE-MAIN', NULL, 'Active', NULL, '2026-05-19 08:03:58'),
(3, 'School Extension Coordinator', 'schoolcoord', '9a60ffa59cbe650509dd3fd5afa5dfd4', 'School Coordinator', 'School of Engineering', 'Information Technology', 'Main Campus', NULL, 'School Coordinator', 0, 'SOE-MAIN', NULL, 'Active', NULL, '2026-05-19 08:03:58'),
(4, 'Campus Director / Dean', 'campusdirector', '6f02716d5ae7cddf7e2c590a241107b2', 'Campus Director', 'School of Engineering', 'Administration', 'Main Campus', NULL, 'Campus Director/Dean', 0, 'SOE-MAIN', NULL, 'Active', NULL, '2026-05-19 08:03:58'),
(5, 'VP ORIES', 'vpories', '8fc83302c44fcb68b793ceca1d376996', 'VP ORIES', 'Administration', 'Administration', 'Main Campus', NULL, 'VP ORIES', 0, 'MAIN', NULL, 'Active', NULL, '2026-05-19 08:03:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approval_history`
--
ALTER TABLE `approval_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barangay_locations`
--
ALTER TABLE `barangay_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_approvals`
--
ALTER TABLE `document_approvals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitoring_entries`
--
ALTER TABLE `monitoring_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quarterly_reports`
--
ALTER TABLE `quarterly_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quarterly_report_items`
--
ALTER TABLE `quarterly_report_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `approval_history`
--
ALTER TABLE `approval_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangay_locations`
--
ALTER TABLE `barangay_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `document_approvals`
--
ALTER TABLE `document_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `monitoring_entries`
--
ALTER TABLE `monitoring_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `quarterly_reports`
--
ALTER TABLE `quarterly_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quarterly_report_items`
--
ALTER TABLE `quarterly_report_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
