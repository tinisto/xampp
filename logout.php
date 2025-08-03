<?php
require_once __DIR__ . '/includes/SessionManager.php';

// Logout user using centralized session management
SessionManager::logout();

// Redirect to homepage
header("Location: /");
exit();