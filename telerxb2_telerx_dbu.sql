-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2026 at 07:57 PM
-- Server version: 11.4.11-MariaDB
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `telerxb2_telerx_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `appointment_date` date NOT NULL,
  `slot_time` varchar(5) NOT NULL,
  `appointment_time` varchar(5) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'confirmed',
  `appointment_number` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `patient_phone` varchar(20) DEFAULT NULL,
  `age` varchar(10) DEFAULT NULL,
  `weight` varchar(20) DEFAULT NULL,
  `body_temperature` varchar(20) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `pulse` varchar(20) DEFAULT NULL,
  `spo2` varchar(20) DEFAULT NULL,
  `rbs_fbs` varchar(20) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `referrer_tid` varchar(20) DEFAULT NULL COMMENT 'Health worker TID (e.g. T1001)',
  `created_by_special_tid_id` int(11) DEFAULT NULL COMMENT 'Special TID account that created this booking',
  `chief_complaints` text DEFAULT NULL,
  `on_examination` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `advice` text DEFAULT NULL,
  `call_status` enum('not_started','in_progress','ended') DEFAULT 'not_started',
  `agora_channel` varchar(255) DEFAULT NULL,
  `doctor_token` text DEFAULT NULL,
  `patient_token` text DEFAULT NULL,
  `prescription_path` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'bkash' COMMENT 'bkash or welfare',
  `prescription_footer` text DEFAULT NULL,
  `note_reference` text DEFAULT NULL,
  `call_started_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `slot_time`, `appointment_time`, `status`, `appointment_number`, `notes`, `patient_name`, `mobile`, `patient_phone`, `age`, `weight`, `body_temperature`, `blood_pressure`, `pulse`, `spo2`, `rbs_fbs`, `attachment_path`, `created_at`, `updated_at`, `referrer_tid`, `created_by_special_tid_id`, `chief_complaints`, `on_examination`, `diagnosis`, `medications`, `advice`, `call_status`, `agora_channel`, `doctor_token`, `patient_token`, `prescription_path`, `payment_method`, `prescription_footer`, `note_reference`, `call_started_at`) VALUES
