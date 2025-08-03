# Email Configuration Guide for 11klassniki.ru

## Overview
This guide explains how to configure email settings for production use.

## Email Provider Options

### Option 1: Gmail SMTP (Recommended for small-medium sites)
1. Create a Gmail account or use existing: noreply@11klassniki.ru
2. Enable 2-Factor Authentication on the Gmail account
3. Generate an App Password:
   - Go to https://myaccount.google.com/security
   - Click on "2-Step Verification"
   - Scroll down and click on "App passwords"
   - Select "Mail" and generate a password
   - Copy the 16-character password

4. Update .env file:
```
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-16-char-app-password
SMTP_SECURITY=tls
SMTP_PORT=587
ADMIN_EMAIL=admin@11klassniki.ru
```

### Option 2: Yandex Mail (Russian provider)
```
SMTP_HOST=smtp.yandex.ru
SMTP_USERNAME=your-email@yandex.ru
SMTP_PASSWORD=your-password
SMTP_SECURITY=tls
SMTP_PORT=587
ADMIN_EMAIL=admin@11klassniki.ru
```

### Option 3: Mail.ru
```
SMTP_HOST=smtp.mail.ru
SMTP_USERNAME=your-email@mail.ru
SMTP_PASSWORD=your-password
SMTP_SECURITY=ssl
SMTP_PORT=465
ADMIN_EMAIL=admin@11klassniki.ru
```

### Option 4: Your Hosting Provider (iPage)
Contact iPage support for SMTP settings. Common settings:
```
SMTP_HOST=mail.yourdomain.com
SMTP_USERNAME=noreply@11klassniki.ru
SMTP_PASSWORD=your-email-password
SMTP_SECURITY=tls
SMTP_PORT=587
ADMIN_EMAIL=admin@11klassniki.ru
```

## Testing Email Configuration

1. After updating .env, test with this script:
```php
<?php
require_once 'vendor/autoload.php';
require_once 'config/loadEnv.php';
require_once 'includes/functions/email_functions.php';

// Test email
$testEmail = 'your-test-email@gmail.com';
$subject = 'Test Email from 11klassniki';
$body = '<h1>Test Email</h1><p>If you see this, email is working!</p>';

try {
    sendToUser($testEmail, $subject, $body);
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Important Security Notes

1. **Never commit real passwords to Git**
   - Always use .env file for sensitive data
   - Add .env to .gitignore

2. **Use App Passwords**
   - Don't use your main account password
   - Generate app-specific passwords

3. **Monitor Email Usage**
   - Set up alerts for unusual activity
   - Check sent folder regularly

## Troubleshooting

### Common Issues:

1. **"SMTP connect() failed"**
   - Check firewall/hosting allows outbound SMTP
   - Verify credentials are correct
   - Try different ports (587, 465, 25)

2. **"Authentication failed"**
   - Double-check username/password
   - For Gmail, ensure using App Password
   - Check if 2FA is enabled

3. **Emails going to spam**
   - Set up SPF/DKIM records
   - Use consistent "From" address
   - Avoid spam trigger words

## Production Checklist

- [ ] Choose email provider
- [ ] Set up dedicated email account
- [ ] Generate app password
- [ ] Update .env file
- [ ] Test email sending
- [ ] Set up SPF/DKIM records
- [ ] Monitor email deliverability