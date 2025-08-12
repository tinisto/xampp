# 🚀 DEPLOY NOW - Step by Step Guide

## CRITICAL: Backup First!
Before proceeding, backup your live site:
- Export your production database
- Download all current website files
- Test that you can restore the backup

## Step 1: Create Database Tables

Run this SQL on your production database:

```sql
-- Rate limiting table
CREATE TABLE IF NOT EXISTS `rate_limit_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `attempt_time` datetime NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT 'login',
  PRIMARY KEY (`id`),
  KEY `idx_ip_email_action` (`ip_address`, `email`, `action`),
  KEY `idx_attempt_time` (`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Security logs table
CREATE TABLE IF NOT EXISTS `security_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `event_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Step 2: Upload These NEW Files

Upload these files to your production server:

```
/includes/csrf-protection.php
/includes/rate-limiter.php  
/includes/security-headers.php
/includes/security-logger.php
/includes/input-validator.php
/includes/header.php
/includes/footer.php
/template.php
```

## Step 3: Upload These UPDATED Files

These files have been modified with security fixes:

```
/home_modern.php
/tests-main-real.php
/dashboard-users-new.php
/dashboard-vpo-new.php
/dashboard-spo-new.php
/dashboard-posts-functional.php
/analyze-news-categories.php
/pages/login/login_process_simple.php
/pages/registration/registration_process.php
/pages/search/search-process.php
/comments/process_comments.php
/real_template.php
/common-components/template-engine-ultimate.php
```

## Step 4: DELETE These Old Files

Remove these files from production:

```
/includes/header_modern.php
/includes/footer_modern.php
/real_template_local.php
/common-components/header-diagnostic.php
/common-components/real_header.php
/common-components/real_footer.php
/pages/common/educational-institutions-in-region/header-links.php
/real_template_broken.php
/real_template_current.php
/real_template_fixed.php
```

## Step 5: Set File Permissions

Ensure proper permissions:
- Files: 644 (readable by web server)
- Directories: 755 (executable by web server)

## Step 6: Test Deployment

1. Upload `/deployment/test_deployment.php` to your site root
2. Visit `https://yourdomain.com/deployment/test_deployment.php`
3. Check all tests pass (green checkmarks)

## Step 7: Manual Testing

Test these critical functions:

1. **Login**: Try logging in (should work with rate limiting)
2. **Wrong Password**: Try 6 wrong passwords (should block after 5 attempts)
3. **Registration**: Create new account (should require strong password)
4. **Search**: Use site search (should work without errors)
5. **Comments**: Post a comment (should require login and CSRF token)
6. **Error Check**: Look for any PHP error messages

## Step 8: Monitor

After deployment:
- Check your server error logs
- Monitor site performance
- Watch for any user complaints
- Check that security logging is working

## If Something Goes Wrong

**Rollback Steps:**
1. Restore your backup files
2. Restore your backup database  
3. Clear any caches
4. Document what went wrong

## Important Notes

✅ **What's New:**
- All forms now require CSRF tokens
- Login attempts are limited to 5 per 15 minutes
- All database queries use prepared statements
- Strong password validation (8+ chars, mixed case, numbers)
- Comprehensive input validation on all forms

⚠️ **Requirements:**
- PHP 7.4 or higher
- MySQLi extension
- Session support enabled
- Write permissions for logs (optional)

## Success Indicators

You'll know deployment was successful when:
- No PHP errors appear on any page
- Login works but blocks after 5 failed attempts
- Registration requires strong passwords
- All pages load correctly
- Search and comments function normally
- test_deployment.php shows all green checkmarks

## Support

If you encounter issues:
1. Check server error logs first
2. Run test_deployment.php to identify problems
3. Verify all files were uploaded correctly
4. Confirm database tables were created
5. Check file permissions

**The site is now significantly more secure with modern PHP security practices!**