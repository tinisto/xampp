<?php
// check_under_construction.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

if (!function_exists('includeEnvironmentConfig')) {
    function includeEnvironmentConfig()
    {
        // Check if the function has already been called
        static $called = false;

        if ($called) {
            return;
        }

        $called = true;

        // Get the environment from the .env file
        $environment = $_ENV['APP_ENV'] ?: 'under_construction'; // Default to 'under_construction' if APP_ENV is not set

        switch ($environment) {
            case "production":
                include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php'; // Your production configuration
                break;
            case "under_construction":
                include $_SERVER['DOCUMENT_ROOT'] . '/config/config_under_construction.php'; // Your under construction configuration
                break;
            default:
                include $_SERVER['DOCUMENT_ROOT'] . '/config/config_local.php'; // Your local configuration
                break;
        }
    }
}

includeEnvironmentConfig();

if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'under_construction') {
    // Redirect to the maintenance page and stop further execution
    header('Location: /maintenance.php');
    exit();
}

// Include database connection only if not in "under construction" mode
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/session_util.php';
