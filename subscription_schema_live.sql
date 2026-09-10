-- ==========================================================
-- TeleRx Live Database Migration: Subscription Packages
-- Target Database: telerx_db / telerxb2_telerx_db
-- ==========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Table structure for `subscription_plans`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `subscription_plans` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_code` varchar(50) NOT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `billing_cycle` enum('monthly','sixmonths','yearly') NOT NULL,
  `duration_label` varchar(50) NOT NULL,
  `duration_months` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gp_discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `specialist_discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `emergency_call_quota` int(11) NOT NULL DEFAULT 0,
  `home_service_discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `purchase_discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `lab_test_discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `family_member_quota` int(11) NOT NULL DEFAULT 0,
  `digital_prescription` tinyint(1) NOT NULL DEFAULT 1,
  `priority_response` tinyint(1) NOT NULL DEFAULT 0,
  `dedicated_coordinator` tinyint(1) NOT NULL DEFAULT 0,
  `health_review_type` varchar(100) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Seed Subscription Plans (Matching subscription.php)
-- --------------------------------------------------------

INSERT INTO `subscription_plans` 
(`plan_code`, `name`, `badge`, `billing_cycle`, `duration_label`, `duration_months`, `price`, `gp_discount_percent`, `specialist_discount_percent`, `emergency_call_quota`, `home_service_discount_percent`, `purchase_discount_percent`, `lab_test_discount_percent`, `family_member_quota`, `digital_prescription`, `priority_response`, `dedicated_coordinator`, `health_review_type`, `is_featured`, `is_active`)
VALUES
-- Monthly Plans
('monthly_basic', 'Basic', 'Occasional Users', 'monthly', '1 Month', 1, 499.00, 5.00, 5.00, 10, 10.00, 7.00, 0.00, 1, 1, 0, 0, 'Monthly basic health follow-up', 0, 1),
('monthly_standard', 'Standard', 'Best Value', 'monthly', '1 Month', 1, 999.00, 7.00, 7.00, 25, 15.00, 10.00, 0.00, 2, 1, 0, 0, 'Annual health review included', 1, 1),
('monthly_premium', 'Premium', 'Recommended', 'monthly', '1 Month', 1, 1499.00, 12.00, 12.00, 40, 30.00, 12.00, 15.00, 4, 1, 1, 1, 'Personal health history management', 0, 1),

-- 6 Months Plans
('sixmonths_basic', 'Basic 6 Months', 'Basic', 'sixmonths', '6 Months', 6, 2499.00, 5.00, 5.00, 70, 10.00, 7.00, 0.00, 1, 1, 0, 0, 'Monthly basic health follow-up', 0, 1),
('sixmonths_standard', 'Standard 6 Months', 'Best Value', 'sixmonths', '6 Months', 6, 5499.00, 7.00, 7.00, 150, 15.00, 10.00, 0.00, 2, 1, 0, 0, 'Mid-term health review included', 1, 1),
('sixmonths_premium', 'Premium 6 Months', 'Maximum Support', 'sixmonths', '6 Months', 6, 7999.00, 12.00, 12.00, 225, 30.00, 12.00, 15.00, 4, 1, 1, 1, 'Comprehensive health review included', 0, 1),

-- 12 Months Plans
('yearly_basic', 'Basic 12 Months', 'Basic', 'yearly', '12 Months', 12, 4499.00, 5.00, 5.00, 120, 10.00, 7.00, 0.00, 1, 1, 0, 0, 'Monthly basic health follow-up', 0, 1),
('yearly_standard', 'Standard 12 Months', 'Best Value', 'yearly', '12 Months', 12, 9999.00, 7.00, 7.00, 250, 15.00, 10.00, 0.00, 2, 1, 0, 0, 'Biannual health review included', 1, 1),
('yearly_premium', 'Premium 12 Months', 'Recommended', 'yearly', '12 Months', 12, 14499.00, 12.00, 12.00, 400, 30.00, 12.00, 15.00, 4, 1, 1, 1, 'Comprehensive annual health review', 0, 1)
ON DUPLICATE KEY UPDATE 
`price` = VALUES(`price`),
`emergency_call_quota` = VALUES(`emergency_call_quota`),
`gp_discount_percent` = VALUES(`gp_discount_percent`),
`specialist_discount_percent` = VALUES(`specialist_discount_percent`),
`family_member_quota` = VALUES(`family_member_quota`);

-- --------------------------------------------------------
-- 3. Table structure for `patient_subscriptions`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `patient_subscriptions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'bkash',
  `transaction_id` varchar(100) NOT NULL,
  `status` enum('pending','active','expired','cancelled') NOT NULL DEFAULT 'active',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `emergency_calls_total` int(11) NOT NULL DEFAULT 0,
  `emergency_calls_used` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_patient_status` (`patient_id`, `status`),
  KEY `idx_end_date` (`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Table structure for `subscription_family_members`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `subscription_family_members` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `member_name` varchar(150) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `age` varchar(10) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sub_id` (`subscription_id`),
  KEY `idx_patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Optional enhancement to `appointments` table (if not exists)
-- --------------------------------------------------------
-- Check and add `subscription_id` to appointments to link subscription usage
SET @dbname = DATABASE();
SET @tablename = "appointments";
SET @columnname = "subscription_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE appointments ADD COLUMN subscription_id INT(10) UNSIGNED DEFAULT NULL AFTER referrer_tid"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
