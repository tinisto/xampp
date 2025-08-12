# Deployment Checklist for Live Site Update

## Pre-Deployment Checks

### 1. Backup Current Live Site
- [ ] Backup database
- [ ] Backup all files
- [ ] Test backup restoration process

### 2. Environment Configuration
- [ ] Verify `.env` file on production has correct settings
- [ ] Ensure `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Verify database credentials are correct

### 3. Database Changes Required
- [ ] Create `rate_limit_attempts` table (for login rate limiting)
- [ ] Create `security_logs` table (for security event logging)

### 4. New Required Files
These files MUST be uploaded:
- `/includes/csrf-protection.php`
- `/includes/rate-limiter.php`
- `/includes/security-headers.php`
- `/includes/security-logger.php`
- `/includes/input-validator.php`
- `/includes/header.php` (renamed from header_modern.php)
- `/includes/footer.php` (renamed from footer_modern.php)
- `/template.php` (renamed from real_template_local.php)

### 5. Files to Delete from Production
These files should be REMOVED:
- `/includes/header_modern.php`
- `/includes/footer_modern.php`
- `/real_template_local.php`
- `/common-components/header-diagnostic.php`
- `/common-components/real_header.php`
- `/common-components/real_footer.php`
- `/pages/common/educational-institutions-in-region/header-links.php`
- `/real_template_broken.php`
- `/real_template_current.php`
- `/real_template_fixed.php`

### 6. Critical Updates
- All 43+ PHP files have been updated to use new template names
- CSRF protection is now required on all forms
- Login process now has rate limiting
- All inputs are validated

## Deployment Steps

### Step 1: Create Database Tables
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

### Step 2: Upload Files
1. Upload all modified files from git
2. Ensure file permissions are correct (usually 644 for files, 755 for directories)
3. Delete the files listed in section 5

### Step 3: Clear Caches
- Clear any PHP opcache
- Clear any CDN caches
- Clear browser caches

### Step 4: Test Critical Functions
- [ ] Test login (with rate limiting)
- [ ] Test registration (with password validation)
- [ ] Test search functionality
- [ ] Test comment submission
- [ ] Test CSRF protection is working
- [ ] Verify no error messages are displayed

### Step 5: Monitor
- Check error logs for any issues
- Monitor site performance
- Check that security logging is working

## Rollback Plan
If issues occur:
1. Restore files from backup
2. Restore database if needed
3. Clear all caches
4. Document what went wrong

## Post-Deployment
- [ ] Verify all pages load correctly
- [ ] Test user registration and login
- [ ] Submit a test comment
- [ ] Check error logs
- [ ] Update deployment documentation with any issues encountered

## Important Notes
1. The site now uses prepared statements for all database queries
2. All forms require CSRF tokens
3. Login attempts are rate limited (5 per 15 minutes)
4. Strong password validation is enforced
5. All user inputs are validated and sanitized

## Support Files
- See `/VALIDATION_GUIDE.md` for validation documentation
- See `/includes/validation-example.php` for implementation examples
- Check error logs at `/var/log/` or your hosting's error log location