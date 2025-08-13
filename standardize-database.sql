-- Database Standardization Script
-- This script will standardize meta field names and remove meta_keywords

-- 1. CATEGORIES TABLE
-- Check current structure first
SHOW COLUMNS FROM categories WHERE Field LIKE '%meta%' OR Field LIKE '%description%' OR Field LIKE '%keyword%';

-- Standardize field names
ALTER TABLE categories 
    CHANGE COLUMN `meta_description` `meta_description` TEXT DEFAULT NULL,
    DROP COLUMN IF EXISTS `meta_keywords`,
    DROP COLUMN IF EXISTS `meta_k_category`,
    DROP COLUMN IF EXISTS `meta_d_category`;

-- 2. POSTS TABLE  
SHOW COLUMNS FROM posts WHERE Field LIKE '%meta%' OR Field LIKE '%description%' OR Field LIKE '%keyword%';

ALTER TABLE posts
    CHANGE COLUMN `meta_d` `meta_description` TEXT DEFAULT NULL,
    DROP COLUMN IF EXISTS `meta_k`,
    DROP COLUMN IF EXISTS `meta_keywords`;

-- 3. NEWS TABLE
SHOW COLUMNS FROM news WHERE Field LIKE '%meta%' OR Field LIKE '%description%' OR Field LIKE '%keyword%';

ALTER TABLE news
    ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL,
    DROP COLUMN IF EXISTS `meta_keywords`,
    DROP COLUMN IF EXISTS `meta_k`;

-- 4. VPO TABLE
SHOW COLUMNS FROM vpo WHERE Field LIKE '%meta%' OR Field LIKE '%description%' OR Field LIKE '%keyword%';

ALTER TABLE vpo
    CHANGE COLUMN `metaD` `meta_description` TEXT DEFAULT NULL,
    DROP COLUMN IF EXISTS `metaK`,
    DROP COLUMN IF EXISTS `meta_keywords`;

-- 5. SPO TABLE
SHOW COLUMNS FROM spo WHERE Field LIKE '%meta%' OR Field LIKE '%description%' OR Field LIKE '%keyword%';

ALTER TABLE spo
    CHANGE COLUMN `metaD` `meta_description` TEXT DEFAULT NULL,
    DROP COLUMN IF EXISTS `metaK`,
    DROP COLUMN IF EXISTS `meta_keywords`;

-- 6. SCHOOLS TABLE
SHOW COLUMNS FROM schools WHERE Field LIKE '%meta%' OR Field LIKE '%description%' OR Field LIKE '%keyword%';

ALTER TABLE schools
    ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL,
    DROP COLUMN IF EXISTS `meta_keywords`;

-- 7. PAGES TABLE (if exists)
-- Check if table exists first
SHOW TABLES LIKE 'pages';

-- If exists, standardize
ALTER TABLE pages
    ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL,
    DROP COLUMN IF EXISTS `meta_keywords`;

-- Final check - show all meta fields remaining
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE
FROM 
    INFORMATION_SCHEMA.COLUMNS
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND (COLUMN_NAME LIKE '%meta%' OR COLUMN_NAME LIKE '%description%')
ORDER BY 
    TABLE_NAME, COLUMN_NAME;