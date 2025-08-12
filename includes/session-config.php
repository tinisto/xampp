<?php
/**
 * Enhanced Session Security Configuration
 * 
 * This file sets secure session parameters and implements
 * session timeout and fingerprinting for additional security.
 */

// Set secure session parameters before session_start()
ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800); // 30 minutes

// Set secure cookie parameters
session_set_cookie_params([
    'lifetime' => 0, // Session cookie
    'path' => '/',
    'domain' => '',
    'secure' => true, // Enable when using HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes in seconds
    
    if (isset($_SESSION['last_activity'])) {
        $elapsed = time() - $_SESSION['last_activity'];
        
        if ($elapsed > $timeout) {
            // Session has timed out
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['error'] = 'Ваша сессия истекла. Пожалуйста, войдите снова.';
            header('Location: /login');
            exit();
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

/**
 * Create session fingerprint
 */
function createSessionFingerprint() {
    $fingerprint = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $fingerprint .= $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    // Don't use IP as it can change for mobile users
    return hash('sha256', $fingerprint);
}

/**
 * Validate session fingerprint
 */
function validateSessionFingerprint() {
    if (!isset($_SESSION['fingerprint'])) {
        $_SESSION['fingerprint'] = createSessionFingerprint();
    } else {
        $currentFingerprint = createSessionFingerprint();
        if ($_SESSION['fingerprint'] !== $currentFingerprint) {
            // Possible session hijacking
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['error'] = 'Обнаружена подозрительная активность. Пожалуйста, войдите снова.';
            header('Location: /login');
            exit();
        }
    }
}

/**
 * Check if user is logged in with timeout and fingerprint validation
 */
function isUserLoggedIn() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    // Check timeout
    checkSessionTimeout();
    
    // Validate fingerprint
    validateSessionFingerprint();
    
    return true;
}

/**
 * Limit concurrent sessions (optional - implement if needed)
 */
function limitConcurrentSessions($userId, $connection) {
    // Store session ID in database
    $sessionId = session_id();
    $stmt = $connection->prepare("UPDATE users SET last_session_id = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $sessionId, $userId);
        $stmt->execute();
        $stmt->close();
    }
}
?>