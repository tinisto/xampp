<?php
// Start session for all pages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Bypass under construction check for local development
// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
?>