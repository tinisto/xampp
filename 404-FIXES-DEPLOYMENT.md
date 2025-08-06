# 404 Fixes - Deployment Guide

## ✅ Completed - Git Push Done!
All fixes have been committed and pushed to the repository.

## 📁 Files to Upload to Server

### **Core 404 Fixes (Priority: HIGH)**
```
pages/post/post.php                    ← Fixed post URL routing
pages/category/category-data-fetch.php ← Fixed category data fetch
```

### **Database Fix Scripts**
```
fix-404-categories.php     ← Web script to create missing categories  
fix-404-manual.php        ← Manual SQL commands
debug-404-routing.php     ← Diagnostic tool
```

## 🚀 Deployment Options

### Option 1: FTP Upload (Recommended)
Use your FTP client (FileZilla, etc.) to upload these files:

**FTP Details:**
- Host: `77.232.131.89` 
- Username: `8b6cdc76_sitearchive`
- Password: `jU9%mHr1`
- Directory: `/domains/11klassniki.ru/public_html/`

### Option 2: Manual Upload Commands
If you have command line access:

```bash
# Upload core fixes
curl -T pages/post/post.php ftp://8b6cdc76_sitearchive:jU9%25mHr1@77.232.131.89/domains/11klassniki.ru/public_html/pages/post/post.php

curl -T pages/category/category-data-fetch.php ftp://8b6cdc76_sitearchive:jU9%25mHr1@77.232.131.89/domains/11klassniki.ru/public_html/pages/category/category-data-fetch.php

# Upload fix scripts  
curl -T fix-404-categories.php ftp://8b6cdc76_sitearchive:jU9%25mHr1@77.232.131.89/domains/11klassniki.ru/public_html/fix-404-categories.php
```

## 🎯 After Upload - Next Steps

### 1. Create Missing Categories
Visit: `https://11klassniki.ru/fix-404-categories.php`

This will create the missing categories:
- ЕГЭ (ege)
- ОГЭ (oge)  
- ВПР (vpr)

### 2. Test Fixed URLs
After running the category creation script, test these URLs:

✅ **Category Pages:**
- `https://11klassniki.ru/category/ege/`
- `https://11klassniki.ru/category/oge/`
- `https://11klassniki.ru/category/vpr/`

✅ **Post Pages:**
- Any individual post URLs that were returning 404 errors

### 3. Clean Up (Optional)
After successful testing, you can remove the fix scripts:
- `fix-404-categories.php`
- `fix-404-manual.php`
- `debug-404-routing.php`

## 🔧 What Was Fixed

### Root Causes:
1. **Missing Categories**: 'ege', 'oge', 'vpr' categories didn't exist in database
2. **URL Parameter Mismatch**: `.htaccess` passes `url_post` but `post.php` expected `url_slug`
3. **Database Field Inconsistency**: Posts use either `url_post` or `url_slug` fields

### Solutions Implemented:
1. **Enhanced `post.php`**: Now handles both `url_post` and `url_slug` parameters and database fields
2. **Improved `category-data-fetch.php`**: Added prepared statements for security
3. **Created category fix script**: Adds missing categories to database

## 🛡️ Security Improvements
- Replaced direct SQL queries with prepared statements
- Added proper parameter sanitization
- Fixed potential SQL injection vulnerabilities

---

**Status**: ✅ Code fixes completed and committed to git  
**Next**: Upload files and run category creation script