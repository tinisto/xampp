<?php
require_once __DIR__ . '/Security.php';

// Output sanitization helper
function h($string) {
    return Security::cleanOutput($string);
}

// Clean input helper
function clean($data) {
    return Security::cleanInput($data);
}

// CSRF token field helper
function csrf_field() {
    return Security::getCSRFField();
}

// CSRF token validation helper
function validate_csrf() {
    Security::requireCSRFToken();
}

// Safe redirect helper
function redirect($url, $statusCode = 302) {
    // Validate URL to prevent open redirects
    $parsed = parse_url($url);
    
    // Allow only relative URLs or URLs from the same domain
    if (!empty($parsed['host']) && $parsed['host'] !== $_SERVER['HTTP_HOST']) {
        $url = '/';
    }
    
    header('Location: ' . $url, true, $statusCode);
    exit();
}

// Error logging helper
function logError($message, $context = []) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/error_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
    
    $logMessage = "[{$timestamp}] {$message}{$contextStr}" . PHP_EOL;
    error_log($logMessage, 3, $logFile);
}

// Session helper
function getSession($key, $default = null) {
    if (!isset($_SESSION)) {
        session_start();
    }
    return $_SESSION[$key] ?? $default;
}

function setSession($key, $value) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION[$key] = $value;
}

function deleteSession($key) {
    if (!isset($_SESSION)) {
        session_start();
    }
    unset($_SESSION[$key]);
}

// Pagination helper
function getPaginationParams($totalItems, $itemsPerPage = 10) {
    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $totalPages = ceil($totalItems / $itemsPerPage);
    $startIndex = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'start_index' => $startIndex,
        'items_per_page' => $itemsPerPage,
        'total_items' => $totalItems
    ];
}

// File upload validation helper
function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large'];
    }
    
    if (!empty($allowedTypes)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }
    }
    
    return ['success' => true];
}

// JSON response helper
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Form validation helper
function validateRequired($fields, $data) {
    $errors = [];
    
    foreach ($fields as $field => $label) {
        if (empty($data[$field])) {
            $errors[$field] = "$label is required";
        }
    }
    
    return $errors;
}