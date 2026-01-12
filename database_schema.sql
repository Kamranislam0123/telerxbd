-- TeleRx Bangladesh Database Schema
-- This script creates the database structure for the TeleRx healthcare platform
-- Run this script to set up the database from scratch

-- Drop existing tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS doctor_sessions;
DROP TABLE IF EXISTS healthcare_providers;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS doctors;

-- Create patients table
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create doctors table
CREATE TABLE doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    bmdc_no VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create healthcare_providers table
CREATE TABLE healthcare_providers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    nid_number VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create doctor_sessions table for session management
CREATE TABLE doctor_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create doctor_experiences table
CREATE TABLE doctor_experiences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    title VARCHAR(255),
    hospital_name VARCHAR(255) NOT NULL,
    years_of_experience VARCHAR(50),
    location VARCHAR(255) NOT NULL,
    employment_type ENUM('Full Time', 'Part Time') DEFAULT 'Full Time',
    job_description TEXT,
    start_date DATE,
    end_date DATE,
    currently_working BOOLEAN DEFAULT FALSE,
    hospital_logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create doctor_education table
CREATE TABLE doctor_education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    degree VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    year_of_completion YEAR,
    grade VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create doctor_awards table
CREATE TABLE doctor_awards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    award_name VARCHAR(255) NOT NULL,
    award_year YEAR,
    awarded_by VARCHAR(255),
    description TEXT,
    award_certificate VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create doctor_insurances table
CREATE TABLE doctor_insurances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    insurance_name VARCHAR(255) NOT NULL,
    insurance_provider VARCHAR(255),
    policy_number VARCHAR(100),
    coverage_amount DECIMAL(10,2),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create doctor_clinics table
CREATE TABLE doctor_clinics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    clinic_name VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    phone VARCHAR(20),
    email VARCHAR(255),
    website VARCHAR(255),
    consultation_fee DECIMAL(8,2),
    clinic_logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create doctor_business_hours table
CREATE TABLE doctor_business_hours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    clinic_id INT,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
    start_time TIME,
    end_time TIME,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES doctor_clinics(id) ON DELETE CASCADE
);

-- Create doctor_profiles table for additional profile information
CREATE TABLE doctor_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT UNIQUE NOT NULL,
    profile_image VARCHAR(255),
    bio TEXT,
    specialty VARCHAR(255),
    languages_spoken VARCHAR(500),
    consultation_fee DECIMAL(8,2),
    experience_years INT,
    total_appointments INT DEFAULT 0,
    total_reviews INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    is_available BOOLEAN DEFAULT TRUE,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_patients_email ON patients(email);
CREATE INDEX idx_doctors_email ON doctors(email);
CREATE INDEX idx_doctors_bmdc_no ON doctors(bmdc_no);
CREATE INDEX idx_doctors_phone ON doctors(phone);
CREATE INDEX idx_healthcare_providers_email ON healthcare_providers(email);
CREATE INDEX idx_healthcare_providers_nid ON healthcare_providers(nid_number);
CREATE INDEX idx_doctor_sessions_token ON doctor_sessions(session_token);
CREATE INDEX idx_doctor_sessions_expires ON doctor_sessions(expires_at);

