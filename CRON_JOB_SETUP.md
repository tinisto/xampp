# Cron Job Setup Documentation

## Overview
The comment system includes an email notification feature that requires a cron job to be set up on the server. This cron job will check for pending notifications and send emails to users when they receive replies or mentions.

## Quick Setup

### Option 1: Using the Setup Script (Recommended)
```bash
bash setup-cron-job.sh
```

### Option 2: Manual Setup
Add this line to your crontab:
```bash
*/10 * * * * /usr/bin/php /home/11066451/public_html/cron/send-comment-notifications.php >> /var/log/comment-notifications.log 2>&1
```

To edit crontab:
```bash
crontab -e
```

## What the Cron Job Does

1. **Runs every 10 minutes** (*/10 * * * *)
2. **Checks for pending notifications** in the `comment_notifications` table
3. **Sends emails** for:
   - Direct replies to comments
   - @mentions in any comment
4. **Updates notification status** to prevent duplicate sends
5. **Logs activity** to `/var/log/comment-notifications.log`

## Email Configuration

The script uses the following configuration (edit in `/cron/send-comment-notifications.php`):

```php
$emailConfig = [
    'from_email' => 'noreply@11klassniki.ru',
    'from_name' => '11 Классники',
    'smtp_host' => 'localhost',  // Update if using external SMTP
    'smtp_port' => 25,
    'smtp_auth' => false,
    'smtp_username' => '',
    'smtp_password' => ''
];
```

## Testing the Cron Job

### 1. Test the PHP script directly:
```bash
php /home/11066451/public_html/cron/send-comment-notifications.php
```

### 2. Check if cron job is installed:
```bash
crontab -l | grep comment-notifications
```

### 3. Monitor the logs:
```bash
tail -f /var/log/comment-notifications.log
```

### 4. Check email queue in database:
```sql
SELECT * FROM comment_notifications WHERE sent = 0 ORDER BY created_at DESC;
```

## Troubleshooting

### Emails not sending?

1. **Check PHP path**:
   ```bash
   which php
   ```
   Update the cron job if path is different.

2. **Check permissions**:
   ```bash
   ls -la /home/11066451/public_html/cron/send-comment-notifications.php
   ```
   Should be readable by the cron user.

3. **Check PHP mail function**:
   ```php
   <?php
   if(mail('test@example.com', 'Test', 'Test message')) {
       echo "Mail works\n";
   } else {
       echo "Mail failed\n";
   }
   ?>
   ```

4. **Check database connection**:
   - Ensure database credentials are accessible from cron environment
   - Check if `/database/db_connections.php` path is correct

### Common Issues

1. **"No such file or directory"**
   - Use absolute paths in the cron job
   - Check if PHP path is correct

2. **"Permission denied"**
   - Ensure script has execute permissions
   - Check log file permissions

3. **"Database connection failed"**
   - Cron runs with minimal environment
   - May need to set database credentials explicitly

## Monitoring

### Email Statistics Query
```sql
-- Daily email stats
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_notifications,
    SUM(CASE WHEN sent = 1 THEN 1 ELSE 0 END) as sent,
    SUM(CASE WHEN sent = 0 THEN 1 ELSE 0 END) as pending,
    AVG(CASE WHEN sent_at IS NOT NULL 
        THEN TIMESTAMPDIFF(SECOND, created_at, sent_at) 
        ELSE NULL END) as avg_send_delay_seconds
FROM comment_notifications
GROUP BY DATE(created_at)
ORDER BY date DESC
LIMIT 30;
```

### Failed Notifications
```sql
-- Check failed notifications (pending for > 1 hour)
SELECT * FROM comment_notifications 
WHERE sent = 0 
AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY created_at;
```

## Best Practices

1. **Monitor logs regularly** - Check for errors or unusual patterns
2. **Set up email alerts** - Get notified if cron job fails
3. **Implement retry logic** - Handle temporary email failures
4. **Clean old notifications** - Archive or delete old sent notifications
5. **Rate limiting** - Current limit is 1 email per second

## Security Considerations

1. **Validate email addresses** before sending
2. **Sanitize email content** to prevent injection
3. **Use authenticated SMTP** for production
4. **Monitor for abuse** - unusual spike in notifications
5. **Implement unsubscribe** mechanism for users

## Alternative Setup Methods

### Using Systemd Timer (Modern Linux)
Create `/etc/systemd/system/comment-notifications.service`:
```ini
[Unit]
Description=Send comment notification emails

[Service]
Type=oneshot
ExecStart=/usr/bin/php /home/11066451/public_html/cron/send-comment-notifications.php
User=www-data
```

Create `/etc/systemd/system/comment-notifications.timer`:
```ini
[Unit]
Description=Run comment notifications every 10 minutes

[Timer]
OnCalendar=*:0/10
Persistent=true

[Install]
WantedBy=timers.target
```

Enable:
```bash
systemctl enable comment-notifications.timer
systemctl start comment-notifications.timer
```

### Using Web-based Cron Services
If server cron is not available, use services like:
- cron-job.org
- easycron.com
- cronhub.io

Set URL: `https://11klassniki.ru/cron/send-comment-notifications.php`
(Requires adding web authentication to the script)