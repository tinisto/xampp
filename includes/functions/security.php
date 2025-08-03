<?php
/**
 * Security helper functions
 */

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 * 
 * @param string $input Input data
 * @param string $type Type of sanitization
 * @return string Sanitized data
 */
function sanitize_input($input, $type = 'string') {
    $input = trim($input);
    
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'html':
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        default:
            return htmlspecialchars(strip_tags($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

/**
 * Validate input data
 * 
 * @param string $input Input data
 * @param string $type Type of validation
 * @return bool True if valid
 */
function validate_input($input, $type = 'string') {
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        case 'url':
            return filter_var($input, FILTER_VALIDATE_URL) !== false;
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT) !== false;
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
        case 'username':
            return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $input);
        case 'slug':
            return preg_match('/^[a-zA-Z0-9_-]+$/', $input);
        default:
            return !empty(trim($input));
    }
}

/**
 * Hash password securely
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 * 
 * @param string $password Plain text password
 * @param string $hash Hashed password
 * @return bool True if password matches
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Rate limiting (simple file-based)
 * 
 * @param string $key Unique key (e.g., IP address)
 * @param int $limit Number of requests allowed
 * @param int $window Time window in seconds
 * @return bool True if request is allowed
 */
function rate_limit($key, $limit = 10, $window = 60) {
    $file = $_SERVER['DOCUMENT_ROOT'] . '/cache/rate_limit_' . md5($key);
    
    $requests = [];
    if (file_exists($file)) {
        $requests = json_decode(file_get_contents($file), true) ?: [];
    }
    
    $now = time();
    $requests = array_filter($requests, function($timestamp) use ($now, $window) {
        return ($now - $timestamp) < $window;
    });
    
    if (count($requests) >= $limit) {
        return false;
    }
    
    $requests[] = $now;
    file_put_contents($file, json_encode($requests));
    
    return true;
}

/**
 * Log security event
 * 
 * @param string $event Event description
 * @param string $level Severity level
 */
function log_security_event($event, $level = 'INFO') {
    $log_entry = sprintf(
        "[%s] %s: %s (IP: %s, User-Agent: %s)\n",
        date('Y-m-d H:i:s'),
        $level,
        $event,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    );
    
    error_log($log_entry, 3, $_SERVER['DOCUMENT_ROOT'] . '/logs/security.log');
}

/**
 * Check if request is from bot/crawler
 * 
 * @return bool True if bot detected
 */
function is_bot() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $bot_patterns = [
        'bot', 'crawler', 'spider', 'scraper', 'googlebot', 'bingbot', 
        'yandexbot', 'slurp', 'duckduckbot', 'baiduspider'
    ];
    
    foreach ($bot_patterns as $pattern) {
        if (stripos($user_agent, $pattern) !== false) {
            return true;
        }
    }
    
    return false;
}