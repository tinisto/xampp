<?php
// Simple environment loader without Composer

// Check if we have a .env.production file
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.production')) {
    $envContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/.env.production');
    $lines = explode("\n", $envContent);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
            
            // Define constants for database
            if (strpos($key, 'DB_') === 0 && !defined($key)) {
                define($key, $value);
            }
        }
    }
} else {
    // Fallback to hardcoded values
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/minimal_env.php';
}
?>