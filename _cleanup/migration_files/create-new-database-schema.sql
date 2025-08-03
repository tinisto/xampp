-- =====================================================
-- 11klassniki.ru - New Clean Database Schema
-- =====================================================
-- This creates a completely new database with proper naming conventions
-- and modern structure while preserving all existing data

-- New database name
-- CREATE DATABASE 11klassniki_new;
-- USE 11klassniki_new;

-- =====================================================
-- 1. REFERENCE TABLES (Countries, Regions, Areas, Towns)
-- =====================================================

-- Countries table
CREATE TABLE countries (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    country_name VARCHAR(255) NOT NULL,
    country_name_en VARCHAR(255) NOT NULL,
    country_code VARCHAR(3) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_country_code (country_code),
    INDEX idx_country_name (country_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Regions table (федеральные округа, области, края)
CREATE TABLE regions (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    country_id INT(10) UNSIGNED NOT NULL,
    region_name VARCHAR(255) NOT NULL,
    region_name_en VARCHAR(255) NOT NULL,
    region_name_genitive VARCHAR(255) NOT NULL COMMENT 'Родительный падеж',
    region_name_locative VARCHAR(255) NOT NULL COMMENT 'Предложный падеж (где?)',
    region_name_locative_en VARCHAR(255) NOT NULL,
    region_image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT,
    INDEX idx_region_name (region_name),
    INDEX idx_region_name_en (region_name_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Areas table (районы)
CREATE TABLE areas (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    region_id INT(10) UNSIGNED NOT NULL,
    area_name VARCHAR(255) NOT NULL,
    area_name_en VARCHAR(255) NULL,
    area_name_genitive VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE RESTRICT,
    INDEX idx_area_name (area_name),
    INDEX idx_region_area (region_id, area_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Towns table (города, поселки)
CREATE TABLE towns (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    area_id INT(10) UNSIGNED NOT NULL,
    region_id INT(10) UNSIGNED NOT NULL,
    country_id INT(10) UNSIGNED NOT NULL,
    town_name VARCHAR(255) NOT NULL,
    town_name_en VARCHAR(255) NULL,
    town_name_genitive VARCHAR(255) NOT NULL COMMENT 'Родительный падеж',
    description TEXT NULL,
    image_url VARCHAR(255) NULL,
    url_slug VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE RESTRICT,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE RESTRICT,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_url_slug (url_slug),
    INDEX idx_town_name (town_name),
    INDEX idx_town_name_en (town_name_en),
    INDEX idx_region_town (region_id, town_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. CATEGORIES TABLE
-- =====================================================

CREATE TABLE categories (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(255) NOT NULL,
    category_name_en VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    meta_keywords VARCHAR(500) NULL,
    category_description TEXT NULL,
    url_slug VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT(10) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_url_slug (url_slug),
    INDEX idx_category_name (category_name),
    INDEX idx_active_sort (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. USERS TABLE
-- =====================================================

CREATE TABLE users (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(191) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    first_name VARCHAR(191) NULL,
    last_name VARCHAR(191) NULL,
    occupation VARCHAR(255) NULL,
    timezone VARCHAR(50) DEFAULT 'Europe/Moscow',
    avatar_url VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT FALSE,
    is_suspended BOOLEAN DEFAULT FALSE,
    activation_token VARCHAR(255) NULL,
    activation_link VARCHAR(255) NULL,
    email_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active),
    INDEX idx_email_password (email, password)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. EDUCATIONAL INSTITUTIONS
-- =====================================================

-- VPO (Universities) table
CREATE TABLE universities (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(10) UNSIGNED NULL,
    parent_university_id INT(10) UNSIGNED NULL COMMENT 'For branches/filials',
    university_name VARCHAR(255) NOT NULL,
    university_name_genitive VARCHAR(255) NOT NULL,
    full_name TEXT NOT NULL,
    short_name VARCHAR(255) NULL,
    old_names TEXT NULL COMMENT 'Previous names',
    
    -- Location
    town_id INT(10) UNSIGNED NOT NULL,
    area_id INT(10) UNSIGNED NOT NULL,
    region_id INT(10) UNSIGNED NOT NULL,
    country_id INT(10) UNSIGNED NOT NULL,
    postal_code VARCHAR(10) NULL,
    street_address VARCHAR(255) NOT NULL,
    
    -- Contact information
    phone VARCHAR(255) NULL,
    fax VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    
    -- Director information
    director_name VARCHAR(255) NULL,
    director_role VARCHAR(255) NULL,
    director_info TEXT NULL,
    director_email VARCHAR(255) NULL,
    director_phone VARCHAR(255) NULL,
    
    -- Administration
    accreditation VARCHAR(255) NULL,
    license VARCHAR(255) NULL,
    founding_year YEAR(4) NULL,
    
    -- SEO and display
    meta_description TEXT NULL,
    meta_keywords TEXT NULL,
    history TEXT NULL,
    url_slug VARCHAR(255) NULL,
    
    -- Images
    image_1 VARCHAR(255) NULL,
    image_2 VARCHAR(255) NULL,
    image_3 VARCHAR(255) NULL,
    
    -- Social media
    vkontakte_url VARCHAR(255) NULL,
    
    -- Statistics
    view_count INT(10) UNSIGNED DEFAULT 0,
    
    -- Status
    is_approved BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_university_id) REFERENCES universities(id) ON DELETE SET NULL,
    FOREIGN KEY (town_id) REFERENCES towns(id) ON DELETE RESTRICT,
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE RESTRICT,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE RESTRICT,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_university_name (university_name),
    UNIQUE KEY unique_url_slug (url_slug),
    INDEX idx_university_name (university_name),
    INDEX idx_region (region_id),
    INDEX idx_town (town_id),
    INDEX idx_approved (is_approved),
    INDEX idx_active (is_active),
    INDEX idx_parent (parent_university_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SPO (Colleges) table - Similar structure to universities
CREATE TABLE colleges (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(10) UNSIGNED NULL,
    parent_college_id INT(10) UNSIGNED NULL,
    college_name VARCHAR(255) NOT NULL,
    college_name_genitive VARCHAR(255) NOT NULL,
    full_name TEXT NOT NULL,
    short_name VARCHAR(255) NULL,
    old_names TEXT NULL,
    
    -- Location
    town_id INT(10) UNSIGNED NOT NULL,
    area_id INT(10) UNSIGNED NOT NULL,
    region_id INT(10) UNSIGNED NOT NULL,
    country_id INT(10) UNSIGNED NOT NULL,
    postal_code VARCHAR(10) NULL,
    street_address VARCHAR(255) NOT NULL,
    
    -- Contact information
    phone VARCHAR(255) NULL,
    fax VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    
    -- Director information
    director_name VARCHAR(255) NULL,
    director_role VARCHAR(255) NULL,
    director_info TEXT NULL,
    director_email VARCHAR(255) NULL,
    director_phone VARCHAR(255) NULL,
    
    -- Administration
    accreditation VARCHAR(255) NULL,
    license VARCHAR(255) NULL,
    founding_year YEAR(4) NULL,
    
    -- SEO and display
    meta_description TEXT NULL,
    meta_keywords TEXT NULL,
    history TEXT NULL,
    url_slug VARCHAR(255) NULL,
    
    -- Images
    image_1 VARCHAR(255) NULL,
    image_2 VARCHAR(255) NULL,
    image_3 VARCHAR(255) NULL,
    
    -- Social media
    vkontakte_url VARCHAR(255) NULL,
    
    -- Statistics
    view_count INT(10) UNSIGNED DEFAULT 0,
    
    -- Status
    is_approved BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_college_id) REFERENCES colleges(id) ON DELETE SET NULL,
    FOREIGN KEY (town_id) REFERENCES towns(id) ON DELETE RESTRICT,
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE RESTRICT,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE RESTRICT,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_college_name (college_name),
    UNIQUE KEY unique_url_slug (url_slug),
    INDEX idx_college_name (college_name),
    INDEX idx_region (region_id),
    INDEX idx_town (town_id),
    INDEX idx_approved (is_approved),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schools table
CREATE TABLE schools (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(10) UNSIGNED NULL,
    school_name VARCHAR(255) NOT NULL,
    full_name TEXT NULL,
    short_name VARCHAR(255) NULL,
    
    -- Location
    town_id INT(10) UNSIGNED NULL,
    area_id INT(10) UNSIGNED NOT NULL,
    region_id INT(10) UNSIGNED NOT NULL,
    country_id INT(10) UNSIGNED NOT NULL,
    street_address VARCHAR(255) NULL,
    
    -- Contact information
    phone VARCHAR(255) NULL,
    fax VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    
    -- Director information
    director_name VARCHAR(255) NULL,
    director_role VARCHAR(255) NULL,
    director_info TEXT NULL,
    director_email VARCHAR(255) NULL,
    director_phone VARCHAR(255) NULL,
    
    -- Administration
    founding_year YEAR(4) NULL,
    history TEXT NULL,
    
    -- Images
    logo_url VARCHAR(255) NULL,
    image_1 VARCHAR(255) NULL,
    image_2 VARCHAR(255) NULL,
    image_3 VARCHAR(255) NULL,
    
    -- Statistics
    view_count INT(10) UNSIGNED DEFAULT 0,
    
    -- Status
    is_approved BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (town_id) REFERENCES towns(id) ON DELETE SET NULL,
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE RESTRICT,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE RESTRICT,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT,
    
    INDEX idx_school_name (school_name),
    INDEX idx_region (region_id),
    INDEX idx_town (town_id),
    INDEX idx_approved (is_approved),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. CONTENT TABLES
-- =====================================================

-- News table
CREATE TABLE news (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(10) UNSIGNED NULL,
    category_id INT(10) UNSIGNED NULL,
    
    -- Content
    news_title VARCHAR(500) NOT NULL,
    news_description TEXT NULL,
    news_content TEXT NOT NULL,
    news_author VARCHAR(191) NULL,
    
    -- SEO
    meta_description TEXT NULL,
    meta_keywords TEXT NULL,
    url_slug VARCHAR(191) NOT NULL,
    
    -- Related institutions
    university_id INT(10) UNSIGNED NULL,
    college_id INT(10) UNSIGNED NULL,
    school_id INT(10) UNSIGNED NULL,
    
    -- Images
    image_1 VARCHAR(255) NULL,
    image_2 VARCHAR(255) NULL,
    image_3 VARCHAR(255) NULL,
    image_source VARCHAR(191) NULL,
    
    -- Statistics
    view_count INT(11) UNSIGNED DEFAULT 0,
    
    -- Status
    is_approved BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE SET NULL,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_url_slug (url_slug),
    INDEX idx_news_title (news_title(100)),
    INDEX idx_published (published_at),
    INDEX idx_approved (is_approved),
    INDEX idx_category (category_id),
    INDEX idx_author (news_author),
    FULLTEXT KEY ft_content (news_title, news_content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posts table (blog posts)
CREATE TABLE posts (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(10) UNSIGNED NULL,
    category_id INT(10) UNSIGNED NULL,
    
    -- Content
    post_title VARCHAR(500) NOT NULL,
    post_description TEXT NULL,
    post_content TEXT NOT NULL,
    post_author VARCHAR(191) NULL,
    post_bio TEXT NULL,
    
    -- SEO
    meta_description TEXT NULL,
    meta_keywords TEXT NULL,
    url_slug VARCHAR(191) NOT NULL,
    
    -- Images
    image_1 VARCHAR(255) NULL,
    image_2 VARCHAR(255) NULL,
    image_3 VARCHAR(255) NULL,
    
    -- Statistics
    view_count INT(11) UNSIGNED DEFAULT 0,
    
    -- Status
    is_approved BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_url_slug (url_slug),
    INDEX idx_post_title (post_title(100)),
    INDEX idx_published (published_at),
    INDEX idx_approved (is_approved),
    INDEX idx_category (category_id),
    FULLTEXT KEY ft_content (post_title, post_content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. INTERACTION TABLES
-- =====================================================

-- Comments table
CREATE TABLE comments (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(10) UNSIGNED NULL,
    parent_comment_id INT(10) UNSIGNED NULL,
    
    -- Content reference (polymorphic)
    commentable_type ENUM('news', 'post', 'university', 'college', 'school') NOT NULL,
    commentable_id INT(10) UNSIGNED NOT NULL,
    
    -- Comment content
    comment_content TEXT NOT NULL,
    commenter_name VARCHAR(191) NULL,
    commenter_email VARCHAR(191) NULL,
    
    -- Status
    is_approved BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    
    INDEX idx_commentable (commentable_type, commentable_id),
    INDEX idx_approved (is_approved),
    INDEX idx_parent (parent_comment_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table (contact form submissions)
CREATE TABLE messages (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    sender_name VARCHAR(191) NOT NULL,
    sender_email VARCHAR(191) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message_content TEXT NOT NULL,
    sender_ip VARCHAR(45) NULL,
    user_agent TEXT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    is_replied BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_email (sender_email),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Search queries logging
CREATE TABLE search_queries (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    query_text VARCHAR(255) NOT NULL,
    results_count INT(10) UNSIGNED DEFAULT 0,
    user_ip VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_query (query_text),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. INITIAL DATA
-- =====================================================

-- Insert default country (Russia)
INSERT INTO countries (country_name, country_name_en, country_code) VALUES 
('Россия', 'Russia', 'RU');

-- Insert default categories
INSERT INTO categories (category_name, category_name_en, url_slug, category_description) VALUES
('Новости образования', 'Education News', 'education-news', 'Актуальные новости сферы образования'),
('Абитуриентам', 'For Applicants', 'for-applicants', 'Информация для поступающих'),
('Студенческая жизнь', 'Student Life', 'student-life', 'О студенческой жизни');