(24, 8, 10, '2026-04-28', '06:00', '06:00', 'completed', 'APT00024', 'Fever\r\nCough\r\nCold\r\nFor 3 days', 'Namirah Chowdhury Aanaya', '01677832046', '01677832046', '8', '25', '101', '', '105', '98%', '', '', '2026-04-27 14:36:32', '2026-04-27 15:10:58', '', NULL, '1. Fever for 3 days\r\n2. Cough and cold for 3 days\r\n3. Occasional nasal blockage ', '', 'Viral Fever (?)', '[{\"name\":\"1. Syp. Napa \",\"dose\":\"3 and 1\\/2 TSF * 3 Times daily\",\"duration\":\"If temp. more than 100* F\"},{\"name\":\"2. Syp. Fexo\",\"dose\":\"1 TSF* 2 Times daily \",\"duration\":\"10 days\"},{\"name\":\"3. Syp. Levostar\",\"dose\":\"1 TSF * 2 Times daily\",\"duration\":\"10 days\"},{\"name\":\"4. Tab. Monas 5mg\",\"dose\":\"0+0+1\",\"duration\":\"10 days\"},{\"name\":\"5. Afrin Nasal drop(0.025%)\",\"dose\":\"1 drop on each nostril * 2 times daily\",\"duration\":\"If nasal blockage occur \"}]', '', 'not_started', NULL, NULL, NULL, 'assets/prescriptions/prescription_24_1777302658.pdf', 'bkash', '', NULL, NULL),
(31, 16, 10, '2026-05-16', '00:15', '00:15', 'confirmed', 'APT00031', '1.cold', 'Marfiyaa', '01909414115', '01909414115', '29', '55', '98', '110/70', '72', '', '', '', '2026-05-12 07:09:04', '2026-05-12 07:09:04', '', 8, NULL, NULL, NULL, NULL, NULL, 'not_started', NULL, NULL, NULL, NULL, 'bkash', NULL, NULL, NULL),
(51, 22, 10, '2026-05-17', '11:15', '11:15', 'confirmed', 'APT00051', '১,ঘাড় দিয়ে ব্যাথা উঠে', 'Amena begum', '01763892178', '01763892178', '50 YEARS ', '52', '', '120/70', '98', '', '', '', '2026-05-17 05:08:03', '2026-05-17 06:31:44', '', 8, '1. Neck pain \r\n2. Bodyache \r\n3. Generalised weakness ', '', '', '[{\"name\":\"1. Tab. NAPA EXTEND \",\"dose\":\"1+1+1\",\"duration\":\"IF FEVER \\/ PAIN\"},{\"name\":\"2. Tab. RIVOTRIL (0.5MG )\",\"dose\":\"0+0+1 \",\"duration\":\"7 DAYS \"},{\"name\":\"3. TAB. CALBO D\",\"dose\":\"0+1+0\",\"duration\":\"1 MONTH \"},{\"name\":\"4. Tab. MAXPRO 20MG \",\"dose\":\"1+0+1 \",\"duration\":\"7 DAYS \"}]', '', 'not_started', NULL, NULL, NULL, 'assets/prescriptions/prescription_51_1778999504.pdf', 'bkash', '', '', NULL),
(54, 24, 10, '2026-05-17', '12:00', '12:00', 'confirmed', 'APT00054', '1,পাজর দিয়ে টেনে ধরে', 'Rosida begum', '00000001109', '00000001109', '50', '', '', '100/70', '72', '', '', '', '2026-05-17 05:37:14', '2026-05-17 06:50:02', '', 8, '1. MUSCLE SPASM \r\n2. TINGLING SENSETION.', '', '', '[{\"name\":\"1. TAB. RESERVIX 100MG\",\"dose\":\"1+0+1\",\"duration\":\" 5 DAYS \"},{\"name\":\"2. TAB. BEKLO 10MG\",\"dose\":\"1+0+1\",\"duration\":\"5 DAYS\"},{\"name\":\"3. TAB. MAXPRO 20MG\",\"dose\":\"1+0+1 (BEFORE MEAL)\",\"duration\":\"5 DAYS \"},{\"name\":\"4. TAB. NEURO B\",\"dose\":\"0+1+0\",\"duration\":\"1 MONTH \"}]', '', 'not_started', NULL, NULL, NULL, 'assets/prescriptions/prescription_54_1779000602.pdf', 'bkash', '', '', NULL),
(55, 25, 10, '2026-05-17', '12:45', '12:45', 'confirmed', 'APT00055', '১,হাটুতে ব্যাথা', 'Jamiron', '00000001159', '00000001159', '70 YEARS ', '', '98', '150/70', '', '', '', '', '2026-05-17 05:51:49', '2026-05-17 06:50:25', '', 8, '1. KNEE PAIN \r\n2. SCABIES.', '', '', '[{\"name\":\"1. TAB. NAPROX PLUS 500MG\",\"dose\":\"1+0+1\",\"duration\":\"7 DAYS \"},{\"name\":\"2. TAB. CALBO D\",\"dose\":\"0+1+0\",\"duration\":\"1 MONTH\"},{\"name\":\"3. TAB. ALATROL 10MG \",\"dose\":\"0+0+1\",\"duration\":\"7 DAYS \"},{\"name\":\"4. LORIX  CREAM\",\"dose\":\"USE 2 TUBE 1 WEEK APART IN WHOLE BODY EXCEPT HEAD\",\"duration\":\"\"}]', '', 'not_started', NULL, NULL, NULL, 'assets/prescriptions/prescription_55_1779000625.pdf', 'bkash', '', '', NULL),
(57, 4, 9, '2026-05-25', '20:00', '20:00', 'confirmed', 'APT00057', '', 'yy', '01732858821', '01732858821', '99', '', '', '', '', '', '', '', '2026-05-24 19:18:49', '2026-05-24 19:56:11', '', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 'bkash', NULL, NULL, '2026-05-24 19:56:11'),
(58, 4, 9, '2026-05-25', '18:45', '18:45', 'confirmed', 'APT00058', '', 'Kamran kamran', '01518942416', '01518942416', '', '', '', '', '', '', '', '', '2026-05-24 19:26:58', '2026-05-24 19:50:31', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'bkash', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `call_sessions`
--

CREATE TABLE `call_sessions` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `caller_id` int(11) NOT NULL,
  `caller_type` enum('doctor','patient','healthcare') NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_type` enum('doctor','patient','healthcare') NOT NULL,
  `channel_name` varchar(255) NOT NULL,
  `token` text NOT NULL,
  `call_type` enum('audio','video') DEFAULT 'video',
  `status` enum('initiated','ongoing','completed','missed','rejected') DEFAULT 'initiated',
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_account` varchar(50) NOT NULL COMMENT 'Format: userType_id, e.g., doctor_1 or patient_5',
  `receiver_account` varchar(50) NOT NULL COMMENT 'Format: userType_id, e.g., doctor_1 or patient_5',
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_account`, `receiver_account`, `message`, `created_at`) VALUES
(1, 'patient_4', 'doctor_9', 'hello', '2026-04-09 17:53:26'),
(2, 'patient_4', 'doctor_10', 'sir i am ready\'', '2026-04-11 04:36:10'),
(3, 'patient_4', 'doctor_10', 'ready', '2026-04-15 16:48:51');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `bmdc_no` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL COMMENT 'Mobile banking account (bKash/Rocket)',
  `degrees` varchar(255) DEFAULT NULL COMMENT 'Educational qualifications',
  `currently_working` varchar(255) DEFAULT NULL COMMENT 'Current workplace/hospital',
  `department` varchar(255) DEFAULT NULL COMMENT 'Medical specialty/department',
  `present_address` text DEFAULT NULL,
  `bmdc_certificate` varchar(500) DEFAULT NULL COMMENT 'File path to BMDC certificate',
  `nid_card` varchar(500) DEFAULT NULL COMMENT 'File path to NID card',
  `degrees_certificate` varchar(500) DEFAULT NULL COMMENT 'File path to degrees certificate',
  `is_verified` tinyint(1) DEFAULT 0 COMMENT 'Doctor verification status',
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `email`, `phone`, `bmdc_no`, `password`, `gender`, `account_number`, `degrees`, `currently_working`, `department`, `present_address`, `bmdc_certificate`, `nid_card`, `degrees_certificate`, `is_verified`, `is_active`, `email_verified`, `phone_verified`, `last_login`, `created_at`, `updated_at`) VALUES
(4, 'Dr. Mohammad Rahman', 'dr.rahman@telerx.com', '+8801712345678', 'A-12345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Male', '01712345678', 'MBBS, MD (Cardiology)', 'Square Hospital', 'Cardiology', '123 Medical Center, Dhanmondi, Dhaka', NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-10 19:12:16', '2026-01-10 19:12:16'),
(5, 'Dr. Fatima Begum', 'dr.begum@telerx.com', '+8801812345678', 'A-12346', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Female', '01812345678', 'MBBS, MD (Pediatrics)', 'Apollo Hospitals', 'Pediatrics', '456 Children Hospital, Gulshan, Dhaka', NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-10 19:12:16', '2026-01-10 19:12:16'),
(6, 'Dr. Ahmed Hossain', 'dr.hossain@telerx.com', '+8801912345678', 'A-12347', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Male', '01912345678', 'MBBS, MS (Orthopedics)', 'United Hospital', 'Orthopedic Surgery', '789 Orthopedic Center, Uttara, Dhaka', NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-10 19:12:16', '2026-01-10 19:12:16'),
(8, 'Md Kamranul Islam', 'kamranislam8@gmail.com', '01771971072', '12345', '$2y$10$.fmupjPTTN7KTvR4h2kt1ORiCF4QOyRUMSQJdoQFizcbzxFFUA.Qq', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-10 19:17:32', '2026-01-10 19:17:32'),
(9, 'Md. Mehedi Hassan', 'mehedimehas@gmail.com', '01616858822', 'CFGG44', '$2y$10$Dg1Jv0a8gbhPyofnc0DmI.j/7xkIED0Ve/WdwJhU.CXI7IExz7xwy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-11 13:33:38', '2026-01-24 05:46:28'),
(10, 'GOLAM MOHAMMAD', 'golammohammaad@gmail.com', '01783652932', 'A91268', '$2y$10$p1mnXP3mEX1TDtd45xkhTeKSVnQm3hhSTgVWHc4Komg/msdUuF6mC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-17 08:17:37', '2026-01-23 14:00:21'),
(11, 'Dr. AKTAR UZZAMAN', 'aktaruzzaman725@gmail.com', '1995259264', 'A88242', '$2y$10$nD7G/WjUx6ioEtNAKSTN/eONK7KT0sCsWpFhT4smFi9oUTOkXIdbe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-21 06:51:01', '2026-01-21 06:51:01'),
(12, 'Sadia Tasnim Urbi', 'sadiaurbi99@gmail.com', '01535452687', '127138', '$2y$10$hurb0s2SEWBSDHqzOMH3Oe.i9T48S3FDkWMCUsojq.wyE0CMTgQ7a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-21 13:44:23', '2026-01-21 13:44:23'),
(13, 'Dr Shirin Sultana', 'drshirinsalekin@gmail.com', '01913846928', '130872', '$2y$10$QNUludm8Na.2SLKzpRVZBurnRAo3mb8Qr.IpYIBliLhZVMFO2B5bO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-21 14:30:23', '2026-01-21 14:30:23'),
(14, 'Sadia Iffat Anam', 'iffatanam30@gmail.com', '1768620600', '73240', '$2y$10$8qlMf7YGYSbHHgxjXDceyOi1tFyDIigm9lNKphU8CnJP2sH9RbFjC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-24 09:08:29', '2026-01-24 09:08:29'),
(15, 'Uzzal Chandra Tangchangya', 'dr.uzzaltang@gmail.com', '1608365534', 'A- 100133', '$2y$10$iBLrSSwfJkeqIv8yHdONAO9FJ.rBoD0DMvoOF9nkaQ8m0EjxGfgEO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-01-27 03:44:22', '2026-01-27 03:44:22'),
(16, 'Sumaya Akter', 'sumayatushi4269@gmail.com', '01632783903', 'A102786', '$2y$10$azmDiyLfsILp4Lnj/HROnOr5dXanNkM2EMcGbdBWIlwFicv0u9.72', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-03-02 11:05:19', '2026-03-02 11:13:15'),
(17, 'Md Kamranul', 'kamranislam09@gmail.com', '01771971009', '1234590', '$2y$10$eBdgXV41STKjEij.qUQNhet2x/JQoEMK81pXrX4NoxFreIqtafwjO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-03-09 09:12:40', '2026-03-09 09:12:40'),
(18, 'Shahjalal Nijami', 'shahjalalnijami61@gmail.com', '01758668884', 'A-129288', '$2y$10$w6/LYo6PeyD/XB8cMqs2x.XyNhwCEJgSioUSJd9YwVPvGuJkSA4TC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-04-26 16:12:27', '2026-04-26 16:12:27'),
(19, 'Dr. B.M. Shohag Rana', 'drshohagjsr@gmail.com', '01744969257', 'A 114605', '$2y$10$9ZxYeqm1BVpFzGU.0KGuzutXkfZl640SsF2TMOADdJmgtqGr8Mknq', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-04-26 16:15:17', '2026-04-26 16:15:17'),
(20, 'Dr. Afia Ibnat Bushra', 'aibprotiva@gmail.com', '01922529922', 'A-127629', '$2y$10$gWhowJT1cyz3p4t63FUfBO/MXAy71M..pxrSqhn/HI1sLBAFYdVUK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-04-27 06:06:33', '2026-04-27 06:06:33'),
(21, 'Abdullah Al Mamun', 'mamun3573@gmail.com', '01725047111', 'A88577', '$2y$10$W6pycLsVZ8K1lAjROZt9DuyEm2SyrM29obwxmH2gV6ozh3bCzwtya', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-04-27 07:48:37', '2026-04-27 08:01:15'),
(22, 'Dr Murshida Akter', 'murshida.akter.680@gmail.com', '01741000680', '129409', '$2y$10$OSNg43a4Xd6gvi8pQPM1Juz5l2CSxuwqiJP.yINQjNfG9qCKtlIiq', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-04-27 09:45:08', '2026-04-27 09:45:08'),
(23, 'Dr. A B Siddique Shohel', 'absiddique.shohel73@gmail.com', '01917785973', 'A-86240', '$2y$10$OYgATT1G7XzhLr6kFoda3uPierb2o9HkQYTlEyx7yeHW3XHNmQeD6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-04-27 14:27:52', '2026-04-27 14:27:52'),
(24, 'Dr. Meraj Rashid Lipa', 'lipa.meraj@gmail.com', '01763357097', 'A-107597', '$2y$10$EqGhLHqdePOk7DAZSuPKW.n7kCSOGhWvig.Yt2lSlZzHrGXNCVlJe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-04-27 16:36:59', '2026-04-27 16:36:59'),
(25, 'Dr.Tanzila Rahman', 'tanzila.antora3514@gmail.com', '01843504732', 'A-126723', '$2y$10$BFCx3UNnJ83zU9VSXviQWuJKqM6CWT47ARN1Q6VR6pcu29oGgvr.6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-05-03 10:20:06', '2026-05-03 10:20:06'),
(26, 'Dr.Salina Banu', 'doctor@bracu.ac.bd', '01740652545', '33189', '$2y$10$9vilwJ4RaekWqxoQbHYxbOOsIZVGbunZha7tIBUS.sriKQBF5Mbd6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, NULL, '2026-05-07 06:37:31', '2026-05-07 06:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability_ranges`
--

CREATE TABLE `doctor_availability_ranges` (
  `id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `day_of_week` tinyint(3) UNSIGNED NOT NULL COMMENT '0=Sun, 1=Mon, ..., 6=Sat',
  `start_time` varchar(5) NOT NULL COMMENT 'HH:MM 24h, e.g. 09:00',
  `end_time` varchar(5) NOT NULL COMMENT 'HH:MM 24h, e.g. 12:00',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_availability_ranges`
--

INSERT INTO `doctor_availability_ranges` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `created_at`) VALUES
(1, 9, 4, '06:15', '06:30', '2026-03-05 05:20:30'),
(2, 9, 4, '07:45', '08:00', '2026-03-05 05:20:30'),
(3, 9, 4, '09:15', '09:30', '2026-03-05 05:20:30'),
(8, 9, 6, '12:00', '18:00', '2026-03-05 05:21:00'),
(10, 9, 0, '12:15', '12:30', '2026-03-05 05:21:19'),
(11, 9, 0, '13:30', '13:45', '2026-03-05 05:21:19'),
(12, 9, 0, '14:45', '15:00', '2026-03-05 05:21:19'),
(13, 9, 0, '16:00', '16:30', '2026-03-05 05:21:19'),
(14, 9, 0, '17:15', '17:30', '2026-03-05 05:21:19'),
(20, 9, 1, '18:45', '19:00', '2026-03-05 05:21:38'),
(21, 9, 1, '20:00', '20:15', '2026-03-05 05:21:38'),
(22, 9, 1, '21:15', '21:30', '2026-03-05 05:21:38'),
(23, 9, 1, '22:30', '22:45', '2026-03-05 05:21:38'),
(24, 9, 1, '23:45', '00:00', '2026-03-05 05:21:38'),
(25, 9, 2, '06:00', '12:00', '2026-03-05 05:21:53'),
(26, 9, 3, '06:00', '08:30', '2026-03-05 05:22:05'),
(32, 9, 5, '12:30', '12:45', '2026-04-24 17:18:39'),
(33, 9, 5, '13:45', '14:00', '2026-04-24 17:18:39'),
(34, 10, 6, '00:00', '06:00', '2026-04-25 10:01:10'),
(35, 10, 6, '12:00', '00:00', '2026-04-25 10:01:10'),
(38, 10, 2, '06:00', '12:00', '2026-04-25 10:01:39'),
(39, 10, 3, '06:00', '12:00', '2026-04-25 10:01:47'),
(40, 10, 4, '06:00', '12:00', '2026-04-25 10:01:56'),
(41, 10, 5, '06:00', '12:00', '2026-04-25 10:02:05'),
(42, 21, 5, '12:00', '18:00', '2026-04-27 08:00:19'),
(43, 23, 5, '06:00', '09:45', '2026-04-27 14:38:32'),
(44, 23, 5, '18:00', '00:00', '2026-04-27 14:38:32'),
(45, 23, 6, '06:00', '12:00', '2026-04-27 14:40:15'),
(46, 23, 6, '18:00', '00:00', '2026-04-27 14:40:15'),
(47, 25, 4, '12:00', '18:00', '2026-05-03 10:23:31'),
(56, 10, 1, '00:00', '06:00', '2026-05-17 04:59:18'),
(65, 10, 0, '06:00', '18:00', '2026-05-17 05:03:38');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_awards`
--

CREATE TABLE `doctor_awards` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `award_name` varchar(255) NOT NULL,
  `award_year` year(4) DEFAULT NULL,
  `awarded_by` varchar(255) DEFAULT NULL COMMENT 'Organization that awarded',
  `description` text DEFAULT NULL,
  `award_certificate` varchar(500) DEFAULT NULL COMMENT 'File path to award certificate',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_business_hours`
--

CREATE TABLE `doctor_business_hours` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `clinic_id` int(11) DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `max_appointments` int(11) DEFAULT NULL COMMENT 'Maximum appointments per day',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctor_business_hours`
--

INSERT INTO `doctor_business_hours` (`id`, `doctor_id`, `clinic_id`, `day_of_week`, `start_time`, `end_time`, `break_start`, `break_end`, `is_available`, `max_appointments`, `created_at`, `updated_at`) VALUES
(8, 8, NULL, 'Monday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-13 13:36:04', '2026-01-13 13:36:04'),
(9, 8, NULL, 'Tuesday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-13 13:36:04', '2026-01-13 13:36:04'),
(10, 8, NULL, 'Wednesday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-13 13:36:04', '2026-01-13 13:36:04'),
(11, 8, NULL, 'Thursday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-13 13:36:04', '2026-01-13 13:36:04'),
(12, 8, NULL, 'Friday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-13 13:36:04', '2026-01-13 13:36:04'),
(13, 8, NULL, 'Saturday', '10:00:00', '16:00:00', NULL, NULL, 1, NULL, '2026-01-13 13:36:04', '2026-01-13 13:36:04'),
(14, 8, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:36:04', '2026-01-13 13:36:04'),
(15, 9, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:42:03', '2026-03-05 05:20:37'),
(16, 9, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:42:03', '2026-03-05 05:20:37'),
(17, 9, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:42:03', '2026-03-05 05:20:37'),
(18, 9, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:42:04', '2026-03-05 05:20:37'),
(19, 9, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:42:04', '2026-03-05 05:20:37'),
(20, 9, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:42:04', '2026-03-05 05:20:37'),
(21, 9, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 13:42:04', '2026-01-13 13:42:04'),
(22, 10, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-17 08:21:27', '2026-03-07 09:09:23'),
(23, 10, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-17 08:21:27', '2026-03-07 09:09:23'),
(24, 10, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-17 08:21:27', '2026-03-07 09:09:23'),
(25, 10, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-17 08:21:27', '2026-03-07 09:09:23'),
(26, 10, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-17 08:21:27', '2026-03-07 09:09:23'),
(27, 10, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-17 08:21:27', '2026-03-07 09:09:23'),
(28, 10, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-17 08:21:27', '2026-01-17 08:21:27'),
(29, 13, NULL, 'Monday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-21 14:41:37', '2026-01-21 14:41:37'),
(30, 13, NULL, 'Tuesday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-21 14:41:37', '2026-01-21 14:41:37'),
(31, 13, NULL, 'Wednesday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-21 14:41:37', '2026-01-21 14:41:37'),
(32, 13, NULL, 'Thursday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-21 14:41:37', '2026-01-21 14:41:37'),
(33, 13, NULL, 'Friday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-21 14:41:37', '2026-01-21 14:41:37'),
(34, 13, NULL, 'Saturday', '10:00:00', '16:00:00', NULL, NULL, 1, NULL, '2026-01-21 14:41:37', '2026-01-21 14:41:37'),
(35, 13, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-21 14:41:37', '2026-01-21 14:41:37'),
(36, 15, NULL, 'Monday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-27 04:03:38', '2026-01-27 04:03:38'),
(37, 15, NULL, 'Tuesday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-27 04:03:38', '2026-01-27 04:03:38'),
(38, 15, NULL, 'Wednesday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-27 04:03:38', '2026-01-27 04:03:38'),
(39, 15, NULL, 'Thursday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-27 04:03:38', '2026-01-27 04:03:38'),
(40, 15, NULL, 'Friday', '09:00:00', '17:00:00', NULL, NULL, 1, NULL, '2026-01-27 04:03:38', '2026-01-27 04:03:38'),
(41, 15, NULL, 'Saturday', '10:00:00', '16:00:00', NULL, NULL, 1, NULL, '2026-01-27 04:03:38', '2026-01-27 04:03:38'),
(42, 15, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-01-27 04:03:38', '2026-01-27 04:03:38'),
(43, 16, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-03-02 11:10:41', '2026-03-02 11:10:41'),
(44, 16, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-03-02 11:10:41', '2026-03-02 11:10:41'),
(45, 16, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-03-02 11:10:41', '2026-03-02 11:10:41'),
(46, 16, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-03-02 11:10:41', '2026-03-02 11:10:41'),
(47, 16, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-03-02 11:10:41', '2026-03-02 11:10:41'),
(48, 16, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-03-02 11:10:41', '2026-03-02 11:10:41'),
(49, 16, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-03-02 11:10:41', '2026-03-02 11:10:41'),
(50, 19, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:27:48', '2026-04-26 16:27:48'),
(51, 19, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:27:48', '2026-04-26 16:27:48'),
(52, 19, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:27:48', '2026-04-26 16:27:48'),
(53, 19, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:27:48', '2026-04-26 16:27:48'),
(54, 19, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:27:48', '2026-04-26 16:27:48'),
(55, 19, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:27:48', '2026-04-26 16:27:48'),
(56, 19, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:27:48', '2026-04-26 16:27:48'),
(57, 12, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:32:45', '2026-04-26 16:32:45'),
(58, 12, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:32:45', '2026-04-26 16:32:45'),
(59, 12, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:32:45', '2026-04-26 16:32:45'),
(60, 12, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:32:45', '2026-04-26 16:32:45'),
(61, 12, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:32:45', '2026-04-26 16:32:45'),
(62, 12, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:32:45', '2026-04-26 16:32:45'),
(63, 12, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-26 16:32:45', '2026-04-26 16:32:45'),
(64, 21, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 07:56:10', '2026-04-27 07:56:10'),
(65, 21, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 07:56:10', '2026-04-27 07:56:10'),
(66, 21, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 07:56:10', '2026-04-27 07:56:10'),
(67, 21, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 07:56:10', '2026-04-27 07:56:10'),
(68, 21, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 07:56:10', '2026-04-27 07:56:10'),
(69, 21, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 07:56:10', '2026-04-27 07:56:10'),
(70, 21, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 07:56:10', '2026-04-27 07:56:10'),
(71, 23, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 14:42:29', '2026-04-27 14:42:29'),
(72, 23, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 14:42:29', '2026-04-27 14:42:29'),
(73, 23, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 14:42:29', '2026-04-27 14:42:29'),
(74, 23, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 14:42:29', '2026-04-27 14:42:29'),
(75, 23, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 14:42:29', '2026-04-27 14:42:29'),
(76, 23, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 14:42:29', '2026-04-27 14:42:29'),
(77, 23, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 14:42:29', '2026-04-27 14:42:29'),
(78, 24, NULL, 'Monday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 16:39:18', '2026-04-27 16:39:18'),
(79, 24, NULL, 'Tuesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 16:39:18', '2026-04-27 16:39:18'),
(80, 24, NULL, 'Wednesday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 16:39:18', '2026-04-27 16:39:18'),
(81, 24, NULL, 'Thursday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 16:39:18', '2026-04-27 16:39:18'),
(82, 24, NULL, 'Friday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 16:39:18', '2026-04-27 16:39:18'),
(83, 24, NULL, 'Saturday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 16:39:18', '2026-04-27 16:39:18'),
(84, 24, NULL, 'Sunday', NULL, NULL, NULL, NULL, 0, NULL, '2026-04-27 16:39:18', '2026-04-27 16:39:18');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_clinics`
--

CREATE TABLE `doctor_clinics` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `clinic_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `consultation_fee` decimal(8,2) DEFAULT NULL,
  `clinic_logo` varchar(500) DEFAULT NULL COMMENT 'File path to clinic logo',
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'Primary clinic affiliation',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_education`
--

CREATE TABLE `doctor_education` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `year_of_completion` year(4) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `cgpa` decimal(4,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `certificate_path` varchar(500) DEFAULT NULL COMMENT 'File path to degree certificate',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_experiences`
--

CREATE TABLE `doctor_experiences` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT 'Job title/position',
  `hospital_name` varchar(255) NOT NULL,
  `years_of_experience` varchar(50) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `employment_type` enum('Full Time','Part Time','Contract','Internship') DEFAULT 'Full Time',
  `job_description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `currently_working` tinyint(1) DEFAULT 0,
  `hospital_logo` varchar(500) DEFAULT NULL COMMENT 'File path to hospital logo',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_insurances`
--

CREATE TABLE `doctor_insurances` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `insurance_name` varchar(255) NOT NULL,
  `insurance_provider` varchar(255) DEFAULT NULL,
  `policy_number` varchar(100) DEFAULT NULL,
  `coverage_amount` decimal(12,2) DEFAULT NULL,
  `premium_amount` decimal(10,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `policy_document` varchar(500) DEFAULT NULL COMMENT 'File path to insurance policy',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_profiles`
--

CREATE TABLE `doctor_profiles` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `profile_image` varchar(500) DEFAULT NULL COMMENT 'File path to profile image',
  `bio` text DEFAULT NULL COMMENT 'Professional biography',
  `specialty` varchar(255) DEFAULT NULL,
  `languages_spoken` varchar(500) DEFAULT NULL COMMENT 'Comma-separated languages',
  `consultation_fee` decimal(8,2) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `total_appointments` int(11) DEFAULT 0,
  `total_reviews` int(11) DEFAULT 0,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `is_available` tinyint(1) DEFAULT 1,
  `availability_status` enum('Available','Busy','Away','Offline') DEFAULT 'Available',
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `social_media_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Social media profiles as JSON' CHECK (json_valid(`social_media_links`)),
  `last_online` timestamp NULL DEFAULT NULL,
  `profile_completion` tinyint(3) DEFAULT 0 COMMENT 'Profile completion percentage',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gender` varchar(20) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `degrees` varchar(255) DEFAULT NULL,
  `currently_working` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `bmdc_certificate` varchar(255) DEFAULT NULL,
  `nid_card` varchar(255) DEFAULT NULL,
  `degrees_certificate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctor_profiles`
--

INSERT INTO `doctor_profiles` (`id`, `doctor_id`, `profile_image`, `bio`, `specialty`, `languages_spoken`, `consultation_fee`, `experience_years`, `total_appointments`, `total_reviews`, `average_rating`, `is_available`, `availability_status`, `address`, `city`, `state`, `zip_code`, `emergency_contact`, `website`, `social_media_links`, `last_online`, `profile_completion`, `is_featured`, `created_at`, `updated_at`, `gender`, `account_number`, `degrees`, `currently_working`, `department`, `present_address`, `district`, `bmdc_certificate`, `nid_card`, `degrees_certificate`) VALUES
(5, 8, 'assets/img/doctors/doctor_8_1768311364.jpg', '', 'Pediatrician', NULL, 0.00, 0, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-01-13 13:36:04', '2026-01-24 07:04:53', 'Male', '0', '', '', '', '', NULL, '', '', ''),
(6, 9, 'assets/img/doctors/doctor_9_1777110184.jpg', '', 'General Physician, Pediatrician, Gynecologist', NULL, 100.00, 20, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-01-13 13:42:03', '2026-04-25 09:43:04', 'Male', '0', '', '', '', '', 'Gaibandha', '', '', ''),
(8, 10, 'assets/img/doctors/doctor_10_1777111080.jpeg', '', 'General Physician', NULL, 200.00, 7, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-01-17 08:21:27', '2026-04-25 09:59:48', 'Male', '1783652932', 'MBBS, DMU', '', '', '', '', '', '', ''),
(9, 13, 'assets/img/doctors/doctor_13_1769006497.jpg', '', 'Gynecology,Diabetology & Sonology', NULL, 400.00, 2, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-01-21 14:41:37', '2026-01-21 14:41:37', 'Female', '1913846928', 'MBBS,CCD,DMUa', 'Jhikorgachha Upazila Health Complex, Jashore', 'Gynecology,Diabetology & Sonology', 'Jashore', NULL, '', '', ''),
(19, 15, 'assets/img/doctors/doctor_15_1769978288.png', '', 'General Physician', NULL, 500.00, 5, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-01-27 04:03:38', '2026-02-01 20:38:08', 'Male', '1818284038', 'MBBS, CCD (BIRDEM)', '', '', '', NULL, '', '', ''),
(24, 16, 'assets/img/doctors/doctor_16_1772450819.jpg', '', 'General Physician, Gynecologist', NULL, 300.00, 5, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-03-02 11:10:41', '2026-03-02 11:26:59', 'Female', '1789904269', 'MBBS', '', '', '', 'Dhaka', 'assets/uploads/certificates/bmdc_16_1772449922.pdf', 'assets/uploads/documents/nid_16_1772449922.pdf', ''),
(46, 19, 'assets/img/doctors/doctor_19_1777220868.jpg', '', 'General Physician', NULL, 0.00, 0, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-04-26 16:27:48', '2026-04-26 16:27:48', 'Male', '0', 'MBBS', '', '', 'Dhaka', 'Dhaka', '', '', ''),
(47, 12, 'assets/img/doctors/doctor_12_1777221165.png', 'Lecturer of Anatomy department of Ad din Sakina Women\'s Medical College Jashore', '', NULL, 300.00, 2, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-04-26 16:32:45', '2026-04-26 16:32:45', '', '0', 'MBBS', '', '', '', '', '', '', ''),
(48, 21, 'assets/img/doctors/doctor_21_1777276570.JPG', '', 'General Physician', NULL, 200.00, 8, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-04-27 07:56:10', '2026-04-27 07:56:10', 'Male', '1725047111', 'MBBS', '', '', 'Dhaka', '', '', '', ''),
(51, 23, 'assets/img/doctors/doctor_23_1777303532.png', '', 'General Physician', NULL, 500.00, 9, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-04-27 14:42:29', '2026-04-28 02:57:03', 'Male', '1917785973', '', '', '', '', '', '', '', ''),
(55, 24, 'assets/img/doctors/doctor_24_1777307958.jpg', '', 'General Physician, Pulmonologist, Internal Medicine', NULL, 0.00, 5, 0, 0, 0.00, 1, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-04-27 16:39:18', '2026-04-27 16:49:39', 'Female', '0', 'MBBS', 'United Medical College Hospital, Clinical & Interventional Pulmonology', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_sessions`
--

CREATE TABLE `doctor_sessions` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctor_sessions`
--

INSERT INTO `doctor_sessions` (`id`, `doctor_id`, `session_token`, `ip_address`, `user_agent`, `device_info`, `expires_at`, `is_active`, `created_at`) VALUES
(1, 4, '105f4bbde376fd6b9a084bdf3fbe121c900a4fc77709fa984f333769946e7465', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', NULL, '2026-02-10 11:25:14', 1, '2026-01-11 05:25:14'),
(2, 9, '3e9690a1a0cfca7827d6dfd2b65358e41f7223dc4e21bdafee65c008702ceceb', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', NULL, '2026-02-10 19:37:42', 1, '2026-01-11 13:37:42'),
(3, 9, '09863a0ccceb706e0346b1249c7114c5055c90f17469f2a066770140b04b07eb', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', NULL, '2026-02-10 20:31:00', 1, '2026-01-11 14:31:00'),
(4, 4, '19cb3eb517a5cf777850c61001e450a0bb6f8af4411485d7fde6b6ee2b115faa', '182.252.79.150', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, '2026-02-10 22:23:30', 1, '2026-01-11 16:23:30'),
(5, 8, '51037033ba31aef7148e76b88fe089f7a602b7e0dd3345bd89b13ca8501ebbfb', '203.99.145.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-12 15:33:22', 1, '2026-01-13 09:33:22'),
(6, 9, '3af720e4f4d06221a742881900172040bece5a8a2d6602964892a1bd857e59b9', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', NULL, '2026-02-12 15:49:06', 1, '2026-01-13 09:49:06'),
(7, 8, 'd332d256f2be5e7cd6fbcc0b0ade25de9d1a77b0175ca92b41e12b8cf9afb251', '203.99.145.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-12 16:02:37', 1, '2026-01-13 10:02:37'),
(8, 8, '390afd9f3289e7b439aaf2cb9a63ef9e5fe0f934e0b1dfd38b75014c15bccac2', '203.99.145.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-12 18:25:19', 1, '2026-01-13 12:25:19'),
(9, 8, 'e410ac4057cbd500b2df2404ee66d61b2a4310f97a0d70a2f9c4879836e0ee39', '203.99.145.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-12 19:34:03', 1, '2026-01-13 13:34:03'),
(10, 9, 'de2a949a9b0033ede7373ffcda082a7dfb23d47dc573ca6555b9a73df071ad1d', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', NULL, '2026-02-12 19:40:33', 1, '2026-01-13 13:40:33'),
(11, 8, 'cd2dac40c567133938dd03e58d2fe097874e6d51dec88c5fa19f49e307adbb45', '203.99.145.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-12 19:44:29', 1, '2026-01-13 13:44:29'),
(12, 8, 'fdc010f85f1d4d2a4cfab8a2720dfbc45ec3b310343108c103257c662c94e110', '203.99.145.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-12 19:44:56', 1, '2026-01-13 13:44:56'),
(13, 9, 'c38076d2f2e4797aaf560a761105f2ffe9f64767c56bcc6f15f5cf2dbde49f3d', '203.99.145.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-12 19:50:10', 1, '2026-01-13 13:50:10'),
(14, 10, '467b4945afc32aa4f0271edf2fbb6858c2d2dc2a283cd46d284b895af66329d8', '182.252.93.237', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-16 14:32:20', 1, '2026-01-17 08:32:20'),
(15, 9, '2f8a8f0fa6f0f612422ddb856f675142ad42640d99e4f3f8bd46bc1d7b0733cc', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-16 15:46:51', 1, '2026-01-17 09:46:51'),
(16, 10, 'd4a7456fe966d84715e69c2cab2ce7e8194d4456a6ea5007ad4b2598dfa43e9a', '37.111.194.113', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-16 23:18:44', 1, '2026-01-17 17:18:44'),
(17, 9, '9ab2285fff1a7d4742ed2163d49002ff76391e0fad458339ee3170fe7e536a69', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-16 23:18:50', 1, '2026-01-17 17:18:50'),
(18, 8, '5ae63443552c6592e7c42cde169c1a131c668c5f9eebd801baf1c2a8ee5d91ad', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-17 15:16:38', 1, '2026-01-18 09:16:38'),
(19, 4, '444ec1b962bea52ac16cc0a8985c9b35ba53e33038547df6d8986dc6101e4936', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-17 15:18:48', 1, '2026-01-18 09:18:48'),
(20, 4, 'b2ccf03f8a6756713d828f5cb376da4b69b590aa93fa1e3f3f6379deb5566b4d', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-18 10:21:24', 1, '2026-01-19 04:21:24'),
(21, 8, 'ee696855d3b2aa4c52ef25e58afd251662ee20dcb9dadbc6ca4a5fbeeec1f0fa', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-18 10:25:55', 1, '2026-01-19 04:25:55'),
(22, 8, 'c64580d5f10b896f9aa41bf8241c6b85df4926d647d3f864fdab736b862cd2b6', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-19 18:43:28', 1, '2026-01-20 12:43:28'),
(23, 10, 'e23697ec40334e2e8e3768644d3aa87c0267ba39c9012283823fda86bfcab500', '103.134.38.115', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-19 22:52:04', 1, '2026-01-20 16:52:04'),
(24, 9, '1d87fbad8d31b5b7299a2cc23000e4c69f22531690ccaf3adb7cf11977fd4836', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-20 12:44:04', 1, '2026-01-21 06:44:04'),
(25, 12, '25c748e2afba5a8003fbfde522be60a44ca8482609f11d3201a996ef84e32799', '37.111.213.153', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, '2026-02-20 19:46:22', 1, '2026-01-21 13:46:22'),
(26, 13, 'f36de1267c3671dbe5830c385ecd8e2f3c2e522d5d099e454123b621420cc633', '114.130.188.220', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', NULL, '2026-02-20 21:14:50', 1, '2026-01-21 15:14:50'),
(27, 9, '9349c64d1982d3e134f9463fe0b1e306b3f4681c9cee0dc7a9022b2c86384a4f', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-21 18:09:59', 1, '2026-01-22 12:09:59'),
(28, 10, '9820c100a36097a5c5724529a4d9088fa5b0d09b8981856b06fdd14b387855a5', '103.134.38.115', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-22 19:59:58', 1, '2026-01-23 13:59:58'),
(29, 10, 'b69435b4ae6b89b3eea5bf70c11c58786335bcbabbc9087bb4c509c58b479804', '103.134.38.115', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-22 20:02:36', 1, '2026-01-23 14:02:36'),
(30, 10, '719ca7537d8596c54f60a3736f49e428998f2ee368a01988af9ddba3416e8c69', '103.134.38.115', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-02-22 20:04:59', 1, '2026-01-23 14:04:59'),
(31, 9, '9c22a58ad2ddf068664b1537105251f78a115bbee4a232b659f3f2f3c3747291', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 09:31:27', 1, '2026-01-24 03:31:27'),
(32, 9, 'bf75494da87b2132cf913c2230c32d442d0a7bda468463e530b3ca272ce5f51e', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 11:45:38', 1, '2026-01-24 05:45:38'),
(33, 9, '649b46b01076ceaa3353176400544606bcd91f10b6c96952bd49fdcef94e82e6', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 11:49:36', 1, '2026-01-24 05:49:36'),
(34, 8, 'acb351701254ddd0a42ae02f712ecf4f6343ac35bd0a567c239abbffaa068e44', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-23 12:15:30', 1, '2026-01-24 06:15:30'),
(35, 9, '6b7cc6259e19e7f6aec6b00a11db5813e46a06b7ca9e926815e0ba02cc3bd2be', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 13:00:52', 1, '2026-01-24 07:00:52'),
(36, 9, '95661de73ba64c0c7e544bc40798b9f76c1598f81e6e1f0d18b97efb4e93db84', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 13:01:19', 1, '2026-01-24 07:01:19'),
(37, 9, 'f5958583fac0951f7fdd24f17209e857b0bf0211bb87cd2b791b10870cdff3f8', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 13:01:54', 1, '2026-01-24 07:01:54'),
(38, 9, '345497c6db86220cf55fa6836bcecf7e9de1b86f9d18bc024ee25b7c00356ef4', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 13:04:34', 1, '2026-01-24 07:04:34'),
(39, 9, '9b80176a29b6d01410846fe4a7240d1849a556be3c0e9345cf0298f23080fde5', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 13:04:43', 1, '2026-01-24 07:04:43'),
(40, 8, '9a49b74ee19bf2aec0fe2eb88c52db30007a45613aa4dc8f70b3d19f1ba39782', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-23 13:04:44', 1, '2026-01-24 07:04:44'),
(41, 9, '62a0facd813fb31131ffb77851854919b4479c7d09f128245793333802c71c9a', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 13:09:01', 1, '2026-01-24 07:09:01'),
(42, 8, '1ddf8bb98d3733cb62bcd5aa3d0a9b30dd575ef9a899cfb00480a49e57811704', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-23 13:38:36', 1, '2026-01-24 07:38:36'),
(43, 8, '545cdc1b3dfc844ed18eaf8353b21ad834a143810b1cd846accf3d29be063770', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-23 13:45:05', 1, '2026-01-24 07:45:05'),
(44, 9, '7caea1a5ac7c94e4edbde5a5f36223266b0503b0a66e62df71c9a0fedb986f23', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 14:05:10', 1, '2026-01-24 08:05:10'),
(45, 9, 'd72d6116077cabd6047255557e71f8720e77bc2fe8cba6d43a3fa2050c403f72', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 14:06:07', 1, '2026-01-24 08:06:07'),
(46, 9, '20212cd3b4046e2850cfb8faba0ad07110b491b14b55e9c9409cacd75a103022', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-23 15:46:08', 1, '2026-01-24 09:46:08'),
(47, 9, '1d09745328836abd081aa7628a3a89ba7c607d312edffdceac6042788aed35db', '202.134.11.254', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', NULL, '2026-02-24 00:20:45', 1, '2026-01-24 18:20:45'),
(48, 10, 'd52e67f13f3cf5f1f1abb9d18ef0c14859e5365e5807b1f1ab71ba626a9f46a9', '37.111.194.98', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/144.0.7559.95 Mobile/15E148 Safari/604.1', NULL, '2026-02-24 23:42:20', 1, '2026-01-25 17:42:20'),
(49, 9, '48eac7a8abd439af387630adf76ab95660a10fd6a7a91dbfb014a3c3c470ac26', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-25 13:55:58', 1, '2026-01-26 07:55:58'),
(50, 15, 'a2937d2afaa3c928705ce5cbe74ee030db97ba93adda521493ee4772c54bce1e', '182.252.93.51', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Safari/605.1.15', NULL, '2026-02-26 09:45:18', 1, '2026-01-27 03:45:18'),
(51, 9, '3291f5d008f9f25a61c5fc60369d4a25429765d18cbf26f8c5a851da8e047a10', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-26 11:18:50', 1, '2026-01-27 05:18:50'),
(52, 9, '51634daea302edf8c9f359e29ebab1a01c3c72d934ed2308fe3068ae0f708a8a', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-28 18:46:51', 1, '2026-01-29 12:46:51'),
(53, 9, '26568f097b3b8ba2c36df643f55e5689f38d6c41b10b3fca53de3fe3b03d536b', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-02-28 18:48:43', 1, '2026-01-29 12:48:43'),
(54, 10, 'f44f2133aef2f51ce29933c439eea5552dbdd06635b1e3c03481aad1e96cedfa', '103.134.38.113', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-03-01 21:39:19', 1, '2026-01-30 15:39:19'),
(55, 10, '591dadbd9fdc493b26af55beaf604fc9834cc600ccffc6886637aa3117c145ca', '118.179.57.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-03-02 10:16:22', 1, '2026-01-31 04:16:22'),
(56, 9, '15f221ffa654403025b53f2f0a0197dc0054fdf96ba1e40490a483b8d02ea2ad', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-02 12:51:43', 1, '2026-01-31 06:51:43'),
(57, 15, '7ca5e001c5ecc315d7b1ab4fe29da397c286addef37b3f5a4905f46b904f4ebe', '103.156.189.79', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/145.0.7632.34 Mobile/15E148 Safari/604.1', NULL, '2026-03-04 02:34:11', 1, '2026-02-01 20:34:11'),
(58, 15, '2c95336bbd5370879319f9bf3c8af3a4609e03ce9bc4d6fa1f57f3cadd0860bf', '103.156.189.79', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/145.0.7632.34 Mobile/15E148 Safari/604.1', NULL, '2026-03-04 02:36:02', 1, '2026-02-01 20:36:02'),
(59, 9, 'b48be5eb46d274eca9f46d17f125669a8265190371d61a8bb0804e9d12fba141', '202.134.10.137', 'Mozilla/5.0 (X11; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-06 19:47:46', 1, '2026-02-04 13:47:46'),
(60, 10, 'f70dde2c6e0b10d92bfc0a42df6eb574eaa43e9e0befc30a4c41bb2dc0c4c40f', '103.134.38.113', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-03-08 21:15:03', 1, '2026-02-06 15:15:03'),
(61, 10, '060e5ccaa1cb4a80eda0cb5a8c5c7a897f6a257d73ecc86e3c99745e75a717ca', '182.48.64.190', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-10 12:12:35', 1, '2026-02-08 06:12:35'),
(62, 10, 'f2cf5e5aa2e68068f360716748c22ad6ad739987a97f8f0bb952b5924eef82b5', '37.111.253.88', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-03-14 14:40:09', 1, '2026-02-12 08:40:09'),
(63, 10, '3feff5457e9fc58ff09cfed6ad3cb4b5a8fdb0cd1e0124e14b81190f251554f2', '37.111.245.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-03-14 21:40:30', 1, '2026-02-12 15:40:30'),
(64, 10, 'fd74e8919f7754b16f897f0a0871f7e8415d77aed30630dfe15ee5fc1d637859', '37.111.243.231', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/144.0.7559.95 Mobile/15E148 Safari/604.1', NULL, '2026-03-15 21:07:01', 1, '2026-02-13 15:07:02'),
(65, 9, '5fd012f29b84aebb6de8c0fb0feb469c3b1289933c1d814c1f916db4eac3d89e', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-15 22:15:20', 1, '2026-02-13 16:15:20'),
(67, 9, '481509e1d25d28f9ab3d056bf13d247c250d3b81ec7850fff03b4a4425414892', '182.252.79.150', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-03-16 23:53:01', 1, '2026-02-14 17:53:01'),
(68, 9, '1eca4d2391d3ddb0ba5a6475988aafa8e5491b93237e60bf729786951be6408c', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-17 09:50:14', 1, '2026-02-15 03:50:14'),
(69, 4, 'b354b8bdd1cce78801affa12fd5289e52bafc2f9e65a17bf690eddfaf25787b5', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-17 13:24:54', 1, '2026-02-15 07:24:54'),
(72, 9, '21d57da47b179785e15a4ebc88070095cc53526829e4864de3beacf9f2eb7460', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-18 11:39:29', 1, '2026-02-16 05:39:29'),
(74, 9, '2dc7c680833bfcced8bae5d35000d2f960446ee4b1f8f6c49f26c26365302713', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-26 10:51:52', 1, '2026-02-24 04:51:52'),
(75, 9, 'fced2d14e402b786618bf72c5ca3477b99be3f1e947562842fe57d5d5f25e1c8', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-26 11:20:35', 1, '2026-02-24 05:20:35'),
(76, 9, '05246cf142720d98b9d73f42eed0bb44050e05c221483485238988fb17bea2fb', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', NULL, '2026-03-26 22:51:06', 1, '2026-02-24 16:51:06'),
(78, 9, 'dfbbcc83cb0e24b66c37720db05ace6bc38f9a5e475a1219081ded39eccc3dc8', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', NULL, '2026-03-28 23:24:02', 1, '2026-02-26 17:24:02'),
(79, 9, '09390096316431591687a0f1838f19031dd8dccbbcabf23160f9b0dde7fba766', '202.134.10.142', 'Mozilla/5.0 (X11; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', NULL, '2026-03-29 19:58:30', 1, '2026-02-27 13:58:30'),
(80, 10, 'f203bd03d746f0b1b70ddec2c24b701ab8588b073f851c3df793ad936e0ba6dd', '118.179.57.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-30 10:17:25', 1, '2026-02-28 04:17:25'),
(81, 16, '99573bf51b2ad03d5112a5982c0cba3b6ae0db90e08215081bd19f8f3c749aad', '103.162.230.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-01 17:06:09', 1, '2026-03-02 11:06:09'),
(82, 16, '1942808778e8e87abe8e434e88d51f6cc62b1d8570dd590ee50a34df79e7cfe5', '103.162.230.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-04-01 17:26:10', 1, '2026-03-02 11:26:10'),
(83, 16, '1caa2ac168d08713e99fa351af80d530df46a464a63924a45897f6fa6d70455c', '103.162.230.13', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-04-01 17:26:38', 1, '2026-03-02 11:26:38'),
(84, 8, 'a3fc868c285f320f7d8d0c1e0020f2eed474073d81f0f0b982948f04779ea0c5', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-04 11:18:31', 1, '2026-03-05 05:18:31'),
(86, 8, '0e8c2fb76704e029342fc1e1f36a04b42a43f54433d99bfa7566b57f7ed9b78d', '220.247.130.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-05 00:20:02', 1, '2026-03-05 18:20:02'),
(87, 10, '532ee9c28101b9bc6756a2698f25acddd08f39db2fa0db8b929f2f3563a70336', '103.134.38.114', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-05 02:46:42', 1, '2026-03-05 20:46:42'),
(89, 9, 'd779bfbe7692957cd97e8c13bb81f46d2ff8b66d9723889cf7304ce596d16ae9', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', NULL, '2026-04-08 15:00:57', 1, '2026-03-09 09:00:57'),
(90, 9, 'e9c1e3107248971df9df065dca291c543a8a219c132a923edfe244c74c02e199', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', NULL, '2026-04-08 15:01:05', 1, '2026-03-09 09:01:05'),
(91, 9, 'a8a2237aae0dddaf3911dc7519e60b333c4ef2b0f4d7fbd9b49e474198d5a1ba', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', NULL, '2026-04-08 15:01:10', 1, '2026-03-09 09:01:10'),
(92, 8, 'dc232f48f4e2d63c9746344d9dd1b03b911bed44ebbad384b7790d646ed713d2', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:04:42', 1, '2026-03-09 09:04:42'),
(93, 8, 'ec05757d0fa5209a89167f3587854ff8bd1d1a85198f5c19e7b9e8461158a9ba', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:04:57', 1, '2026-03-09 09:04:57'),
(94, 4, '7e51415baabaf7ae4c762ef06bf39374b7a8691834dca5cc804bce2c4ad9fa24', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:07:27', 1, '2026-03-09 09:07:27'),
(95, 17, 'c18b2507001aa117081e4f5c3736e4ae752adc97a51e1fcf8185c480911d91ca', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:13:02', 1, '2026-03-09 09:13:02'),
(96, 8, '03b9d3b018bad1bddedb139666614d6c72c756a5658554f11cd34ed15f2f27ce', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:14:26', 1, '2026-03-09 09:14:26'),
(97, 4, '829e1b3547708c39f9344154037f7b623467f04db9e46822f2bff8fc935cbb22', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:14:57', 1, '2026-03-09 09:14:57'),
(98, 4, 'fa80d157b5a6f7ec115928f6fdf3f00d3f3144c161c308d9d6fad5e79199d116', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:15:06', 1, '2026-03-09 09:15:06'),
(99, 17, 'ed8da1d6c3bc6fd41d12da54715bc3625ef66748b8529a5b9d7eaf3b619fd3cd', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:21:38', 1, '2026-03-09 09:21:38'),
(100, 17, '24ecc3f08b4cda56947d1a5de893436ea109192139cc7f5d66ba0f7e08a2d84f', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:22:18', 1, '2026-03-09 09:22:18'),
(101, 17, '0694d529f09269b82d15f7e930570420aa4d3f89fd669d3ff5e27907cf8a21a8', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:27:16', 1, '2026-03-09 09:27:16'),
(102, 4, 'b556f0e3d6d6861c8d27a88e60b2ccd09295b0c2be4c2a6544cfca45a3d6f120', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:28:03', 1, '2026-03-09 09:28:03'),
(103, 17, '7d0b788de23b6a1244ca365e7066be9f164367f74073cb9e88f1c599495eaa66', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:29:58', 1, '2026-03-09 09:29:58'),
(104, 17, 'b329411c6b29da4ba084f38ee91a16b0525a91113894cbb337b13c035cfcc6b8', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:31:14', 1, '2026-03-09 09:31:14'),
(105, 17, 'b12f7fcf20a2c7d55567eb5a1653ed6894b6e40889f41c6a34e3aa5070418708', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:31:42', 1, '2026-03-09 09:31:42'),
(106, 17, 'ef4d93f71b5897436ad543c36b1284fb6795abbae213d54067ff0063f0f63659', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:39:20', 1, '2026-03-09 09:39:20'),
(107, 4, '6ecdfd97fe992e4338f7a8ad579988cc3106c6dc477145ecd34e57ad158b7d66', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 15:40:05', 1, '2026-03-09 09:40:05'),
(108, 4, '8d47fcf3008a27496b1ef5cf65a5d0b6a6643dd8393676cd5bb2725a50980a00', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-08 16:00:09', 1, '2026-03-09 10:00:09'),
(109, 10, '48fc2f99ef1dac43c77dbf97f49ac21bd178fd9481f51465e74589a90063fedf', '37.111.242.172', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-04-18 17:38:54', 1, '2026-03-19 11:38:54'),
(110, 9, '3864732921b546fad6b077e3b796332b9d350a12f0a911dbb3884be6844406e2', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-04 10:31:20', 1, '2026-04-04 04:31:20'),
(111, 9, 'e610021e81c943f09d050757f515413e8a72d351f31264834ec93ec5ea4e7e82', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-04 10:50:22', 1, '2026-04-04 04:50:22'),
(115, 9, '2136568f1b6ef444281396be3f755d50d5067b5fb6194beec4d16409708b4088', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-05 23:09:03', 1, '2026-04-05 17:09:03'),
(116, 9, '57b26113327809d5a730d773c58506132ab05b426a54ed4c7fff9f91b40650d8', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-06 00:37:44', 1, '2026-04-05 18:37:44'),
(119, 9, 'a0e9ba8b75686d63b77c979a6a957664ba51abdb6a587101aee696650cb62107', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-06 16:06:27', 1, '2026-04-06 10:06:27'),
(120, 9, 'd4f696b6a8378ea5d8cb91de5283de7fac1bb1d6a056894dfc2e754437981956', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-07 00:24:04', 1, '2026-04-06 18:24:04'),
(122, 9, '2d7b884153dd68f5d77c6b156856b9be380b41cee4f4b325f73b751f8d69ec1b', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-07 01:12:23', 1, '2026-04-06 19:12:23'),
(123, 9, '3d78c0376c999bc03e8189789d3b1ca70b0b271369a009d8f71be2d040eca429', '202.134.11.245', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', NULL, '2026-05-07 01:41:49', 1, '2026-04-06 19:41:49'),
(124, 9, '74f7abe76af8e82b0de1e5c1878e4a38fb1b9628a6605b2133ce7c9f00699aec', '202.134.10.131', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', NULL, '2026-05-07 17:43:45', 1, '2026-04-07 11:43:45'),
(125, 9, 'db6ad9b95f1f858fe2a512aeef72205e6980287357e7509138c66679edcba73d', '202.134.10.131', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', NULL, '2026-05-07 17:44:27', 1, '2026-04-07 11:44:27'),
(126, 9, '2d795a851d0212593653795995e783abad3f996501b34f1c222e0d4e09b52175', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-05-07 17:51:07', 1, '2026-04-07 11:51:07'),
(127, 9, '89a420ef43176fbbd7b4b3266084c7484d116722c0c1149dc198477d437761c1', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-07 18:34:34', 1, '2026-04-07 12:34:34'),
(128, 9, 'd84c3249c2d0c56050169d08d427f7b2dcada249ab6c93ea1f0805d7beff0941', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-08 13:05:25', 1, '2026-04-08 07:05:25'),
(129, 9, 'b5475b5cfdea2f591d37c620fde3b9ec7389b005ff61e89ac10287195c2cdfa9', '182.252.70.7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', NULL, '2026-05-08 13:06:12', 1, '2026-04-08 07:06:12'),
(130, 10, 'f60d5996d860aaf067ed66d6149d97d31c366bf170fb15779b90123bd17cb2f8', '118.179.57.142', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-05-09 10:03:22', 1, '2026-04-09 04:03:22'),
(132, 9, 'fe49a1dd89c6a3ba1833800f42249e4665f1ce018910c608d4509fda45ae2490', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-09 23:52:17', 1, '2026-04-09 17:52:17'),
(133, 9, '554e3d40c1c3ca59f6b11a837cc959f51502cb08ed02a8ca037bcd3aeeb75a1c', '203.99.145.14', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-05-09 23:55:25', 1, '2026-04-09 17:55:25'),
(134, 9, 'fe34f8150c12cbab3c29b39058bb878449f1e904fc66c36a21d6deee3eef26aa', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-10 00:23:53', 1, '2026-04-09 18:23:53'),
(139, 9, 'd806fa3c21e1439749356d1ec016d98a69f64f50ca082dabbc32fe79e70bc343', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-15 16:16:40', 1, '2026-04-15 10:16:40'),
(142, 9, '5321eefe1b9382643e0072123b96b9497fbbc73419ef495e297f55cc937b7954', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', NULL, '2026-05-20 16:06:22', 1, '2026-04-20 10:06:22'),
(143, 9, '19db677c612ec72727fa4ab29f101f7fbe5773a6d3040837f638e9541f0fd09c', '103.77.16.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-05-20 16:06:40', 1, '2026-04-20 10:06:40'),
(147, 9, 'e31e57021bc064543e1e3a3fe945ca557e883daa182c8c814f009f2bc460cf08', '103.77.16.176', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-05-24 16:29:25', 1, '2026-04-24 10:29:25'),
(148, 9, 'a6c71ebeb4f07a4a4b54c9a03920c8115cace8c12a792a2f4e1b8fbefd3699a4', '103.77.16.176', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-05-24 21:22:18', 1, '2026-04-24 15:22:18'),
(154, 9, 'c95480b1498c96f4127c5cb437d90befb3ee20f13e8997474417719ba4b2b825', '182.252.79.150', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', NULL, '2026-05-24 21:57:07', 1, '2026-04-24 15:57:07'),
(162, 9, '15a29f59655634a10746bd0c995d885a794156954e856a15ec5dd160239b6322', '182.252.70.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko/20100101 Firefox/150.0', NULL, '2026-05-25 15:42:42', 1, '2026-04-25 09:42:42'),
(163, 10, '871dd188c7d9cc613867dec653354eaf99aa7826a0344bd02f2b01abf25b1c14', '37.111.194.249', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/147.0.7727.99 Mobile/15E148 Safari/604.1', NULL, '2026-05-25 15:52:13', 1, '2026-04-25 09:52:13'),
(165, 12, '61060811de1095e1febd1d36dddd2f2be27bf80ae38d28dfde1bafa41d7b1ed4', '37.111.206.152', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-05-26 21:56:09', 1, '2026-04-26 15:56:09'),
(166, 19, '9dff6c3c756fd842edb51ed060def15285d71949128dd6276a3117f27680375b', '103.77.63.34', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', NULL, '2026-05-26 22:25:31', 1, '2026-04-26 16:25:31'),
(167, 12, 'b433d7c17d93ebd7fa5038755369224e6a24a8e7729385293c309b07930927cf', '37.111.206.152', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-05-26 22:31:13', 1, '2026-04-26 16:31:13'),
(168, 21, 'd879f591153fb8a85280660afcdeca1c83159760cf28cc96ea36144fcadd8a73', '103.78.226.77', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-05-27 13:54:34', 1, '2026-04-27 07:54:34'),
(170, 23, 'c5fa367b32bb18fac717bef4b57286b836f881ee9317720ac02ece8f1e80c845', '103.155.219.41', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', NULL, '2026-05-27 20:41:52', 1, '2026-04-27 14:41:52'),
(171, 10, '8f5644af28b64f98eba9eccc55b7d30173ca308ecd14a05281eb26409db6f912', '37.111.213.148', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/147.0.7727.99 Mobile/15E148 Safari/604.1', NULL, '2026-05-27 20:56:26', 1, '2026-04-27 14:56:26'),
(173, 23, '99789a88cfd20f89b6208d7ad353aa61cc9ab56829013ec9a180c4a98415817f', '103.155.219.41', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', NULL, '2026-05-27 21:24:55', 1, '2026-04-27 15:24:55'),
(174, 10, 'e3e4c832ccd3bdc8bc8255be28c5a5d0f616909a89eb4d753f237e30ba5102b4', '37.111.213.148', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/147.0.7727.99 Mobile/15E148 Safari/604.1', NULL, '2026-05-27 21:38:52', 1, '2026-04-27 15:38:52'),
(176, 23, 'e2e2980e7b7f70868d189fc6e8c0cc8f822bf1e566f9c46871dd810a16d9346e', '103.155.219.41', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', NULL, '2026-05-28 08:56:35', 1, '2026-04-28 02:56:35'),
(178, 9, 'e5b5710917f9ebe67a056478ba0c00352d56e9d606aa4d3afc56ef74b29a4b50', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko/20100101 Firefox/150.0', NULL, '2026-05-30 23:42:23', 1, '2026-04-30 17:42:23'),
(179, 9, 'acd3bc231ae36ac2740d43172d52929e8e2c530de7f9e56a860b38a78cea9b80', '103.77.16.176', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', NULL, '2026-05-30 23:42:53', 1, '2026-04-30 17:42:53'),
(180, 19, '53774b511e61c9c68a77f90c47975abd56fc21060073c0578047c603cfaacd3d', '103.77.63.35', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', NULL, '2026-05-31 22:05:37', 1, '2026-05-01 16:05:37'),
(181, 9, 'a476e6511321cc6d0bf83ee019220df747fafc25007ed8a7cb993950ec2dde8c', '182.252.79.150', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', NULL, '2026-06-09 00:28:04', 1, '2026-05-09 18:28:04'),
(182, 10, 'd6f265985681dfce5a0e08b7ce175cc720092c4de2b161ff8a10572ddb73b3f6', '103.244.49.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-10 17:19:13', 1, '2026-05-11 11:19:13'),
(183, 10, '4f4ea547c770730a4bc6a633715cbd0e47d29ccac7fce88820ae490a87d12687', '37.111.194.127', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/148.0.7778.166 Mobile/15E148 Safari/604.1', NULL, '2026-06-15 20:54:24', 1, '2026-05-16 14:54:24'),
(186, 9, '964490a7decad94f904bf7130219f6b8a843d58de7af413dedf8c5a8b4bc7d27', '182.252.79.150', 'Mozilla/5.0 (Android 16; Mobile; rv:150.0) Gecko/150.0 Firefox/150.0', NULL, '2026-06-16 00:17:42', 1, '2026-05-16 18:17:42'),
(187, 9, '6f973bbb3eda1228abc1539bd641738339d05b6edf5eb3349beab5cf9a524df7', '220.247.131.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-16 01:48:47', 1, '2026-05-16 19:48:47'),
(189, 9, '376a54e808614aa901c47597f238ef1992c2a7b04dd716057020a53c6dc8d486', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko/20100101 Firefox/150.0', NULL, '2026-06-16 10:49:01', 1, '2026-05-17 04:49:01'),
(190, 9, 'abe55b991fd1625c8cbf972933c92bb16d9a35ad83afe9de5520c0148b3640d3', '182.252.79.150', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko/20100101 Firefox/150.0', NULL, '2026-06-16 12:13:08', 1, '2026-05-17 06:13:08'),
(192, 10, '86b1437f6656fee30f2f41bf5a7981c079cf576df924d7fc3fea4027e4001e82', '37.111.194.149', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/148.0.7778.166 Mobile/15E148 Safari/604.1', NULL, '2026-06-16 12:43:14', 1, '2026-05-17 06:43:14'),
(193, 10, 'd76a69c4a2d693621b3a36da41daf1ee5773abb66eb10475ea83eef9f3a74406', '182.160.122.131', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-16 12:47:12', 1, '2026-05-17 06:47:12'),
(194, 10, '1a69f32a88708c6079fbd8b0016dc0bde91c1ab009d81318a46fd5d5da27bdfa', '182.160.122.131', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-16 12:47:47', 1, '2026-05-17 06:47:47'),
(195, 10, 'e395b73d1917588e540aa6f25fe1d106c25c2f6353f876b3d49152346697c38f', '182.160.122.131', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-16 12:49:42', 1, '2026-05-17 06:49:42'),
(196, 9, '10d1f1e98e6968ed731109163e0a432c48c23831dad987be8ebf69c1b79e27b2', '103.77.16.178', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-24 01:14:12', 1, '2026-05-24 19:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `healthcare_providers`
--

CREATE TABLE `healthcare_providers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nid_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `tid` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `healthcare_providers`
--

INSERT INTO `healthcare_providers` (`id`, `name`, `email`, `nid_number`, `password`, `phone`, `tid`, `created_at`, `updated_at`) VALUES
(3, 'asads', 'aerialhassan@gmail.com', '3222404000063', '$2y$10$UgTzPMz8ZOmpHdexw.n5d.7yzXYFENxjrmeae11/OUqQTn4diwUUy', '01616858822', 'T1001', '2026-01-31 03:39:00', '2026-02-08 09:52:30'),
(4, 'golam', 'g.mohammad652932@gmail.com', '7795634257', '$2y$10$76YydIRsuXSouxwLaIFBk.eBKgdbrNBwm2/ON3ZGt/CS8L1OGSS3O', '01783652932', 'T1002', '2026-01-31 04:10:36', '2026-01-31 04:10:36'),
(5, 'Mst.Surovi Akter', 'mostsuroviakter12@gmail.com', '6004586142', '$2y$10$l2T80bgMefwYz1gTT7lFJOgykdpuKU.gZ4HZJU.mXutIoT5VDTL4W', '01755250767', 'T1003', '2026-02-28 05:27:06', '2026-02-28 05:27:06'),
(6, 'Satyajoy Mondol', 'satyajoylearn@gmail.com', '5987489373', '$2y$10$Bi67TaGzLw7mExt4Ch0FN.O4DIZVwurAXJfJEwwsC77bswA6so7Ba', '01937300432', 'T1004', '2026-03-01 05:30:39', '2026-03-01 05:30:39'),
(7, 'Mst.Shorna Iasmin', 'sornajs@gmail.com', '6005591414', '$2y$10$oF.JQ6fVJk0bz.46GhE4SO55vKPtiGmhZYGVJ7QHh/fB9O8uh1/qS', '01744868616', 'T1005', '2026-03-07 04:27:38', '2026-03-07 04:27:38'),
(8, 'Sonjoy Mondol', 'cassiemchamplin770@gmail.com', '8658367316', '$2y$10$XeO45fjKX5hszad/6Ab2XOsOjiMUs1n04n8UJyFV1ITZTSh8xs03O', '01836007354', 'T1006', '2026-03-21 14:39:05', '2026-03-21 14:39:05'),
(9, 'Saikat mondal', 'mondalsaikat@gmali.com', '7823150185', '$2y$10$TVbj9Qh1HiuZFoAjpPS/7egSpTwKXS9NqMVqL9J.VZl0I0p6TdJSG', '01966871085', 'T1007', '2026-03-21 15:47:11', '2026-03-21 15:47:11'),
(10, 'Gmohammad', 'gm@gmail.com', '7562396756', '$2y$10$fyQeE2j3nKdmhHNSBgoKv.g/wotHnd.UbZFCNWuhOsl.lOrJ4SxUC', '01335053237', 'TID01335053237', '2026-04-30 06:06:03', '2026-04-30 06:06:03');

-- --------------------------------------------------------

--
-- Table structure for table `healthcare_providers_profiles`
--

CREATE TABLE `healthcare_providers_profiles` (
  `id` int(11) NOT NULL,
  `healthcare_provider_id` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `degrees` varchar(255) DEFAULT NULL,
  `currently_working` varchar(255) DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `nid_file` varchar(255) DEFAULT NULL,
  `degrees_certificate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `family_members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`family_members`)),
  `district` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `healthcare_providers_profiles`
--

INSERT INTO `healthcare_providers_profiles` (`id`, `healthcare_provider_id`, `profile_image`, `bio`, `gender`, `degrees`, `currently_working`, `present_address`, `nid_file`, `degrees_certificate`, `created_at`, `updated_at`, `family_members`, `district`) VALUES
(1, 3, '', NULL, '', '', '', '', '', '', '2026-01-31 03:40:42', '2026-02-08 09:52:30', '[{\"relation\":\"Father\",\"name\":\"asads\",\"nid\":\"asdasdas\"}]', 'Gaibandha'),
(7, 5, '', NULL, 'Female', 'Diploma in nursing science and midwifery', 'Brac University medical centre', 'South badda', '', '', '2026-02-28 05:28:56', '2026-02-28 05:28:56', '[]', 'Gaibandha'),
(8, 7, '', NULL, 'Female', 'Diploma in science and midwifery', 'Brac university', 'Merul badda ,Dhaka', '', '', '2026-03-07 04:33:50', '2026-03-07 04:33:50', '[]', 'Rajshahi');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `email`, `password`, `phone`, `date_of_birth`, `gender`, `blood_group`, `emergency_contact`, `address`, `city`, `state`, `zip_code`, `is_active`, `email_verified`, `created_at`, `updated_at`) VALUES
(3, 'nahid', 'nuharat.nahid@gmail.com', '$2y$10$d8ZHUE6a.22UF9MUK0q3dexxBUK9ZSvak540EMvWEUl0GvfnAPdlq', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-01-31 04:14:13', '2026-01-31 04:14:13'),
(4, 'Mehedi', 'svdopro@gmail.com', '$2y$10$P1MKZqO5UCK/KnHDvklmiuwgUM.GGlwZWRQ..pZHYzULDhOuhqZqe', '01933890894', '2026-04-17', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-02-17 17:26:47', '2026-04-30 17:40:02'),
(5, 'Abdullah Al Mamun', 'mamunovi1000@gmail.com', '$2y$10$qsgP7Gp4ZwenH0VU5N2h4.jRGu9ndui4hXmcMsjbXd7m8Xq9uhpwe', '01303327890', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-02-19 08:24:30', '2026-02-19 08:24:30'),
(7, 'Saikat mondal', 'saikatmondal1920x@gmail.com', '$2y$10$VKlL/RWotedy98mH.TkJ4upesyW6YfXRZj7V6jJQ2hMYorzs1OZiu', '01966871085', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-03-21 15:20:41', '2026-03-21 15:20:41'),
(8, 'Namirah Chowdhury Aanaya', 'shompah1@gmail.com', '$2y$10$LUdG/O/Y.Qom808Pw.1Nwu1.AqdhLeVc4/5SESm5Rfrn82NGrEy32', '01677832046', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-04-27 14:32:35', '2026-04-27 14:32:35'),
(12, 'Dr.Salina Banu', 'dr.muktasab@gmail.com', '$2y$10$TnKhcAM24ggcABaH.PTO0e3bXebApKpjcy21i.6732U6Y9fpRkx2.', '01740652545', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-05-07 06:30:21', '2026-05-07 06:30:21'),
(14, 'Md. Musa Ali', 'musa2243@gmail.com', '$2y$10$13YpFYg7clxtj90DoQDnpOlKeNcRMmuD2frK4NtVBArzCcNR44idW', '01814637930', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-05-11 08:09:37', '2026-05-11 08:09:37'),
(22, 'Amena begum', 'patient01763892178@auto.telerx.local', '$2y$10$LbWJeiYQpYR.4e9u/aNqt.erDUSC5IyMuEvO5xqrWVelwG7wziOu6', '01763892178', NULL, 'Female', '', NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-05-17 05:08:03', '2026-05-17 06:17:44'),
(23, 'Amena begum', 'patient00000001097@auto.telerx.local', '$2y$10$ukmVfGHE0QiJzYKNVAs8U.11qcQPU142sfYUOyIExEoEYwpNXD1vC', '00000001097', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-05-17 05:13:59', '2026-05-17 05:13:59'),
(24, 'Rosida begum', 'patient00000001109@auto.telerx.local', '$2y$10$6giTTFh5RWxeeS61oHwTqO/R5JGAtWzPdaUUT.osBBbuMeTyKXLO2', '00000001109', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-05-17 05:37:14', '2026-05-17 05:37:14'),
(25, 'Jamiron', 'patient00000001159@auto.telerx.local', '$2y$10$BhyHz4JnWAs85mG57ZJCfuKlgpDk6lg6zehdjvCbYDDEi0nXq8H3m', '00000001159', NULL, 'Female', '', NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-05-17 05:51:49', '2026-05-17 05:52:53'),
(26, 'Demo', 'patient01616585588@auto.telerx.local', '$2y$10$rUUInLBCb.dnRthRyg0N2.JfcSUmdX0MGPra8fCSv5pgdvqxbDYri', '01616585588', NULL, 'Male', '', NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-05-17 06:21:40', '2026-05-17 06:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `special_tid_users`
--

CREATE TABLE `special_tid_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `tid` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `healthcare_provider_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `special_tid_users`
--

INSERT INTO `special_tid_users` (`id`, `name`, `email`, `mobile`, `tid`, `password`, `healthcare_provider_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'asads', 'aerialhassan@gmail.com', '01616858822', 'T1001', '$2y$10$UgTzPMz8ZOmpHdexw.n5d.7yzXYFENxjrmeae11/OUqQTn4diwUUy', 3, 1, '2026-04-28 04:17:39', '2026-04-28 04:17:39'),
(2, 'golam', 'g.mohammad652932@gmail.com', '01783652932', 'T1002', '$2y$10$76YydIRsuXSouxwLaIFBk.eBKgdbrNBwm2/ON3ZGt/CS8L1OGSS3O', 4, 1, '2026-04-28 04:17:39', '2026-04-28 04:17:39'),
(3, 'Mst.Surovi Akter', 'mostsuroviakter12@gmail.com', '01755250767', 'T1003', '$2y$10$l2T80bgMefwYz1gTT7lFJOgykdpuKU.gZ4HZJU.mXutIoT5VDTL4W', 5, 1, '2026-04-28 04:17:39', '2026-04-28 04:17:39'),
(4, 'Satyajoy Mondol', 'satyajoylearn@gmail.com', '01937300432', 'T1004', '$2y$10$Bi67TaGzLw7mExt4Ch0FN.O4DIZVwurAXJfJEwwsC77bswA6so7Ba', 6, 1, '2026-04-28 04:17:39', '2026-04-28 04:17:39'),
(5, 'Mst.Shorna Iasmin', 'sornajs@gmail.com', '01744868616', 'T1005', '$2y$10$oF.JQ6fVJk0bz.46GhE4SO55vKPtiGmhZYGVJ7QHh/fB9O8uh1/qS', 7, 1, '2026-04-28 04:17:39', '2026-04-28 04:17:39'),
(6, 'Sonjoy Mondol', 'cassiemchamplin770@gmail.com', '01836007354', 'T1006', '$2y$10$XeO45fjKX5hszad/6Ab2XOsOjiMUs1n04n8UJyFV1ITZTSh8xs03O', 8, 1, '2026-04-28 04:17:39', '2026-04-28 04:17:39'),
(7, 'Saikat mondal', 'mondalsaikat@gmali.com', '01966871085', 'T1007', '$2y$10$TVbj9Qh1HiuZFoAjpPS/7egSpTwKXS9NqMVqL9J.VZl0I0p6TdJSG', 9, 1, '2026-04-28 04:17:39', '2026-04-28 04:17:39'),
(8, 'Special TID User', 'specialtid@gmail.com', NULL, 'T-SPECIAL-001', '$2y$10$GQOBCR3YcdqejI4K4PnLa.0b5zgFmX/RuQ4NvtR/ndgB9R/yeNLqe', NULL, 1, '2026-04-28 04:19:10', '2026-04-28 04:42:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_doctor_date_time` (`doctor_id`,`appointment_date`,`slot_time`),
  ADD KEY `idx_doctor_date` (`doctor_id`,`appointment_date`),
  ADD KEY `idx_patient_date` (`patient_id`,`appointment_date`),
  ADD KEY `idx_created_by_special_tid` (`created_by_special_tid_id`);

--
-- Indexes for table `call_sessions`
--
ALTER TABLE `call_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `channel_name` (`channel_name`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_account` (`sender_account`),
  ADD KEY `receiver_account` (`receiver_account`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `bmdc_no` (`bmdc_no`),
  ADD KEY `idx_doctors_email` (`email`),
  ADD KEY `idx_doctors_phone` (`phone`),
  ADD KEY `idx_doctors_bmdc_no` (`bmdc_no`),
  ADD KEY `idx_doctors_department` (`department`),
  ADD KEY `idx_doctors_verified` (`is_verified`),
  ADD KEY `idx_doctors_active` (`is_active`);

--
-- Indexes for table `doctor_availability_ranges`
--
ALTER TABLE `doctor_availability_ranges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_day` (`doctor_id`,`day_of_week`);

--
-- Indexes for table `doctor_awards`
--
ALTER TABLE `doctor_awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_awards_doctor` (`doctor_id`),
  ADD KEY `idx_doctor_awards_year` (`award_year`);

--
-- Indexes for table `doctor_business_hours`
--
ALTER TABLE `doctor_business_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_doctor_clinic_day` (`doctor_id`,`clinic_id`,`day_of_week`),
  ADD KEY `idx_doctor_business_hours_doctor` (`doctor_id`),
  ADD KEY `idx_doctor_business_hours_clinic` (`clinic_id`),
  ADD KEY `idx_doctor_business_hours_day` (`day_of_week`),
  ADD KEY `idx_doctor_business_hours_available` (`is_available`);

--
-- Indexes for table `doctor_clinics`
--
ALTER TABLE `doctor_clinics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_clinics_doctor` (`doctor_id`),
  ADD KEY `idx_doctor_clinics_primary` (`is_primary`),
  ADD KEY `idx_doctor_clinics_active` (`is_active`);

--
-- Indexes for table `doctor_education`
--
ALTER TABLE `doctor_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_education_doctor` (`doctor_id`),
  ADD KEY `idx_doctor_education_year` (`year_of_completion`);

--
-- Indexes for table `doctor_experiences`
--
ALTER TABLE `doctor_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_experiences_doctor` (`doctor_id`),
  ADD KEY `idx_doctor_experiences_current` (`currently_working`);

--
-- Indexes for table `doctor_insurances`
--
ALTER TABLE `doctor_insurances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_insurances_doctor` (`doctor_id`),
  ADD KEY `idx_doctor_insurances_active` (`is_active`),
  ADD KEY `idx_doctor_insurances_expiry` (`expiry_date`);

--
-- Indexes for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_id` (`doctor_id`),
  ADD KEY `idx_doctor_profiles_specialty` (`specialty`),
  ADD KEY `idx_doctor_profiles_available` (`is_available`),
  ADD KEY `idx_doctor_profiles_featured` (`is_featured`),
  ADD KEY `idx_doctor_profiles_rating` (`average_rating`);

--
-- Indexes for table `doctor_sessions`
--
ALTER TABLE `doctor_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `idx_doctor_sessions_token` (`session_token`),
  ADD KEY `idx_doctor_sessions_expires` (`expires_at`),
  ADD KEY `idx_doctor_sessions_active` (`is_active`);

--
-- Indexes for table `healthcare_providers`
--
ALTER TABLE `healthcare_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nid_number` (`nid_number`),
  ADD UNIQUE KEY `tid` (`tid`),
  ADD KEY `idx_healthcare_providers_email` (`email`),
  ADD KEY `idx_healthcare_providers_nid` (`nid_number`),
  ADD KEY `idx_healthcare_providers_tid` (`tid`),
  ADD KEY `idx_healthcare_providers_phone` (`phone`);

--
-- Indexes for table `healthcare_providers_profiles`
--
ALTER TABLE `healthcare_providers_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `healthcare_provider_id` (`healthcare_provider_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_patients_email` (`email`),
  ADD KEY `idx_patients_phone` (`phone`),
  ADD KEY `idx_patients_active` (`is_active`);

--
-- Indexes for table `special_tid_users`
--
ALTER TABLE `special_tid_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_special_tid_email` (`email`),
  ADD UNIQUE KEY `uk_special_tid_tid` (`tid`),
  ADD UNIQUE KEY `uk_special_tid_mobile` (`mobile`),
  ADD KEY `idx_special_tid_hcp` (`healthcare_provider_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `call_sessions`
--
ALTER TABLE `call_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `doctor_availability_ranges`
--
ALTER TABLE `doctor_availability_ranges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `doctor_awards`
--
ALTER TABLE `doctor_awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_business_hours`
--
ALTER TABLE `doctor_business_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `doctor_clinics`
--
ALTER TABLE `doctor_clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_education`
--
ALTER TABLE `doctor_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctor_experiences`
--
ALTER TABLE `doctor_experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_insurances`
--
ALTER TABLE `doctor_insurances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `doctor_sessions`
--
ALTER TABLE `doctor_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `healthcare_providers`
--
ALTER TABLE `healthcare_providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `healthcare_providers_profiles`
--
ALTER TABLE `healthcare_providers_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `special_tid_users`
--
ALTER TABLE `special_tid_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_special_tid` FOREIGN KEY (`created_by_special_tid_id`) REFERENCES `special_tid_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `doctor_awards`
--
ALTER TABLE `doctor_awards`
  ADD CONSTRAINT `doctor_awards_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_business_hours`
--
ALTER TABLE `doctor_business_hours`
  ADD CONSTRAINT `doctor_business_hours_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_business_hours_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `doctor_clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_clinics`
--
ALTER TABLE `doctor_clinics`
  ADD CONSTRAINT `doctor_clinics_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_education`
--
ALTER TABLE `doctor_education`
  ADD CONSTRAINT `doctor_education_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_experiences`
--
ALTER TABLE `doctor_experiences`
  ADD CONSTRAINT `doctor_experiences_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_insurances`
--
ALTER TABLE `doctor_insurances`
  ADD CONSTRAINT `doctor_insurances_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  ADD CONSTRAINT `doctor_profiles_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_sessions`
--
ALTER TABLE `doctor_sessions`
  ADD CONSTRAINT `doctor_sessions_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `healthcare_providers_profiles`
--
ALTER TABLE `healthcare_providers_profiles`
  ADD CONSTRAINT `healthcare_providers_profiles_ibfk_1` FOREIGN KEY (`healthcare_provider_id`) REFERENCES `healthcare_providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `special_tid_users`
--
ALTER TABLE `special_tid_users`
  ADD CONSTRAINT `fk_special_tid_healthcare` FOREIGN KEY (`healthcare_provider_id`) REFERENCES `healthcare_providers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
