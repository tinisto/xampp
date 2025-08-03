# Database Migration Guide

## 🎯 What We're Doing
Migrating from inconsistent old database structure to clean new structure while preserving all Russian content.

## 📋 Step-by-Step Instructions

### Step 1: Backup Current Database
1. Open phpMyAdmin (localhost/phpmyadmin or ipage.com phpMyAdmin)
2. Select your current database
3. Click "Export" → "Go" → Save the .sql file

### Step 2: Create New Database Locally
1. Open phpMyAdmin
2. Click "New" → Database name: `11klassniki_new`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"

### Step 3: Create Schema
1. In phpMyAdmin, select `11klassniki_new`
2. Click "SQL" tab
3. Copy contents from `create-new-database-schema.sql`
4. Click "Go"

### Step 4: Run Migration
1. Open browser: `http://localhost/migrate-database-data.php`
2. Read the warnings
3. Click "CONFIRM MIGRATION"
4. Wait for completion (shows progress for each table)

### Step 5: Test New Database
1. Update `.env` file: `DB_NAME=11klassniki_new`
2. Test these pages:
   - Homepage with news
   - VPO page: `/vpo-in-region/moskovskaya-oblast`
   - SPO page: `/spo-in-region/moskovskaya-oblast`
   - Individual institution pages
   - Search functionality

### Step 6: Deploy to Production
1. Backup live database first
2. Create `11klassniki_new` on iPage phpMyAdmin
3. Upload schema via SQL tab
4. Upload and run migration script
5. Update live `.env` file

## 🔧 Files Created
- `create-new-database-schema.sql` - New clean database structure
- `migrate-database-data.php` - Data migration script
- Migration handles Russian text with UTF8MB4

## 🚨 Important Notes
- Always backup before migration
- Test locally first
- Russian text preserved with proper encoding
- All declensions (род. падеж, предл. падеж) maintained

## ✅ Expected Results
- Clean table/column names
- Proper foreign key relationships
- All Russian content preserved
- Better performance and maintainability