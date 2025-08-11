<?php
// Email configuration for sending real emails from localhost
// Using Gmail SMTP (requires app password)

class GmailEmailer {
    private static $gmailEmail = 'your-email@gmail.com'; // Your Gmail address
    private static $gmailAppPassword = 'your-app-password'; // Gmail App Password (not regular password)
    
    public static function sendEmail($to, $subject, $htmlBody) {
        // Gmail SMTP configuration
        $smtpHost = 'smtp.gmail.com';
        $smtpPort = 587;
        
        // Email headers
        $headers = [
            'From: ' . self::$gmailEmail,
            'Reply-To: ' . self::$gmailEmail,
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // SMTP connection string for macOS
        $additionalParams = '-f' . self::$gmailEmail;
        
        // Try to send
        return mail($to, $subject, $htmlBody, implode("\r\n", $headers), $additionalParams);
    }
}

// To use Gmail SMTP properly, you need:
// 1. Enable 2-factor authentication on your Gmail account
// 2. Generate an App Password: https://myaccount.google.com/apppasswords
// 3. Use PHPMailer library (recommended)

// Better solution: Use PHPMailer
// Run: composer require phpmailer/phpmailer
?>