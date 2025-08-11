<?php
/**
 * Security Headers Configuration
 * 
 * This file sets important HTTP security headers to protect against
 * various attacks like XSS, clickjacking, MIME sniffing, etc.
 * 
 * Include this file at the beginning of your application entry points.
 */

// Prevent clickjacking attacks by denying the page from being embedded in frames
header('X-Frame-Options: SAMEORIGIN');

// Prevent MIME type sniffing
header('X-Content-Type-Options: nosniff');

// Enable XSS filter in browsers (deprecated but still useful for older browsers)
header('X-XSS-Protection: 1; mode=block');

// Referrer Policy - controls how much referrer information is sent
header('Referrer-Policy: strict-origin-when-cross-origin');

// Permissions Policy (formerly Feature Policy) - control browser features
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Content Security Policy - helps prevent XSS and other code injection attacks
// This is a basic CSP - adjust based on your needs
$csp = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com https://maxcdn.bootstrapcdn.com https://www.googletagmanager.com https://www.google-analytics.com https://mc.yandex.ru https://yandex.ru https://share.yandex.net",
    "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://maxcdn.bootstrapcdn.com https://use.fontawesome.com",
    "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://use.fontawesome.com data:",
    "img-src 'self' data: https: blob:",
    "connect-src 'self' https://mc.yandex.ru https://www.google-analytics.com",
    "frame-src 'self' https://share.yandex.net",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'self'"
];

header('Content-Security-Policy: ' . implode('; ', $csp));

// Strict Transport Security - force HTTPS (only enable on HTTPS sites)
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    // max-age=31536000 equals one year
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Prevent the browser from DNS prefetching
header('X-DNS-Prefetch-Control: off');

// Remove PHP version from headers
header_remove('X-Powered-By');

// Set secure cookie parameters if not already set
if (session_status() === PHP_SESSION_NONE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

/**
 * Optional: Add CORS headers if needed
 * Uncomment and modify as needed for your API endpoints
 */
/*
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: https://yourdomain.com');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
    exit(0);
}
*/

/**
 * Security functions
 */

/**
 * Generate a secure random token
 * 
 * @param int $length Length of the token
 * @return string
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Sanitize output to prevent XSS
 * 
 * @param string $data Data to sanitize
 * @return string
 */
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Validate and sanitize input
 * 
 * @param mixed $data Input data
 * @param string $type Type of validation
 * @return mixed Sanitized data or false if invalid
 */
function validateInput($data, $type = 'string') {
    $data = trim($data);
    $data = stripslashes($data);
    
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL);
        case 'string':
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        default:
            return false;
    }
}
?>