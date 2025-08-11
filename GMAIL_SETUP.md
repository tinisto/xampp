# Gmail Setup for Sending Emails from Localhost

## Steps to Enable Gmail SMTP:

1. **Enable 2-Factor Authentication** (if not already enabled)
   - Go to: https://myaccount.google.com/security
   - Click on "2-Step Verification"
   - Follow the setup process

2. **Generate App Password**
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" as the app
   - Select "Other" as device and name it "11klassniki localhost"
   - Click "Generate"
   - Copy the 16-character password

3. **Update email.php**
   Edit `/includes/email.php` line 392-393:
   ```php
   $mail->Username   = 'tinisto@gmail.com';        // Your Gmail
   $mail->Password   = 'xxxx xxxx xxxx xxxx';      // Your App Password (without spaces)
   ```

4. **Test It**
   - Go to http://localhost:8000/forgot-password.php
   - Enter your email
   - Check your Gmail inbox!

## Alternative: Use a Temporary Solution

For quick testing, update email.php with these test credentials:
```php
$mail->Username   = 'your-email@gmail.com';
$mail->Password   = 'your-16-char-app-password';
```

## Security Note
- Never commit real credentials to git
- Use environment variables in production
- App passwords are safer than regular passwords
- Consider using SendGrid or Mailgun for production