<?php
require_once __DIR__ . '/Security.php';

// Automatically validate CSRF tokens for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Whitelist certain endpoints that don't need CSRF protection
    $csrfWhitelist = [
        '/api/', // API endpoints might use different authentication
        '/webhook/', // External webhooks
    ];
    
    $requestUri = $_SERVER['REQUEST_URI'];
    $skipCsrf = false;
    
    foreach ($csrfWhitelist as $path) {
        if (strpos($requestUri, $path) === 0) {
            $skipCsrf = true;
            break;
        }
    }
    
    if (!$skipCsrf && !Security::isValidCSRFToken()) {
        http_response_code(403);
        die('CSRF token validation failed. Please refresh the page and try again.');
    }
}

// Function to add CSRF token to all forms automatically
function addCSRFToForms($html) {
    $csrfField = Security::getCSRFField();
    
    // Add CSRF field to all forms
    $pattern = '/<form[^>]*method=["\']post["\'][^>]*>/i';
    $replacement = '$0' . "\n" . $csrfField;
    
    return preg_replace($pattern, $replacement, $html);
}

// Start output buffering to automatically add CSRF tokens to forms
if (!headers_sent()) {
    ob_start(function($buffer) {
        if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') !== false) {
            return addCSRFToForms($buffer);
        }
        return $buffer;
    });
}