<?php
/**
 * Environment Configuration Loader
 * Loads environment variables from .env file safely
 */

class Environment {
    private static $loaded = false;
    private static $config = [];
    
    /**
     * Load environment variables from .env file
     */
    public static function load($envPath = null) {
        if (self::$loaded) {
            return;
        }
        
        if ($envPath === null) {
            $envPath = $_SERVER['DOCUMENT_ROOT'] . '/.env';
        }
        
        // If .env doesn't exist, try to load from system environment
        if (!file_exists($envPath)) {
            self::loadFromSystem();
            self::$loaded = true;
            return;
        }
        
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            self::$config[$key] = $value;
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
        
        self::$loaded = true;
    }
    
    /**
     * Load from system environment variables
     */
    private static function loadFromSystem() {
        // Load critical environment variables if available
        $envVars = [
            'RECAPTCHA_SECRET_KEY',
            'RECAPTCHA_SITE_KEY',
            'DB_HOST',
            'DB_NAME',
            'DB_USER',
            'DB_PASS',
            'CSRF_SECRET',
            'SESSION_SECRET',
            'DEBUG_MODE'
        ];
        
        foreach ($envVars as $var) {
            $value = getenv($var);
            if ($value !== false) {
                self::$config[$var] = $value;
            }
        }
    }
    
    /**
     * Get environment variable
     */
    public static function get($key, $default = null) {
        self::load();
        
        // Try from loaded config first
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        
        // Try from $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Try from getenv
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        return $default;
    }
    
    /**
     * Get required environment variable (throws error if not found)
     */
    public static function getRequired($key) {
        $value = self::get($key);
        if ($value === null) {
            throw new Exception("Required environment variable '$key' not found");
        }
        return $value;
    }
    
    /**
     * Check if we're in debug mode
     */
    public static function isDebug() {
        return self::get('DEBUG_MODE', 'false') === 'true';
    }
    
    /**
     * Get database configuration
     */
    public static function getDatabaseConfig() {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'name' => self::getRequired('DB_NAME'),
            'user' => self::getRequired('DB_USER'),
            'pass' => self::getRequired('DB_PASS')
        ];
    }
    
    /**
     * Get reCAPTCHA configuration
     */
    public static function getRecaptchaConfig() {
        return [
            'site_key' => self::getRequired('RECAPTCHA_SITE_KEY'),
            'secret_key' => self::getRequired('RECAPTCHA_SECRET_KEY')
        ];
    }
}

// Auto-load environment on include
Environment::load();
?>