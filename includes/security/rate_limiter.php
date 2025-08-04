<?php
/**
 * Rate Limiting System for 11klassniki
 * Protects against brute force attacks and spam
 */

class RateLimiter {
    
    private static $sessionKey = 'rate_limits';
    
    /**
     * Initialize rate limiter
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION[self::$sessionKey])) {
            $_SESSION[self::$sessionKey] = [];
        }
        
        self::cleanOldAttempts();
    }
    
    /**
     * Check if action is rate limited
     * @param string $action Action identifier (e.g., 'login', 'comment')
     * @param int $max_attempts Maximum attempts allowed
     * @param int $time_window Time window in seconds
     * @param string $identifier Optional identifier (IP, user ID, etc.)
     * @return bool True if rate limited
     */
    public static function isRateLimited($action, $max_attempts = 5, $time_window = 300, $identifier = null) {
        self::init();
        
        if ($identifier === null) {
            $identifier = self::getClientIdentifier();
        }
        
        $key = $action . '_' . $identifier;
        $current_time = time();
        
        if (!isset($_SESSION[self::$sessionKey][$key])) {
            return false;
        }
        
        $attempts = $_SESSION[self::$sessionKey][$key];
        
        // Count attempts within time window
        $recent_attempts = 0;
        foreach ($attempts as $timestamp) {
            if (($current_time - $timestamp) <= $time_window) {
                $recent_attempts++;
            }
        }
        
        return $recent_attempts >= $max_attempts;
    }
    
    /**
     * Record an attempt
     * @param string $action Action identifier
     * @param string $identifier Optional identifier
     */
    public static function recordAttempt($action, $identifier = null) {
        self::init();
        
        if ($identifier === null) {
            $identifier = self::getClientIdentifier();
        }
        
        $key = $action . '_' . $identifier;
        $current_time = time();
        
        if (!isset($_SESSION[self::$sessionKey][$key])) {
            $_SESSION[self::$sessionKey][$key] = [];
        }
        
        $_SESSION[self::$sessionKey][$key][] = $current_time;
        
        // Keep only recent attempts (last 50)
        if (count($_SESSION[self::$sessionKey][$key]) > 50) {
            $_SESSION[self::$sessionKey][$key] = array_slice($_SESSION[self::$sessionKey][$key], -50);
        }
    }
    
    /**
     * Check and record attempt in one call
     * @param string $action Action identifier
     * @param int $max_attempts Maximum attempts allowed
     * @param int $time_window Time window in seconds
     * @param string $identifier Optional identifier
     * @return bool True if allowed (not rate limited)
     */
    public static function checkAndRecord($action, $max_attempts = 5, $time_window = 300, $identifier = null) {
        if (self::isRateLimited($action, $max_attempts, $time_window, $identifier)) {
            return false;
        }
        
        self::recordAttempt($action, $identifier);
        return true;
    }
    
    /**
     * Clear attempts for an action
     * @param string $action Action identifier
     * @param string $identifier Optional identifier
     */
    public static function clearAttempts($action, $identifier = null) {
        self::init();
        
        if ($identifier === null) {
            $identifier = self::getClientIdentifier();
        }
        
        $key = $action . '_' . $identifier;
        unset($_SESSION[self::$sessionKey][$key]);
    }
    
    /**
     * Get remaining time until rate limit expires
     * @param string $action Action identifier
     * @param int $time_window Time window in seconds
     * @param string $identifier Optional identifier
     * @return int Seconds remaining
     */
    public static function getTimeRemaining($action, $time_window = 300, $identifier = null) {
        self::init();
        
        if ($identifier === null) {
            $identifier = self::getClientIdentifier();
        }
        
        $key = $action . '_' . $identifier;
        
        if (!isset($_SESSION[self::$sessionKey][$key])) {
            return 0;
        }
        
        $attempts = $_SESSION[self::$sessionKey][$key];
        
        if (empty($attempts)) {
            return 0;
        }
        
        $latest_attempt = max($attempts);
        $elapsed = time() - $latest_attempt;
        
        return max(0, $time_window - $elapsed);
    }
    
    /**
     * Get client identifier (IP + User Agent hash)
     * @return string Client identifier
     */
    private static function getClientIdentifier() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Use first 8 chars of hash for storage efficiency
        return substr(hash('sha256', $ip . $user_agent), 0, 8);
    }
    
    /**
     * Clean old attempts to prevent session bloat
     */
    private static function cleanOldAttempts() {
        if (!isset($_SESSION[self::$sessionKey])) {
            return;
        }
        
        $current_time = time();
        $max_age = 3600; // 1 hour
        
        foreach ($_SESSION[self::$sessionKey] as $key => $attempts) {
            // Remove attempts older than max_age
            $_SESSION[self::$sessionKey][$key] = array_filter($attempts, function($timestamp) use ($current_time, $max_age) {
                return ($current_time - $timestamp) <= $max_age;
            });
            
            // Remove empty arrays
            if (empty($_SESSION[self::$sessionKey][$key])) {
                unset($_SESSION[self::$sessionKey][$key]);
            }
        }
    }
    
    /**
     * Enforce rate limit - die if exceeded
     * @param string $action Action identifier
     * @param int $max_attempts Maximum attempts allowed
     * @param int $time_window Time window in seconds
     * @param string $message Custom error message
     */
    public static function enforce($action, $max_attempts = 5, $time_window = 300, $message = null) {
        if (!self::checkAndRecord($action, $max_attempts, $time_window)) {
            $remaining = self::getTimeRemaining($action, $time_window);
            
            if ($message === null) {
                $message = "Too many attempts. Please try again in " . ceil($remaining / 60) . " minutes.";
            }
            
            http_response_code(429);
            
            if (headers_sent()) {
                echo "<h1>Rate Limited</h1><p>{$message}</p>";
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => $message,
                    'code' => 429,
                    'retry_after' => $remaining
                ]);
            }
            
            exit;
        }
    }
}

// Auto-initialize on include
RateLimiter::init();