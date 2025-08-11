<?php
// Admin logout
session_start();

// Clear all session data
session_destroy();

// Redirect to login page
header('Location: /admin/login.php');
exit;
?>