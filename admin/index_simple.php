<?php
// Simple admin index backup
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login');
    exit;
}

// Redirect to comprehensive dashboard
header('Location: /admin/dashboard.php');
exit;
?>