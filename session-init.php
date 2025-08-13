<?php
// Simple session initialization without custom path
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Use PHP's default session handling
    @session_start();
}
?>