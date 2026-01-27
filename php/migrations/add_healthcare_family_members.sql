-- Add family_members JSON column to healthcare_providers_profiles
-- Run this once: php/migrations/add_healthcare_family_members.sql
-- Example: mysql -u root -p telerx_db < php/migrations/add_healthcare_family_members.sql

ALTER TABLE healthcare_providers_profiles
ADD COLUMN family_members JSON DEFAULT NULL;
