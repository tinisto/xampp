<?php
// Test sending email directly to Gmail
require_once 'includes/email.php';

// Test email
$to = 'tinisto@gmail.com';
$subject = 'Test Email from 11klassniki.ru';
$htmlBody = '<h1>Test Email</h1><p>This is a test email sent from your localhost.</p>';
$textBody = 'Test Email - This is a test email sent from your localhost.';

// Try to send using PHP mail()
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: noreply@11klassniki.ru',
    'Reply-To: noreply@11klassniki.ru',
    'X-Mailer: PHP/' . phpversion()
];

$result = mail($to, $subject, $htmlBody, implode("\r\n", $headers));

if ($result) {
    echo "Email sent successfully!";
} else {
    echo "Email failed to send.";
}

echo "\n\nPHP mail configuration:\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
?>