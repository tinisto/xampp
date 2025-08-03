<?php
/**
 * Override database connection to force new database
 * This file replaces the cached connection with the new one
 */

// Force override of database constants before they get cached
if (!defined('DB_HOST_OVERRIDE')) {
    define('DB_HOST_OVERRIDE', '11klassnikiru67871.ipagemysql.com');
    define('DB_USER_OVERRIDE', 'admin_claude');
    define('DB_PASS_OVERRIDE', 'W4eZ!#9uwLmrMay');
    define('DB_NAME_OVERRIDE', '11klassniki_claude');
}

// Override the global connection variable
if (!isset($GLOBALS['connection_override_done'])) {
    // Create new connection with correct database
    $connection = new mysqli(
        DB_HOST_OVERRIDE,
        DB_USER_OVERRIDE,
        DB_PASS_OVERRIDE,
        DB_NAME_OVERRIDE
    );
    
    // Check connection
    if ($connection->connect_error) {
        // Fallback to old database if new one fails
        $connection = new mysqli(
            '11klassnikiru67871.ipagemysql.com',
            '11klone_user',
            'K8HqqBV3hTf4mha',
            '11klassniki_ru'
        );
    } else {
        // Set charset for new connection
        $connection->set_charset('utf8mb4');
        $GLOBALS['connection_override_done'] = true;
    }
}
?>