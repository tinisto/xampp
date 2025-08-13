-- Migration: Standardize Field Names Across Database
-- Created: 2025-08-04
-- Description: Standardizes primary keys to 'id' and foreign keys to 'table_id' format

-- IMPORTANT: BACKUP YOUR DATABASE BEFORE RUNNING THIS MIGRATION!
-- This migration makes significant changes to table structures

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. POSTS TABLE
-- ============================================
-- Rename primary key from id_post to id
ALTER TABLE posts CHANGE COLUMN id_post id INT(11) NOT NULL AUTO_INCREMENT;

-- ============================================
-- 2. NEWS TABLE
-- ============================================
-- Rename primary key from id_news to id
ALTER TABLE news CHANGE COLUMN id_news id INT(5) NOT NULL AUTO_INCREMENT;

-- Rename foreign keys to standard format
ALTER TABLE news CHANGE COLUMN id_vpo vpo_id INT(11);
ALTER TABLE news CHANGE COLUMN id_spo spo_id INT(11);
ALTER TABLE news CHANGE COLUMN id_school school_id INT(11);

-- ============================================
-- 3. VPO TABLE
-- ============================================
-- Rename primary key from id_vpo to id
ALTER TABLE vpo CHANGE COLUMN id_vpo id INT(11) NOT NULL AUTO_INCREMENT;

-- Rename foreign keys to standard format
ALTER TABLE vpo CHANGE COLUMN id_region region_id INT(11);
ALTER TABLE vpo CHANGE COLUMN id_town town_id INT(11);
ALTER TABLE vpo CHANGE COLUMN id_area area_id INT(11);
ALTER TABLE vpo CHANGE COLUMN id_country country_id INT(11);

-- ============================================
-- 4. SPO TABLE
-- ============================================
-- Rename primary key from id_spo to id
ALTER TABLE spo CHANGE COLUMN id_spo id INT(11) NOT NULL AUTO_INCREMENT;

-- Rename foreign keys to standard format
ALTER TABLE spo CHANGE COLUMN id_region region_id INT(11);
ALTER TABLE spo CHANGE COLUMN id_town town_id INT(11);
ALTER TABLE spo CHANGE COLUMN id_area area_id INT(11);
ALTER TABLE spo CHANGE COLUMN id_country country_id INT(11);

-- ============================================
-- 5. SCHOOLS TABLE
-- ============================================
-- Rename primary key from id_school to id
ALTER TABLE schools CHANGE COLUMN id_school id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- Rename foreign keys to standard format
ALTER TABLE schools CHANGE COLUMN id_region region_id INT(11);
ALTER TABLE schools CHANGE COLUMN id_town town_id INT(11);
ALTER TABLE schools CHANGE COLUMN id_area area_id INT(11);
ALTER TABLE schools CHANGE COLUMN id_country country_id INT(11);
ALTER TABLE schools CHANGE COLUMN id_rono rono_id INT(11);
ALTER TABLE schools CHANGE COLUMN id_indeks indeks_id INT(11);

-- ============================================
-- 6. COMMENTS TABLE - Update foreign key references
-- ============================================
-- Since we're changing primary keys in other tables, we need to ensure
-- comments table references are correct
-- (The id_entity field will still work as it stores the numeric ID)

-- ============================================
-- 7. Update any other tables that reference the changed primary keys
-- ============================================

-- Update any indexes that might be affected
-- (Add specific index updates here if needed)

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these after migration to verify success:
-- SELECT 'posts' as table_name, COUNT(*) as id_count FROM posts WHERE id IS NOT NULL;
-- SELECT 'news' as table_name, COUNT(*) as id_count FROM news WHERE id IS NOT NULL;
-- SELECT 'vpo' as table_name, COUNT(*) as id_count FROM vpo WHERE id IS NOT NULL;
-- SELECT 'spo' as table_name, COUNT(*) as id_count FROM spo WHERE id IS NOT NULL;
-- SELECT 'schools' as table_name, COUNT(*) as id_count FROM schools WHERE id IS NOT NULL;