<?php
/**
 * Security Bootstrap for 11klassniki
 * Loads all security components and applies basic protections
 */

// Load security components
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/rate_limiter.php';
require_once __DIR__ . '/security_headers.php';
require_once __DIR__ . '/input_sanitizer.php';

// Load monitoring components
require_once __DIR__ . '/../monitoring/error_logger.php';
require_once __DIR__ . '/../monitoring/performance_monitor.php';

/**
 * Security Bootstrap Class
 */
class SecurityBootstrap {
    
    private static $initialized = false;
    
    /**
     * Initialize all security features
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', SecurityHeaders::isHTTPS() ? 1 : 0);
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
        
        // Set security headers
        if (!headers_sent()) {
            SecurityHeaders::setAll();
        }
        
        // Initialize CSRF protection
        CSRFProtection::init();
        
        // Initialize rate limiter
        RateLimiter::init();
        
        self::$initialized = true;
    }
    
    /**
     * Quick CSRF protection for forms
     * @param string $form_name Form identifier
     */
    public static function protectForm($form_name = 'default') {
        self::init();
        echo CSRFProtection::getTokenField($form_name);
    }
    
    /**
     * Quick rate limiting check
     * @param string $action Action identifier
     * @param int $max_attempts Maximum attempts
     * @param int $time_window Time window in seconds
     */
    public static function checkRateLimit($action, $max_attempts = 5, $time_window = 300) {
        self::init();
        RateLimiter::enforce($action, $max_attempts, $time_window);
    }
    
    /**
     * Validate POST request with CSRF and rate limiting
     * @param string $form_name Form identifier
     * @param string $action Rate limit action
     * @param int $max_attempts Maximum attempts
     * @param int $time_window Time window in seconds
     */
    public static function validatePOST($form_name = 'default', $action = null, $max_attempts = 10, $time_window = 60) {
        self::init();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }
        
        // Check rate limit if action specified
        if ($action !== null) {
            RateLimiter::enforce($action, $max_attempts, $time_window);
        }
        
        // Check CSRF token
        CSRFProtection::checkRequest($_POST, $form_name);
    }
    
    /**
     * Sanitize all POST data
     * @param array $rules Sanitization rules
     * @return array Sanitized POST data
     */
    public static function sanitizePOST($rules = []) {
        return InputSanitizer::sanitizeArray($_POST, $rules);
    }
    
    /**
     * Sanitize all GET data
     * @param array $rules Sanitization rules
     * @return array Sanitized GET data
     */
    public static function sanitizeGET($rules = []) {
        return InputSanitizer::sanitizeArray($_GET, $rules);
    }
    
    /**
     * Get safe output for HTML
     * @param string $string String to escape
     * @return string Safe HTML output
     */
    public static function out($string) {
        return InputSanitizer::escapeHTML($string);
    }
    
    /**
     * Check if user is authenticated
     * @return bool True if authenticated
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) || isset($_SESSION['username']);
    }
    
    /**
     * Require authentication
     * @param string $redirect_url URL to redirect if not authenticated
     */
    public static function requireAuth($redirect_url = '/login') {
        if (!self::isAuthenticated()) {
            header("Location: {$redirect_url}");
            exit;
        }
    }
    
    /**
     * Check if user has admin role
     * @return bool True if admin
     */
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Require admin role
     * @param string $redirect_url URL to redirect if not admin
     */
    public static function requireAdmin($redirect_url = '/') {
        self::requireAuth();
        
        if (!self::isAdmin()) {
            http_response_code(403);
            header("Location: {$redirect_url}");
            exit;
        }
    }
    
    /**
     * Log security event
     * @param string $event Event description
     * @param array $context Additional context
     */
    public static function logSecurityEvent($event, $context = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? null,
            'context' => $context
        ];
        
        $log_file = __DIR__ . '/../../logs/security.log';
        
        // Create logs directory if it doesn't exist
        $log_dir = dirname($log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
    }
}

// Auto-initialize security
SecurityBootstrap::init();