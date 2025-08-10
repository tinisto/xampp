<?php
echo "<h1>MySQL Access Check</h1>";

echo "<h2>Common XAMPP MySQL passwords to try:</h2>";
echo "<ul>";
echo "<li>No password (empty)</li>";
echo "<li>root</li>";
echo "<li>password</li>";
echo "<li>admin</li>";
echo "</ul>";

echo "<h2>To reset MySQL root password:</h2>";
echo "<pre>";
echo "1. Stop MySQL in XAMPP Control Panel

2. Open Terminal and run:
   sudo /Applications/XAMPP/xamppfiles/bin/mysqld_safe --skip-grant-tables &

3. In another Terminal window:
   /Applications/XAMPP/xamppfiles/bin/mysql -u root

4. Run these SQL commands:
   FLUSH PRIVILEGES;
   ALTER USER 'root'@'localhost' IDENTIFIED BY '';
   FLUSH PRIVILEGES;
   exit;

5. Stop the mysqld_safe process (Ctrl+C)
6. Start MySQL normally from XAMPP Control Panel
</pre>";

echo "<h2>Alternative: Create new user</h2>";
echo "<p>If you know the root password, you can create a new user for the import.</p>";

// Try some common passwords
$passwords = ['', 'root', 'password', 'admin', 'xampp'];
echo "<h2>Testing common passwords...</h2>";

foreach ($passwords as $pass) {
    $mysqli = @new mysqli('127.0.0.1', 'root', $pass);
    if (!$mysqli->connect_error) {
        echo "<p style='color: green;'>✓ SUCCESS! Password is: " . ($pass === '' ? '(empty)' : "'$pass'") . "</p>";
        $mysqli->close();
        
        // Update the config file
        $config = "<?php
// Local database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '$pass');
define('DB_NAME', '11klassniki_claude');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Environment flag
define('IS_LOCAL', true);

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Timezone
date_default_timezone_set('Europe/Moscow');
?>";
        
        file_put_contents(__DIR__ . '/config/database.local.php', $config);
        echo "<p style='color: green;'>✓ Updated database.local.php with correct password</p>";
        echo "<p><a href='/import-database.php'>Click here to retry import</a></p>";
        break;
    }
}
?>