<?php
class Security {
    private static $csrfTokenName = 'csrf_token';
    private static $csrfTokenExpiry = 3600; // 1 hour
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::$csrfTokenName] = [
            'token' => $token,
            'expiry' => time() + self::$csrfTokenExpiry
        ];
        
        return $token;
    }
    
    public static function getCSRFToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION[self::$csrfTokenName]) || 
            $_SESSION[self::$csrfTokenName]['expiry'] < time()) {
            return self::generateCSRFToken();
        }
        
        return $_SESSION[self::$csrfTokenName]['token'];
    }
    
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION[self::$csrfTokenName])) {
            return false;
        }
        
        $sessionToken = $_SESSION[self::$csrfTokenName];
        
        if ($sessionToken['expiry'] < time()) {
            unset($_SESSION[self::$csrfTokenName]);
            return false;
        }
        
        return hash_equals($sessionToken['token'], $token);
    }
    
    public static function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    public static function cleanOutput($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
    
    public static function sanitizeFileName($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        $filename = preg_replace('/\.+/', '.', $filename);
        return $filename;
    }
    
    public static function generateSecurePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        return substr(str_shuffle($chars), 0, $length);
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public static function isValidCSRFToken() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }
        
        $token = $_POST[self::$csrfTokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return self::validateCSRFToken($token);
    }
    
    public static function requireCSRFToken() {
        if (!self::isValidCSRFToken()) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
    
    public static function getCSRFField() {
        $token = self::getCSRFToken();
        return '<input type="hidden" name="' . self::$csrfTokenName . '" value="' . $token . '">';
    }
}