# Website Cleanup Guide

This guide explains how to use the cleanup tools to organize and clean your website files.

## Tools Available

### 1. website-cleanup-script.php (Command Line)
A comprehensive PHP script for automated cleanup with safety levels.

**Usage:**
```bash
# Dry run - see what would be deleted without actually deleting
php website-cleanup-script.php --dry-run

# Interactive mode - asks for confirmation for each file
php website-cleanup-script.php --interactive

# Actual cleanup - deletes files automatically
php website-cleanup-script.php

# Show help
php website-cleanup-script.php --help
```

**Features:**
- Categorizes files by safety level
- Safe to delete: .bak, .tmp, test files, debug files, Python scripts
- Needs review: under_construction files, migrations, dashboard duplicates
- Reorganizes files to appropriate directories
- Creates detailed logs
- Shows space freed

### 2. cleanup-review-interface.php (Web Interface)
A visual interface for reviewing files before deletion.

**Access:** https://yoursite.com/cleanup-review-interface.php

**Features:**
- Preview file contents before deletion
- Archive files instead of deleting
- Bulk operations for categories
- Real-time statistics
- Safe web-based interface

## File Categories

### Safe to Delete (Automated)
- `*.bak`, `*.backup`, `*.old`, `*.tmp`
- `test-*.php`, `check-*.php`, `debug-*.php`
- `upload-*.py`, `fix-*.py`, `verify-*.py`
- `_cleanup/`, `_old/`, `_backup/` directories

### Needs Manual Review
- `*under_construction*.php` files
- `*migration*.php` files
- Duplicate dashboard files
- Admin tool files in wrong locations

### Will Be Reorganized
- `admin/*.php` → `dashboard/admin-tools/`
- `migrations/*.php` → `database/migrations/`
- `test_*.php` → `tests/`
- `check_*.php` → `diagnostics/`

## Recommended Cleanup Process

1. **First, do a dry run:**
   ```bash
   php website-cleanup-script.php --dry-run > cleanup-preview.txt
   ```

2. **Review the preview output**

3. **Use the web interface for files needing review:**
   - Open cleanup-review-interface.php in browser
   - Preview each file marked for review
   - Archive important files, delete unnecessary ones

4. **Run the actual cleanup:**
   ```bash
   php website-cleanup-script.php
   ```

5. **Check the cleanup log:**
   - Review `cleanup-log-[timestamp].txt`
   - Verify no important files were deleted

## Important Files Found

### Under Construction Files
- `pages/about_us_under_construction.php`
- `pages/privacy_policy_under_construction.php`
- `pages/search_under_construction.php`
- `pages/stats_under_construction.php`

### Duplicate/Misplaced Files
- `dashboard-professional.php` (duplicate in root)
- `dashboard/comments-simple.php`
- `dashboard/comments.php`
- `dashboard/database-text-cleanup.php`
- `admin/database-text-cleanup.php`

### Python Upload Scripts (200+ files)
These were used for FTP uploads during development and can be safely removed.

## Safety Notes

- Always do a dry run first
- Archive important files instead of deleting
- Check git status before deleting to ensure files are committed
- Keep the cleanup logs for reference
- The scripts will NOT delete:
  - Core application files
  - User data
  - Configuration files
  - Database files

## After Cleanup

1. **Update .gitignore:**
   Add patterns to prevent temporary files:
   ```
   *.bak
   *.tmp
   *.old
   test-*.php
   upload-*.py
   _cleanup/
   ```

2. **Set up automated cleanup:**
   Add to crontab for weekly cleanup:
   ```bash
   0 2 * * 0 php /path/to/website-cleanup-script.php --dry-run
   ```

3. **Review remaining files:**
   - Decide on under_construction files
   - Consolidate duplicate functionality
   - Archive old migrations

## Support

If you encounter any issues:
1. Check the cleanup log files
2. Restore from backups if needed
3. Use git to recover deleted files if they were committed