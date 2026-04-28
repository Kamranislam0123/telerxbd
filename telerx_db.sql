-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 27, 2026 at 05:44 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `telerx_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int UNSIGNED NOT NULL,
  `patient_id` int UNSIGNED NOT NULL DEFAULT '0',
  `doctor_id` int UNSIGNED NOT NULL,
  `appointment_date` date NOT NULL,
  `slot_time` varchar(5) NOT NULL,
  `appointment_time` varchar(5) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'confirmed',
  `appointment_number` varchar(20) DEFAULT NULL,
  `notes` text,
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
  `prescription_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `referrer_tid` varchar(20) DEFAULT NULL COMMENT 'Health worker TID (e.g. T1001)',
  `chief_complaints` text,
  `on_examination` text,
  `diagnosis` text,
  `medications` text,
  `advice` text,
  `payment_method` varchar(50) DEFAULT 'bkash' COMMENT 'bkash or welfare',
  `prescription_footer` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `slot_time`, `appointment_time`, `status`, `appointment_number`, `notes`, `patient_name`, `mobile`, `patient_phone`, `age`, `weight`, `body_temperature`, `blood_pressure`, `pulse`, `spo2`, `rbs_fbs`, `attachment_path`, `prescription_path`, `created_at`, `updated_at`, `referrer_tid`, `chief_complaints`, `on_examination`, `diagnosis`, `medications`, `advice`, `payment_method`, `prescription_footer`) VALUES
(1, 1, 1, '2026-03-05', '06:00', '06:00', 'completed', 'APT00001', 'ok', 'kamran', '01771971072', '01771971072', '50', '80', '100', '100', '100', '100', '100', NULL, NULL, '2026-03-05 02:42:39', '2026-04-22 16:04:11', NULL, NULL, NULL, NULL, NULL, NULL, 'bkash', NULL),
(2, 1, 1, '2026-03-05', '06:15', '06:15', 'completed', 'APT00002', 'kkmdmdm', 'kkkkk', '01771971079', '01771971079', '50', '80', '100', '100', '100', '100', '100', NULL, NULL, '2026-03-05 02:47:52', '2026-04-15 19:25:05', NULL, NULL, NULL, NULL, NULL, NULL, 'bkash', NULL),
(3, 1, 1, '2026-03-12', '06:00', '06:00', 'completed', 'APT00003', 'mmmmm', 'jjjjjj', '01771971079', '01771971079', '50', '100', '100', '100', '100', '100', '100', NULL, NULL, '2026-03-07 16:41:44', '2026-04-15 19:06:18', '', NULL, NULL, NULL, NULL, NULL, 'bkash', NULL),
(4, 1, 1, '2026-03-12', '11:45', '11:45', 'completed', 'APT00004', 'hekkko', 'ookk', '01771971075', '01771971075', '100', '80', '100', '', '100', '100', '120', NULL, NULL, '2026-03-08 04:37:56', '2026-04-15 17:53:01', 'T1001', NULL, NULL, NULL, NULL, NULL, 'bkash', NULL),
(5, 1, 1, '2026-04-09', '06:00', '06:00', 'completed', 'APT00005', 'heeello', 'Md Kamran', '01771971072', '01771971072', '100', '50', '60', '100', '100', '120', '56', NULL, 'assets/prescriptions/prescription_5_1775465235.pdf', '2026-04-06 06:49:00', '2026-04-15 17:51:15', '', NULL, NULL, NULL, NULL, NULL, 'bkash', NULL),
(6, 1, 1, '2026-04-16', '06:15', '06:15', 'completed', 'APT00006', 'kkkkkkkkkkkk', 'Md kala jahangir', '01771971090', '01771971090', '100', '100', '100', '100', '100', '100', '100', NULL, 'assets/prescriptions/Prescription_APT00006.pdf', '2026-04-12 15:33:15', '2026-04-20 10:01:36', '', 'abcd', 'no issue', 'nooot', '[{\"name\":\"napa\",\"dose\":\"1+1+1\",\"duration\":\"7\"},{\"name\":\"deslor\",\"dose\":\"1+1+1\",\"duration\":\"10\"}]', 'sleep', 'bkash', NULL),
(7, 1, 1, '2026-04-30', '06:00', '06:00', 'completed', 'APT00007', 'hello', 'Jahangir', '01771971009', '01771971009', '100', '100', '100', '100', '100', '100', '100', NULL, 'assets/prescriptions/Prescription_APT00007.pdf', '2026-04-23 14:34:05', '2026-04-23 14:38:36', '', 'hello', 'iokkk', 'kjhh', '[{\"name\":\"napa\",\"dose\":\"1+1+1\",\"duration\":\"7\"}]', 'kkkkk', 'bkash', NULL),
(8, 1, 1, '2026-04-30', '06:15', '06:15', 'confirmed', 'APT00008', 'heello', 'Alam', '01771971008', '01771971008', '100', '100', '100', '100', '100', '100', '100', NULL, 'assets/prescriptions/prescription_8_1777049886.pdf', '2026-04-23 15:12:13', '2026-04-24 16:58:06', '', 'vgbfgb', 'xgbgbsfg', 'gbfgbfg', '[{\"name\":\"gxfgxg\",\"dose\":\"gxfgxg\",\"duration\":\"\"}]', 'bzgbzgbz', 'bkash', 'gbzgbzgb');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int UNSIGNED NOT NULL,
  `sender_account` varchar(50) NOT NULL COMMENT 'Format: userType_id, e.g., doctor_1 or patient_5',
  `receiver_account` varchar(50) NOT NULL COMMENT 'Format: userType_id, e.g., doctor_1 or patient_5',
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_account`, `receiver_account`, `message`, `created_at`) VALUES
(1, 'doctor_1', 'patient_1', 'hello', '2026-04-09 09:46:29'),
(2, 'doctor_1', 'patient_1', 'hello', '2026-04-09 11:39:37'),
(3, 'patient_1', 'doctor_1', 'hello', '2026-04-09 11:40:40'),
(4, 'doctor_1', 'patient_1', 'This is  doctor', '2026-04-09 11:45:07'),
(5, 'patient_1', 'doctor_1', 'This is patient', '2026-04-09 11:47:18'),
(6, 'patient_1', 'doctor_1', 'How are you', '2026-04-09 12:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `bmdc_no` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `email`, `phone`, `bmdc_no`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Test Doctor', 'test@telerx.com', '+880123456789', '12345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-01-09 16:41:44', '2026-01-13 13:06:31'),
(2, 'Dr. Fatima Begum', 'dr.begum@telerx.com', '+8801812345678', 'A-12346', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(3, 'Dr. Ahmed Hossain', 'dr.hossain@telerx.com', '+8801912345678', 'A-12347', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(4, 'Md Kamranul', 'kamranislam88@gmail.com', '01771971073', 'CFGG44', '$2y$10$dMzB1cIn2a8xgbZzSh0w2.qy7RCfuLzQnMxsOhXy/2aH2iKhb7TUy', '2026-02-10 10:36:32', '2026-02-10 10:36:32');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability_ranges`
--

