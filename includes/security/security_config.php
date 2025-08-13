<?php
/**
 * Security Configuration
 * Centralizes all security settings and functions
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Security headers
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Prevent XSS
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    
    // Force HTTPS in production
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Input sanitization functions
function sanitizeInput($input, $type = 'string') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $input);
    }
    
    $input = trim($input);
    
    switch ($type) {
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
            
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
            
        case 'html':
            // Allow some HTML tags for content
            $allowed_tags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote>';
            return strip_tags($input, $allowed_tags);
            
        case 'sql':
            // For SQL identifiers (table names, column names)
            return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
            
        default:
            // Remove all HTML and PHP tags
            $input = strip_tags($input);
            // Convert special characters to HTML entities
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

// Validate input
function validateInput($input, $type, $options = []) {
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
            
        case 'url':
            return filter_var($input, FILTER_VALIDATE_URL) !== false;
            
        case 'int':
            $options = array_merge(['min' => PHP_INT_MIN, 'max' => PHP_INT_MAX], $options);
            $filtered = filter_var($input, FILTER_VALIDATE_INT, [
                'options' => [
                    'min_range' => $options['min'],
                    'max_range' => $options['max']
                ]
            ]);
            return $filtered !== false;
            
        case 'length':
            $len = mb_strlen($input);
            $min = $options['min'] ?? 0;
            $max = $options['max'] ?? PHP_INT_MAX;
            return $len >= $min && $len <= $max;
            
        case 'regex':
            return preg_match($options['pattern'], $input) === 1;
            
        default:
            return !empty($input);
    }
}

// CSRF Protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// SQL Injection Prevention - Prepared Statement Helper
function secureQuery($connection, $query, $params = [], $types = '') {
    if (empty($params)) {
        return mysqli_query($connection, $query);
    }
    
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($connection));
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

// Password Security
function securePassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// File Upload Security
function validateFileUpload($file, $options = []) {
    $allowed_types = $options['types'] ?? ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = $options['max_size'] ?? 5 * 1024 * 1024; // 5MB default
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed'];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large'];
    }
    
    // Check file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];
    
    if (!isset($allowed_mimes[$ext]) || $mime !== $allowed_mimes[$ext]) {
        return ['success' => false, 'error' => 'Invalid file content'];
    }
    
    return ['success' => true];
}

// Rate Limiting
function checkRateLimit($identifier, $max_attempts = 5, $window = 300) {
    $key = 'rate_limit_' . $identifier;
    $attempts = $_SESSION[$key] ?? 0;
    $first_attempt = $_SESSION[$key . '_time'] ?? time();
    
    if (time() - $first_attempt > $window) {
        // Reset the window
        $_SESSION[$key] = 1;
        $_SESSION[$key . '_time'] = time();
        return true;
    }
    
    if ($attempts >= $max_attempts) {
        return false;
    }
    
    $_SESSION[$key] = $attempts + 1;
    return true;
}

// Security configuration loaded - headers set by template engine
?>