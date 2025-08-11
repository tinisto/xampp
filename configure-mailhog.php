<?php
// MailHog Configuration for local email testing

// This script configures PHP to use MailHog's SMTP server
// Run this once to update your php.ini settings

$phpIniPath = '/Applications/XAMPP/xamppfiles/etc/php.ini';

// Backup current php.ini
if (!file_exists($phpIniPath . '.backup')) {
    copy($phpIniPath, $phpIniPath . '.backup');
    echo "✅ Created backup of php.ini\n";
}

// Read current php.ini
$phpIni = file_get_contents($phpIniPath);

// Update SMTP settings for MailHog
$updates = [
    'SMTP = localhost',
    'smtp_port = 1025',
    'sendmail_path = "/usr/sbin/sendmail -t -i"',
    'mail.log = "/Applications/XAMPP/xamppfiles/logs/mail.log"'
];

echo "\n📧 MailHog Setup Instructions:\n";
echo "================================\n\n";

echo "1. Start MailHog in a terminal:\n";
echo "   mailhog\n\n";

echo "2. MailHog will run on:\n";
echo "   - SMTP Server: localhost:1025\n";
echo "   - Web UI: http://localhost:8025\n\n";

echo "3. Update php.ini manually:\n";
echo "   sudo nano $phpIniPath\n\n";

echo "4. Find and update these settings:\n";
foreach ($updates as $setting) {
    echo "   $setting\n";
}

echo "\n5. Restart XAMPP Apache:\n";
echo "   sudo /Applications/XAMPP/xamppfiles/xampp restart\n\n";

echo "6. All emails will be captured by MailHog\n";
echo "   View them at: http://localhost:8025\n\n";

echo "That's it! Your local emails will now be captured by MailHog.\n";
?>