-- Insert sample doctor data for testing (optional)
INSERT INTO doctors (name, email, phone, bmdc_no, password) VALUES
('Dr. Mohammad Rahman', 'dr.rahman@telerx.com', '+8801712345678', 'A-12345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
('Dr. Fatima Begum', 'dr.begum@telerx.com', '+8801812345678', 'A-12346', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
('Dr. Ahmed Hossain', 'dr.hossain@telerx.com', '+8801912345678', 'A-12347', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Insert sample doctor profiles
INSERT INTO doctor_profiles (doctor_id, bio, specialty, languages_spoken, consultation_fee, experience_years, total_appointments, total_reviews, average_rating, is_available, address, city, state, zip_code) VALUES
(1, 'Experienced cardiologist with over 15 years of practice. Specializes in cardiovascular diseases and preventive cardiology.', 'Cardiology', 'English, Bengali, Hindi', 150.00, 15, 200, 50, 4.8, TRUE, '123 Medical Center, Dhanmondi', 'Dhaka', 'Dhaka', '1209'),
(2, 'Pediatrician dedicated to providing comprehensive healthcare for children from infancy through adolescence.', 'Pediatrics', 'English, Bengali', 120.00, 12, 150, 35, 4.9, TRUE, '456 Children Hospital, Gulshan', 'Dhaka', 'Dhaka', '1212'),
(3, 'Orthopedic surgeon specializing in joint replacement and sports medicine.', 'Orthopedic Surgery', 'English, Bengali, Urdu', 180.00, 18, 300, 75, 4.7, TRUE, '789 Orthopedic Center, Uttara', 'Dhaka', 'Dhaka', '1230');

-- Insert sample doctor experiences
INSERT INTO doctor_experiences (doctor_id, title, hospital_name, years_of_experience, location, employment_type, job_description, start_date, end_date, currently_working) VALUES
(1, 'Senior Cardiologist', 'Square Hospital', '10 years', 'Dhaka, Bangladesh', 'Full Time', 'Lead cardiologist responsible for patient care, cardiac procedures, and team management.', '2014-01-15', NULL, TRUE),
(1, 'Cardiology Resident', 'Bangabandhu Sheikh Mujib Medical University', '5 years', 'Dhaka, Bangladesh', 'Full Time', 'Completed residency training in cardiology with focus on interventional cardiology.', '2009-01-01', '2014-01-14', FALSE),
(2, 'Pediatric Consultant', 'Apollo Hospitals', '8 years', 'Dhaka, Bangladesh', 'Full Time', 'Provided comprehensive pediatric care including neonatal care and developmental assessments.', '2016-03-01', NULL, TRUE),
(3, 'Orthopedic Surgeon', 'United Hospital', '12 years', 'Dhaka, Bangladesh', 'Full Time', 'Performed complex orthopedic surgeries including joint replacements and arthroscopic procedures.', '2012-06-01', NULL, TRUE);

-- Insert sample doctor education
INSERT INTO doctor_education (doctor_id, degree, institution, year_of_completion, grade, description) VALUES
(1, 'MBBS', 'Dhaka Medical College', 2004, 'First Class', 'Bachelor of Medicine, Bachelor of Surgery'),
(1, 'MD Cardiology', 'Bangabandhu Sheikh Mujib Medical University', 2009, 'Distinction', 'Doctor of Medicine in Cardiology'),
(2, 'MBBS', 'Sir Salimullah Medical College', 2007, 'First Class', 'Bachelor of Medicine, Bachelor of Surgery'),
(2, 'FCPS Pediatrics', 'Bangladesh College of Physicians and Surgeons', 2012, 'Pass', 'Fellow of College of Physicians and Surgeons in Pediatrics'),
(3, 'MBBS', 'Mymensingh Medical College', 2001, 'First Class', 'Bachelor of Medicine, Bachelor of Surgery'),
(3, 'MS Orthopedics', 'National Institute of Traumatology and Orthopedic Rehabilitation', 2006, 'Distinction', 'Master of Surgery in Orthopedics');

-- Insert sample doctor awards
INSERT INTO doctor_awards (doctor_id, award_name, award_year, awarded_by, description) VALUES
(1, 'Best Cardiologist Award', 2022, 'Bangladesh Medical Association', 'Recognized for excellence in cardiovascular care and patient outcomes'),
(1, 'Research Excellence Award', 2020, 'Cardiac Society of Bangladesh', 'Awarded for outstanding research in preventive cardiology'),
(2, 'Pediatrician of the Year', 2021, 'Bangladesh Pediatric Association', 'Recognized for exceptional pediatric care and community service'),
(3, 'Orthopedic Excellence Award', 2019, 'Orthopedic Society of Bangladesh', 'Awarded for innovative orthopedic surgical techniques');

-- Insert sample doctor insurances
INSERT INTO doctor_insurances (doctor_id, insurance_name, insurance_provider, policy_number, coverage_amount, description) VALUES
(1, 'Professional Liability Insurance', 'Green Delta Insurance', 'PLI-2023-001', 500000.00, 'Comprehensive professional liability coverage for medical malpractice'),
(2, 'Medical Practice Insurance', 'Pragati Insurance', 'MPI-2023-002', 300000.00, 'Coverage for medical practice operations and professional liability'),
(3, 'Healthcare Professional Insurance', 'Islami Insurance', 'HPI-2023-003', 400000.00, 'Comprehensive coverage for orthopedic practice and surgical procedures');

-- Insert sample doctor clinics
INSERT INTO doctor_clinics (doctor_id, clinic_name, address, city, state, zip_code, phone, email, website, consultation_fee) VALUES
(1, 'Rahman Cardiac Care Center', '123 Dhanmondi Road, Dhanmondi', 'Dhaka', 'Dhaka', '1209', '+8801712345678', 'info@rahman-cardiac.com', 'www.rahman-cardiac.com', 150.00),
(2, 'Begum Children Clinic', '456 Gulshan Avenue, Gulshan-2', 'Dhaka', 'Dhaka', '1212', '+8801812345678', 'info@begum-children.com', 'www.begum-children.com', 120.00),
(3, 'Hossain Orthopedic Clinic', '789 Uttara Sector 7', 'Dhaka', 'Dhaka', '1230', '+8801912345678', 'info@hossain-ortho.com', 'www.hossain-ortho.com', 180.00);

-- Insert sample business hours
INSERT INTO doctor_business_hours (doctor_id, clinic_id, day_of_week, start_time, end_time, is_available) VALUES
(1, 1, 'Monday', '09:00:00', '17:00:00', TRUE),
(1, 1, 'Tuesday', '09:00:00', '17:00:00', TRUE),
(1, 1, 'Wednesday', '09:00:00', '17:00:00', TRUE),
(1, 1, 'Thursday', '09:00:00', '17:00:00', TRUE),
(1, 1, 'Friday', '09:00:00', '14:00:00', TRUE),
(1, 1, 'Saturday', '10:00:00', '16:00:00', TRUE),
(1, 1, 'Sunday', NULL, NULL, FALSE);

-- Insert sample patient data for testing (optional)
INSERT INTO patients (name, email, password) VALUES
('John Doe', 'patient1@telerx.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
('Jane Smith', 'patient2@telerx.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Insert sample healthcare provider data for testing (optional)
INSERT INTO healthcare_providers (name, email, nid_number, password) VALUES
('Healthcare Provider One', 'healthcare1@telerx.com', '12345678901234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
('Healthcare Provider Two', 'healthcare2@telerx.com', '12345678901234568', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password
