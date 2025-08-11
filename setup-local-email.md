# Setting Up Email on Localhost

## Option 1: Quick Fix - Remove localhost check
Edit `/includes/email.php` and comment out the localhost check:

```php
// Change line 381 from:
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') {

// To:
if (false && isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') {
```

## Option 2: Use Gmail SMTP (Recommended)
1. Install PHPMailer via Composer:
```bash
cd /Applications/XAMPP/xamppfiles/htdocs
composer require phpmailer/phpmailer
```

2. Update email.php to use PHPMailer with Gmail

## Option 3: Use MailHog (Best for Development)
1. Install MailHog:
```bash
brew install mailhog
```

2. Start MailHog:
```bash
mailhog
```

3. Configure PHP to use MailHog's SMTP:
- SMTP Host: localhost
- SMTP Port: 1025
- View emails at: http://localhost:8025

## Option 4: Use macOS Postfix
1. Edit Postfix config:
```bash
sudo nano /etc/postfix/main.cf
```

2. Add:
```
relayhost = [smtp.gmail.com]:587
smtp_sasl_auth_enable = yes
smtp_sasl_password_maps = hash:/etc/postfix/sasl_passwd
smtp_sasl_security_options = noanonymous
smtp_use_tls = yes
```

3. Create password file:
```bash
sudo nano /etc/postfix/sasl_passwd
```
Add: `[smtp.gmail.com]:587 your-email@gmail.com:your-app-password`

4. Secure and update:
```bash
sudo postmap /etc/postfix/sasl_passwd
sudo chmod 600 /etc/postfix/sasl_passwd
sudo postfix reload
```