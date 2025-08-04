<?php
/**
 * Monitoring Dashboard Route
 */

session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/monitoring/monitoring_dashboard.php';