CREATE TABLE `doctor_availability_ranges` (
  `id` int UNSIGNED NOT NULL,
  `doctor_id` int UNSIGNED NOT NULL,
  `day_of_week` tinyint UNSIGNED NOT NULL COMMENT '0=Sun, 1=Mon, ..., 6=Sat',
  `start_time` varchar(5) NOT NULL COMMENT 'HH:MM 24h, e.g. 09:00',
  `end_time` varchar(5) NOT NULL COMMENT 'HH:MM 24h, e.g. 12:00 (exclusive) or 12:00 for slots up to 11:45',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_availability_ranges`
--

INSERT INTO `doctor_availability_ranges` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `created_at`) VALUES
(4, 1, 4, '06:00', '12:00', '2026-03-05 02:17:06');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_awards`
--

CREATE TABLE `doctor_awards` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `award_name` varchar(255) NOT NULL,
  `award_year` year DEFAULT NULL,
  `awarded_by` varchar(255) DEFAULT NULL,
  `description` text,
  `award_certificate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_awards`
--

INSERT INTO `doctor_awards` (`id`, `doctor_id`, `award_name`, `award_year`, `awarded_by`, `description`, `award_certificate`, `created_at`, `updated_at`) VALUES
(1, 1, 'Best Cardiologist Award', 2022, 'Bangladesh Medical Association', 'Recognized for excellence in cardiovascular care and patient outcomes', NULL, '2026-01-09 16:41:45', '2026-01-09 16:41:45'),
(2, 1, 'Research Excellence Award', 2020, 'Cardiac Society of Bangladesh', 'Awarded for outstanding research in preventive cardiology', NULL, '2026-01-09 16:41:45', '2026-01-09 16:41:45'),
(3, 2, 'Pediatrician of the Year', 2021, 'Bangladesh Pediatric Association', 'Recognized for exceptional pediatric care and community service', NULL, '2026-01-09 16:41:45', '2026-01-09 16:41:45'),
(4, 3, 'Orthopedic Excellence Award', 2019, 'Orthopedic Society of Bangladesh', 'Awarded for innovative orthopedic surgical techniques', NULL, '2026-01-09 16:41:45', '2026-01-09 16:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_business_hours`
--

CREATE TABLE `doctor_business_hours` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `clinic_id` int DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_business_hours`
--

INSERT INTO `doctor_business_hours` (`id`, `doctor_id`, `clinic_id`, `day_of_week`, `start_time`, `end_time`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Monday', NULL, NULL, 0, '2026-01-09 16:41:45', '2026-02-23 18:45:54'),
(2, 1, 1, 'Tuesday', NULL, NULL, 0, '2026-01-09 16:41:45', '2026-02-23 18:45:54'),
(3, 1, 1, 'Wednesday', NULL, NULL, 0, '2026-01-09 16:41:45', '2026-02-23 18:45:54'),
(4, 1, 1, 'Thursday', NULL, NULL, 0, '2026-01-09 16:41:45', '2026-02-23 18:45:54'),
(5, 1, 1, 'Friday', NULL, NULL, 0, '2026-01-09 16:41:45', '2026-02-23 18:45:54'),
(6, 1, 1, 'Saturday', NULL, NULL, 0, '2026-01-09 16:41:45', '2026-02-23 18:45:54'),
(7, 1, 1, 'Sunday', NULL, NULL, 0, '2026-01-09 16:41:45', '2026-01-09 16:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_clinics`
--

CREATE TABLE `doctor_clinics` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `clinic_name` varchar(255) NOT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `consultation_fee` decimal(8,2) DEFAULT NULL,
  `clinic_logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_clinics`
--

INSERT INTO `doctor_clinics` (`id`, `doctor_id`, `clinic_name`, `address`, `city`, `state`, `zip_code`, `phone`, `email`, `website`, `consultation_fee`, `clinic_logo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Rahman Cardiac Care Center', '123 Dhanmondi Road, Dhanmondi', 'Dhaka', 'Dhaka', '1209', '+8801712345678', 'info@rahman-cardiac.com', 'www.rahman-cardiac.com', '150.00', NULL, '2026-01-09 16:41:45', '2026-01-09 16:41:45'),
(2, 2, 'Begum Children Clinic', '456 Gulshan Avenue, Gulshan-2', 'Dhaka', 'Dhaka', '1212', '+8801812345678', 'info@begum-children.com', 'www.begum-children.com', '120.00', NULL, '2026-01-09 16:41:45', '2026-01-09 16:41:45'),
(3, 3, 'Hossain Orthopedic Clinic', '789 Uttara Sector 7', 'Dhaka', 'Dhaka', '1230', '+8801912345678', 'info@hossain-ortho.com', 'www.hossain-ortho.com', '180.00', NULL, '2026-01-09 16:41:45', '2026-01-09 16:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_education`
--

CREATE TABLE `doctor_education` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `degree` varchar(255) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `year_of_completion` year DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_education`
--

INSERT INTO `doctor_education` (`id`, `doctor_id`, `degree`, `institution`, `year_of_completion`, `grade`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'MBBS', 'Dhaka Medical College', 2004, 'First Class', 'Bachelor of Medicine, Bachelor of Surgery', '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(2, 1, 'MD Cardiology', 'Bangabandhu Sheikh Mujib Medical University', 2009, 'Distinction', 'Doctor of Medicine in Cardiology', '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(3, 2, 'MBBS', 'Sir Salimullah Medical College', 2007, 'First Class', 'Bachelor of Medicine, Bachelor of Surgery', '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(4, 2, 'FCPS Pediatrics', 'Bangladesh College of Physicians and Surgeons', 2012, 'Pass', 'Fellow of College of Physicians and Surgeons in Pediatrics', '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(5, 3, 'MBBS', 'Mymensingh Medical College', 2001, 'First Class', 'Bachelor of Medicine, Bachelor of Surgery', '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(6, 3, 'MS Orthopedics', 'National Institute of Traumatology and Orthopedic Rehabilitation', 2006, 'Distinction', 'Master of Surgery in Orthopedics', '2026-01-09 16:41:44', '2026-01-09 16:41:44');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_experiences`
--

CREATE TABLE `doctor_experiences` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `years_of_experience` varchar(50) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `employment_type` enum('Full Time','Part Time') DEFAULT 'Full Time',
  `job_description` text,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `currently_working` tinyint(1) DEFAULT '0',
  `hospital_logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_experiences`
--

INSERT INTO `doctor_experiences` (`id`, `doctor_id`, `title`, `hospital_name`, `years_of_experience`, `location`, `employment_type`, `job_description`, `start_date`, `end_date`, `currently_working`, `hospital_logo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Senior Cardiologist', 'Square Hospital', '10 years', 'Dhaka, Bangladesh', 'Full Time', 'Lead cardiologist responsible for patient care, cardiac procedures, and team management.', '2014-01-15', NULL, 1, NULL, '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(2, 1, 'Cardiology Resident', 'Bangabandhu Sheikh Mujib Medical University', '5 years', 'Dhaka, Bangladesh', 'Full Time', 'Completed residency training in cardiology with focus on interventional cardiology.', '2009-01-01', '2014-01-14', 0, NULL, '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(3, 2, 'Pediatric Consultant', 'Apollo Hospitals', '8 years', 'Dhaka, Bangladesh', 'Full Time', 'Provided comprehensive pediatric care including neonatal care and developmental assessments.', '2016-03-01', NULL, 1, NULL, '2026-01-09 16:41:44', '2026-01-09 16:41:44'),
(4, 3, 'Orthopedic Surgeon', 'United Hospital', '12 years', 'Dhaka, Bangladesh', 'Full Time', 'Performed complex orthopedic surgeries including joint replacements and arthroscopic procedures.', '2012-06-01', NULL, 1, NULL, '2026-01-09 16:41:44', '2026-01-09 16:41:44');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_insurances`
--

CREATE TABLE `doctor_insurances` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `insurance_name` varchar(255) NOT NULL,
  `insurance_provider` varchar(255) DEFAULT NULL,
  `policy_number` varchar(100) DEFAULT NULL,
  `coverage_amount` decimal(10,2) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_insurances`
--

INSERT INTO `doctor_insurances` (`id`, `doctor_id`, `insurance_name`, `insurance_provider`, `policy_number`, `coverage_amount`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Professional Liability Insurance', 'Green Delta Insurance', 'PLI-2023-001', '500000.00', 'Comprehensive professional liability coverage for medical malpractice', '2026-01-09 16:41:45', '2026-01-09 16:41:45'),
(2, 2, 'Medical Practice Insurance', 'Pragati Insurance', 'MPI-2023-002', '300000.00', 'Coverage for medical practice operations and professional liability', '2026-01-09 16:41:45', '2026-01-09 16:41:45'),
(3, 3, 'Healthcare Professional Insurance', 'Islami Insurance', 'HPI-2023-003', '400000.00', 'Comprehensive coverage for orthopedic practice and surgical procedures', '2026-01-09 16:41:45', '2026-01-09 16:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_profiles`
--

CREATE TABLE `doctor_profiles` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text,
  `specialty` varchar(255) DEFAULT NULL,
  `languages_spoken` varchar(500) DEFAULT NULL,
  `consultation_fee` decimal(8,2) DEFAULT NULL,
  `experience_years` int DEFAULT NULL,
  `total_appointments` int DEFAULT '0',
  `total_reviews` int DEFAULT '0',
  `average_rating` decimal(3,2) DEFAULT '0.00',
  `is_available` tinyint(1) DEFAULT '1',
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gender` varchar(20) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `degrees` varchar(255) DEFAULT NULL,
  `currently_working` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `present_address` text,
  `bmdc_certificate` varchar(255) DEFAULT NULL,
  `nid_card` varchar(255) DEFAULT NULL,
  `degrees_certificate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_profiles`
--

INSERT INTO `doctor_profiles` (`id`, `doctor_id`, `profile_image`, `bio`, `specialty`, `languages_spoken`, `consultation_fee`, `experience_years`, `total_appointments`, `total_reviews`, `average_rating`, `is_available`, `address`, `city`, `district`, `state`, `zip_code`, `created_at`, `updated_at`, `gender`, `account_number`, `degrees`, `currently_working`, `department`, `present_address`, `bmdc_certificate`, `nid_card`, `degrees_certificate`) VALUES
(1, 1, 'assets/img/doctors/doctor_1_1777105116.jpg', 'Test biography', 'General Physician, Pediatrician', 'English, Bengali, Hindi', '150.00', 10, 200, 50, '4.80', 1, '123 Medical Center, Dhanmondi', 'Dhaka', 'Bogra', 'Dhaka', '1209', '2026-01-09 16:41:44', '2026-04-25 08:18:36', 'Male', '1712345678', 'MBBS, MD', 'Test Hospital', '', '123 Medical Center, Dhanmondi', NULL, NULL, NULL),
(2, 2, NULL, 'Pediatrician dedicated to providing comprehensive healthcare for children from infancy through adolescence.', 'Pediatrics', 'English, Bengali', '120.00', 12, 150, 35, '4.90', 1, '456 Children Hospital, Gulshan', 'Dhaka', NULL, 'Dhaka', '1212', '2026-01-09 16:41:44', '2026-01-09 16:41:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 3, NULL, 'Orthopedic surgeon specializing in joint replacement and sports medicine.', 'Orthopedic Surgery', 'English, Bengali, Urdu', '180.00', 18, 300, 75, '4.70', 1, '789 Orthopedic Center, Uttara', 'Dhaka', NULL, 'Dhaka', '1230', '2026-01-09 16:41:44', '2026-01-09 16:41:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `id` int UNSIGNED NOT NULL,
  `doctor_id` int UNSIGNED NOT NULL,
  `slot_date` date NOT NULL,
  `slot_time` varchar(5) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_schedule`
--

INSERT INTO `doctor_schedule` (`id`, `doctor_id`, `slot_date`, `slot_time`, `created_at`) VALUES
(97, 1, '2026-02-27', '06:00', '2026-02-27 09:47:39'),
(98, 1, '2026-02-27', '06:15', '2026-02-27 09:47:40'),
(99, 1, '2026-02-27', '06:30', '2026-02-27 09:47:40'),
(100, 1, '2026-02-27', '06:45', '2026-02-27 09:47:40'),
(101, 1, '2026-02-27', '07:00', '2026-02-27 09:47:40'),
(102, 1, '2026-02-27', '07:15', '2026-02-27 09:47:40'),
(103, 1, '2026-02-27', '07:30', '2026-02-27 09:47:40'),
(104, 1, '2026-02-27', '07:45', '2026-02-27 09:47:40'),
(105, 1, '2026-02-27', '08:00', '2026-02-27 09:47:40'),
(106, 1, '2026-02-27', '08:15', '2026-02-27 09:47:40'),
(107, 1, '2026-02-27', '08:30', '2026-02-27 09:47:40'),
(108, 1, '2026-02-27', '08:45', '2026-02-27 09:47:40'),
(109, 1, '2026-02-27', '09:00', '2026-02-27 09:47:40'),
(110, 1, '2026-02-27', '09:15', '2026-02-27 09:47:40'),
(111, 1, '2026-02-27', '09:30', '2026-02-27 09:47:40'),
(112, 1, '2026-02-27', '09:45', '2026-02-27 09:47:40'),
(113, 1, '2026-02-27', '10:00', '2026-02-27 09:47:40'),
(114, 1, '2026-02-27', '10:15', '2026-02-27 09:47:40'),
(115, 1, '2026-02-27', '10:30', '2026-02-27 09:47:40'),
(116, 1, '2026-02-27', '10:45', '2026-02-27 09:47:40'),
(117, 1, '2026-02-27', '11:00', '2026-02-27 09:47:40'),
(118, 1, '2026-02-27', '11:15', '2026-02-27 09:47:40'),
(119, 1, '2026-02-27', '11:30', '2026-02-27 09:47:40'),
(120, 1, '2026-02-27', '11:45', '2026-02-27 09:47:40'),
(121, 1, '2026-02-27', '12:00', '2026-02-27 09:47:40'),
(122, 1, '2026-02-27', '12:15', '2026-02-27 09:47:40'),
(123, 1, '2026-02-27', '12:30', '2026-02-27 09:47:40'),
(124, 1, '2026-02-27', '12:45', '2026-02-27 09:47:40'),
(125, 1, '2026-02-27', '13:00', '2026-02-27 09:47:40'),
(126, 1, '2026-02-27', '13:15', '2026-02-27 09:47:40'),
(127, 1, '2026-02-27', '13:30', '2026-02-27 09:47:40'),
(128, 1, '2026-02-27', '13:45', '2026-02-27 09:47:40'),
(129, 1, '2026-02-27', '14:00', '2026-02-27 09:47:40'),
(130, 1, '2026-02-27', '14:15', '2026-02-27 09:47:40'),
(131, 1, '2026-02-27', '14:30', '2026-02-27 09:47:40'),
(132, 1, '2026-02-27', '14:45', '2026-02-27 09:47:40'),
(133, 1, '2026-02-27', '15:00', '2026-02-27 09:47:40'),
(134, 1, '2026-02-27', '15:15', '2026-02-27 09:47:40'),
(135, 1, '2026-02-27', '15:30', '2026-02-27 09:47:40'),
(136, 1, '2026-02-27', '15:45', '2026-02-27 09:47:40'),
(137, 1, '2026-02-27', '16:00', '2026-02-27 09:47:40'),
(138, 1, '2026-02-27', '16:15', '2026-02-27 09:47:40'),
(139, 1, '2026-02-27', '16:30', '2026-02-27 09:47:40'),
(140, 1, '2026-02-27', '16:45', '2026-02-27 09:47:40'),
(141, 1, '2026-02-27', '17:00', '2026-02-27 09:47:40'),
(142, 1, '2026-02-27', '17:15', '2026-02-27 09:47:40'),
(143, 1, '2026-02-27', '17:30', '2026-02-27 09:47:40'),
(144, 1, '2026-02-27', '17:45', '2026-02-27 09:47:40'),
(145, 1, '2026-03-02', '06:00', '2026-03-02 10:11:15'),
(146, 1, '2026-03-02', '06:15', '2026-03-02 10:11:15'),
(147, 1, '2026-03-02', '06:30', '2026-03-02 10:11:15'),
(148, 1, '2026-03-02', '06:45', '2026-03-02 10:11:15'),
(149, 1, '2026-03-02', '07:00', '2026-03-02 10:11:15'),
(150, 1, '2026-03-02', '07:15', '2026-03-02 10:11:15'),
(151, 1, '2026-03-02', '07:30', '2026-03-02 10:11:15'),
(152, 1, '2026-03-02', '07:45', '2026-03-02 10:11:15'),
(153, 1, '2026-03-02', '08:00', '2026-03-02 10:11:15'),
(154, 1, '2026-03-02', '08:15', '2026-03-02 10:11:15'),
(155, 1, '2026-03-02', '08:30', '2026-03-02 10:11:15'),
(156, 1, '2026-03-02', '08:45', '2026-03-02 10:11:15'),
(157, 1, '2026-03-02', '09:00', '2026-03-02 10:11:15'),
(158, 1, '2026-03-02', '09:15', '2026-03-02 10:11:15'),
(159, 1, '2026-03-02', '09:30', '2026-03-02 10:11:15'),
(160, 1, '2026-03-02', '09:45', '2026-03-02 10:11:15'),
(161, 1, '2026-03-02', '10:00', '2026-03-02 10:11:15'),
(162, 1, '2026-03-02', '10:15', '2026-03-02 10:11:15'),
(163, 1, '2026-03-02', '10:30', '2026-03-02 10:11:15'),
(164, 1, '2026-03-02', '10:45', '2026-03-02 10:11:15'),
(165, 1, '2026-03-02', '11:00', '2026-03-02 10:11:15'),
(166, 1, '2026-03-02', '11:15', '2026-03-02 10:11:15'),
(167, 1, '2026-03-02', '11:30', '2026-03-02 10:11:15'),
(168, 1, '2026-03-02', '11:45', '2026-03-02 10:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_sessions`
--

CREATE TABLE `doctor_sessions` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_sessions`
--

INSERT INTO `doctor_sessions` (`id`, `doctor_id`, `session_token`, `ip_address`, `user_agent`, `expires_at`, `created_at`) VALUES
(1, 1, '6e9937d48b1b980f9e11aee3dbb5217b956defbfaf409b023047d9e48da35cd6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-08 22:43:11', '2026-01-09 16:43:11'),
(2, 1, '2e820cae249399eedc5c1ff061f4f52e9011d9e3fc1b9bad0e8ab98a9a444190', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-08 22:48:08', '2026-01-09 16:48:08'),
(3, 1, '3e096ef442c74052b27234b26ebfbcebe4905df5873b19cf11647536203df549', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-08 23:01:21', '2026-01-09 17:01:21'),
(4, 1, 'e6d4fbc64c62c26038d26ef834af9566e9788368d59b6745d58612a319f0da42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-08 23:09:55', '2026-01-09 17:09:55'),
(5, 1, 'bbd7a96cde456191b3a4bae06f3cda091f871c71a2057665ec3f805e5074ff7c', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-08 23:10:41', '2026-01-09 17:10:41'),
(6, 1, '1a42abacbdeb061258b7adf56984bb5950f39d241a18fb82014bb584cb5263bb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-08 23:26:53', '2026-01-09 17:26:53'),
(7, 1, '57f06e895c5e9176ab9d421820db0cf86c49c1f63af7db57f9d10af081931321', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-09 22:21:34', '2026-01-10 16:21:34'),
(8, 1, '942ed1fef9380d495cb89f83d286d6f6d2006a946d08d2d342e415f86d3125ef', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 00:21:42', '2026-01-10 18:21:42'),
(9, 1, '2fc016e0b0d218a94d8076f39e3c9c38eb4e716f846d0bdaa053a2c2882680d8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 00:30:32', '2026-01-10 18:30:32'),
(10, 1, 'eaec6ef5786298301b041e628af7f7d54f5dbbe437096cd86b0c1b0b2e724062', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 00:33:54', '2026-01-10 18:33:54'),
(11, 1, '89e2aac1bfe34ff60f2edf768a4cc7841afb91a79a91d631201214108517c642', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 00:41:16', '2026-01-10 18:41:16'),
(12, 1, '60ef47f5e92818d7e6f6fe2de4a20b4db200730a6263a0ccfbac7750b13fe185', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-10 00:43:21', '2026-01-10 18:43:21'),
(13, 1, 'ff2a3868fd3dfac74e05e4e1e46ba5b853001b18c73f84424ded426d32979552', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-12 16:06:33', '2026-01-13 10:06:33'),
(14, 1, 'ec15a86e4cc5b6f22b748af7fac1c6d822e8d6867837c5dd5fccceb2ef08372d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-12 18:29:47', '2026-01-13 12:29:47'),
(15, 1, '2929e008d1724baec0a80ab2066de759f212364ab27896d69ba0817606a3ca79', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-12 19:02:42', '2026-01-13 13:02:42'),
(16, 1, '7aa8748020cee23b4bbee8ab9406c9ef6c36a56bc39ad975f7382b62fff856eb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-12 19:17:20', '2026-01-13 13:17:20'),
(17, 1, 'cface1e01cc9b10e338bdf31bf59c5ef197b9e0174e52e2cff8a9c3999821aeb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 11:58:15', '2026-01-24 05:58:15'),
(18, 1, 'adae7dc327b6bbc44a59cde5f71c0d3c79405e7b91d4aec558b7ff2c53ced361', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 12:07:44', '2026-01-24 06:07:44'),
(19, 1, '255b12c4213d59d9ab39fad3f8ca22fe7764ccb70579390572e7a7cf7eb7d075', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 12:09:40', '2026-01-24 06:09:40'),
(20, 1, '68174c62be069d7a88483c7174474ba4810e024344c47d164cb0d9a14e3a34ae', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 12:10:35', '2026-01-24 06:10:35'),
(21, 1, '4550fd9e0f07b975db7febda8e34ce36804f979d6323a56618f1ed069dc4bfc8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 12:16:13', '2026-01-24 06:16:13'),
(22, 1, '765d107a69e9363fbb57885aa6d1badbf15d519435efec19d0cf2f6a7115cdab', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 12:22:45', '2026-01-24 06:22:45'),
(23, 1, '2dfb429c5c64c786a292809fa97f5da03afe6076461702af5699c3d36af34dda', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 12:24:53', '2026-01-24 06:24:53'),
(24, 1, '0461b9fa08bcb076810dc89bbc55c7f24070b70a1ffe7f7c31c8b75bb002fce3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-23 13:41:42', '2026-01-24 07:41:42'),
(25, 1, '35f3bb84d8d6fbb404f141f214a53f037c21f4e6270f61147d009f6c1cdff859', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-25 10:07:20', '2026-01-26 04:07:20'),
(28, 1, '4ecb650e86fc29515d67e6b7ef77cd3f52bc6927b8a729ac67dd43be33e91979', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-26 16:38:56', '2026-01-27 10:38:56'),
(30, 1, '5ec71b2476920bca03d5689e54d988ed828f57e2884a9b74efadbe4593f547de', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-11 02:20:48', '2026-02-08 20:20:48'),
(32, 1, '8aeb43f3883a3743324f919597a84d48efcb12cd36e1b097a54ed7cbe42580a3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-11 02:23:34', '2026-02-08 20:23:34'),
(33, 1, 'f99cbc92f4301e8e6a98d9943c6f6a8203556b4730fe5a74b6ea514cec97cfb4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-11 02:32:05', '2026-02-08 20:32:05'),
(34, 1, '347e2c0b147e19dfeafbb56c757fb8c571b5b32fd3c730d5e5fa479aa5947ccc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-11 02:44:39', '2026-02-08 20:44:39'),
(35, 1, '6d0f4f9e669125eed2d3694a80be95d2be4b073d0a7bc069dc0bd7f092e25025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-12 15:23:20', '2026-02-10 09:23:20'),
(36, 1, 'd698380c54e2ba67d4c5ff50cf5222fdc503df1b01c08caef183e860469025ee', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-12 15:27:27', '2026-02-10 09:27:27'),
(37, 1, 'c55edcdceb7a919ad82d1c46e5bbdbbcd57d6355f37edba3b6c4de8d7abc7718', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-12 15:57:42', '2026-02-10 09:57:42'),
(38, 1, 'a154897bc24a975348021fdd4306863c0679aedfb8278bc84deb012bf77cb480', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-12 16:31:48', '2026-02-10 10:31:48'),
(44, 1, '9dfe5b8a4c513331f4d34397a57fe72289a4e0f891f0d2dac98f62c5c89a2a70', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-25 23:13:59', '2026-02-23 17:13:59'),
(48, 1, '0458ce00de0f17ea4f8ad2fb44e7b2b2f58434c6a8d27020105c95f5e87a3a13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-28 00:01:59', '2026-02-25 18:01:59'),
(49, 1, 'ea1405166e7e061ff20fb364a2641f73238ba8509cf30d99748f957fca22bc7f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-28 00:36:12', '2026-02-25 18:36:12'),
(50, 1, 'c3c6ecb1093211d2ebacf7b4b6a91e728595fca18b4d3dc7245598422e79cb92', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-28 00:36:19', '2026-02-25 18:36:19'),
(73, 1, '242d5008358285170d936dcac1534b7f238dfa4f97cfadf0d2e0ebc5e950b4bf', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-05-06 15:27:18', '2026-04-06 09:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `healthcare_providers`
--

CREATE TABLE `healthcare_providers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nid_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `tid` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `healthcare_providers`
--

INSERT INTO `healthcare_providers` (`id`, `name`, `email`, `nid_number`, `password`, `phone`, `tid`, `created_at`, `updated_at`) VALUES
(1, 'Md Kamranul Islam', 'kamranislam81@gmail.com', '1234567891', '$2y$10$2hUXUFT0EXpUBbCxiwFyDu.a.oH.t4R3t/IuvhHhIJfqzrVl6.emG', '01771971071', 'TEL-20260125-0001', '2026-01-25 02:33:55', '2026-01-25 02:33:55'),
(2, 'nurjahan', 'islam8@gmail.com', '1234567910', '$2y$10$5a.6e4Qr9eSS4gn7Tq5W4uoKuowrbynGnFxYQt4ojq9sqAST6a0XO', '01771971079', 'T1001', '2026-01-27 06:23:35', '2026-01-27 10:36:39');

-- --------------------------------------------------------

--
-- Table structure for table `healthcare_providers_profiles`
--

CREATE TABLE `healthcare_providers_profiles` (
  `id` int NOT NULL,
  `healthcare_provider_id` int NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text,
  `gender` varchar(20) DEFAULT NULL,
  `degrees` varchar(255) DEFAULT NULL,
  `currently_working` varchar(255) DEFAULT NULL,
  `present_address` text,
  `nid_file` varchar(255) DEFAULT NULL,
  `degrees_certificate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `family_members` json DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `healthcare_providers_profiles`
--

INSERT INTO `healthcare_providers_profiles` (`id`, `healthcare_provider_id`, `profile_image`, `bio`, `gender`, `degrees`, `currently_working`, `present_address`, `nid_file`, `degrees_certificate`, `created_at`, `updated_at`, `family_members`, `district`) VALUES
(1, 1, '', NULL, '', '', '', '', '', '', '2026-01-27 06:22:16', '2026-01-27 06:22:16', '[{\"nid\": \"12345678\", \"name\": \"Md Kamranul Islam\", \"relation\": \"Father\"}]', NULL),
(2, 2, '', NULL, '', 'MBBS, MD', 'Test Hospital', 'Nikunjo 2, Khilkhet, Dhaka', '', '', '2026-01-27 06:24:30', '2026-01-27 10:36:39', '[{\"nid\": \"12345678\", \"name\": \"khairul\", \"relation\": \"Father\"}, {\"nid\": \"123456789\", \"name\": \"nurjahan\", \"relation\": \"Mother\"}]', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Bangladesh',
  `pincode` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `email`, `phone`, `password`, `gender`, `date_of_birth`, `blood_group`, `profile_image`, `address`, `city`, `state`, `country`, `pincode`, `created_at`, `updated_at`) VALUES
(1, 'Test Patient', 'testpatient@example.com', '01712345678', '$2y$10$HCGKz6O0YTtO.qYRCiMQN.jNgFvnRByvHlRJTg/3PIlCeRk9l7lUe', NULL, NULL, NULL, 'assets/img/patients/patient_1_1777107714.jpg', NULL, NULL, NULL, 'Bangladesh', NULL, '2026-02-17 13:04:20', '2026-04-25 09:01:54');

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
  ADD KEY `idx_patient_date` (`patient_id`,`appointment_date`);

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
  ADD KEY `idx_doctors_bmdc_no` (`bmdc_no`),
  ADD KEY `idx_doctors_phone` (`phone`);

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
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_business_hours`
--
ALTER TABLE `doctor_business_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `clinic_id` (`clinic_id`);

--
-- Indexes for table `doctor_clinics`
--
ALTER TABLE `doctor_clinics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_education`
--
ALTER TABLE `doctor_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_experiences`
--
ALTER TABLE `doctor_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_insurances`
--
ALTER TABLE `doctor_insurances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_doctor_date_time` (`doctor_id`,`slot_date`,`slot_time`),
  ADD KEY `idx_doctor_date` (`doctor_id`,`slot_date`);

--
-- Indexes for table `doctor_sessions`
--
ALTER TABLE `doctor_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `idx_doctor_sessions_token` (`session_token`),
  ADD KEY `idx_doctor_sessions_expires` (`expires_at`);

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
  ADD KEY `idx_patients_phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_availability_ranges`
--
ALTER TABLE `doctor_availability_ranges`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_awards`
--
ALTER TABLE `doctor_awards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_business_hours`
--
ALTER TABLE `doctor_business_hours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `doctor_clinics`
--
ALTER TABLE `doctor_clinics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_education`
--
ALTER TABLE `doctor_education`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctor_experiences`
--
ALTER TABLE `doctor_experiences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_insurances`
--
ALTER TABLE `doctor_insurances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_profiles`
--
ALTER TABLE `doctor_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `doctor_sessions`
--
ALTER TABLE `doctor_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `healthcare_providers`
--
ALTER TABLE `healthcare_providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `healthcare_providers_profiles`
--
ALTER TABLE `healthcare_providers_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
