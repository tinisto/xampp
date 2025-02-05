<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

define('APP_NAME', '11-классники');
define('APP_VERSION', '1.0.0');
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL']);
define('ADMIN_NAME', 'Елена Иванова');

// SMTP configuration
define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME']);
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD']);
define('SMTP_SECURITY', $_ENV['SMTP_SECURITY']);
define('SMTP_PORT', $_ENV['SMTP_PORT']);
