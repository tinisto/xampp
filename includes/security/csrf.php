<?php
/**
 * CSRF Protection System for 11klassniki
 * Protects against Cross-Site Request Forgery attacks
 */

class CSRFProtection {
    
    private static $sessionKey = 'csrf_tokens';
    private static $maxTokens = 10; // Keep last 10 tokens active
    
    /**
     * Initialize CSRF protection
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION[self::$sessionKey])) {
            $_SESSION[self::$sessionKey] = [];
        }
        
        // Clean old tokens
        self::cleanOldTokens();
    }
    
    /**
     * Generate a new CSRF token
     * @param string $form_name Optional form identifier
     * @return string CSRF token
     */
    public static function generateToken($form_name = 'default') {
        self::init();
        
        $token = bin2hex(random_bytes(32));
        $timestamp = time();
        
        $_SESSION[self::$sessionKey][$form_name] = [
            'token' => $token,
            'timestamp' => $timestamp
        ];
        
        return $token;
    }
    
    /**
     * Validate CSRF token
     * @param string $token Token to validate
     * @param string $form_name Form identifier
     * @param int $max_age Maximum age in seconds (default: 1 hour)
     * @return bool True if valid
     */
    public static function validateToken($token, $form_name = 'default', $max_age = 3600) {
        self::init();
        
        if (!isset($_SESSION[self::$sessionKey][$form_name])) {
            return false;
        }
        
        $stored = $_SESSION[self::$sessionKey][$form_name];
        
        // Check if token matches
        if (!hash_equals($stored['token'], $token)) {
            return false;
        }
        
        // Check if token is not expired
        if ((time() - $stored['timestamp']) > $max_age) {
            unset($_SESSION[self::$sessionKey][$form_name]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate and consume token (one-time use)
     * @param string $token Token to validate
     * @param string $form_name Form identifier
     * @param int $max_age Maximum age in seconds
     * @return bool True if valid
     */
    public static function validateAndConsumeToken($token, $form_name = 'default', $max_age = 3600) {
        $isValid = self::validateToken($token, $form_name, $max_age);
        
        if ($isValid) {
            // Remove token after use
            unset($_SESSION[self::$sessionKey][$form_name]);
        }
        
        return $isValid;
    }
    
    /**
     * Get HTML input field for CSRF token
     * @param string $form_name Form identifier
     * @return string HTML input field
     */
    public static function getTokenField($form_name = 'default') {
        $token = self::generateToken($form_name);
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Get token value for AJAX requests
     * @param string $form_name Form identifier
     * @return string Token value
     */
    public static function getTokenValue($form_name = 'default') {
        return self::generateToken($form_name);
    }
    
    /**
     * Check request for CSRF token
     * Dies with error if invalid
     * @param array $data $_POST or $_GET data
     * @param string $form_name Form identifier
     */
    public static function checkRequest($data, $form_name = 'default') {
        if (!isset($data['csrf_token'])) {
            self::dieWithError('CSRF token missing');
        }
        
        if (!self::validateAndConsumeToken($data['csrf_token'], $form_name)) {
            self::dieWithError('Invalid CSRF token');
        }
    }
    
    /**
     * Clean old tokens to prevent session bloat
     */
    private static function cleanOldTokens() {
        if (!isset($_SESSION[self::$sessionKey])) {
            return;
        }
        
        $tokens = $_SESSION[self::$sessionKey];
        
        // Remove expired tokens
        $current_time = time();
        foreach ($tokens as $form_name => $data) {
            if (($current_time - $data['timestamp']) > 3600) { // 1 hour
                unset($_SESSION[self::$sessionKey][$form_name]);
            }
        }
        
        // Keep only the most recent tokens if too many
        if (count($_SESSION[self::$sessionKey]) > self::$maxTokens) {
            // Sort by timestamp and keep newest
            uasort($_SESSION[self::$sessionKey], function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
            
            $_SESSION[self::$sessionKey] = array_slice($_SESSION[self::$sessionKey], 0, self::$maxTokens, true);
        }
    }
    
    /**
     * Die with CSRF error
     * @param string $message Error message
     */
    private static function dieWithError($message) {
        http_response_code(403);
        
        if (headers_sent()) {
            echo "<h1>Security Error</h1><p>{$message}</p>";
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => $message, 'code' => 403]);
        }
        
        exit;
    }
    
    /**
     * Generate meta tag for AJAX requests
     * @return string HTML meta tag
     */
    public static function getMetaTag() {
        $token = self::generateToken('ajax');
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
}

// Auto-initialize on include
CSRFProtection::init();