<?php
// This script creates a production config file
$config_content = '<?php
// Production database configuration
define("DB_HOST", "localhost");
define("DB_NAME", "eigbox_11klassniki");
define("DB_USER", "eigbox_11klassniki");
define("DB_PASS", "your_database_password"); // NEED TO UPDATE THIS

// Other settings
define("SITE_URL", "https://11klassniki.ru");
define("DEBUG_MODE", false);

// Override any environment loading
$_ENV["DB_HOST"] = DB_HOST;
$_ENV["DB_NAME"] = DB_NAME;
$_ENV["DB_USER"] = DB_USER;
$_ENV["DB_PASS"] = DB_PASS;
?>';

file_put_contents('config/prod-config.php', $config_content);
echo "Production config file created!\n";
echo "IMPORTANT: You need to update the database password in config/prod-config.php\n";
?>