<?php
/**
 * CSRF Protection Utility
 * 
 * This file provides functions for generating and validating CSRF tokens
 * to protect against Cross-Site Request Forgery attacks.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate a CSRF token
 * 
 * @return string The generated CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get the current CSRF token or generate a new one
 * 
 * @return string The CSRF token
 */
function getCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        return generateCSRFToken();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 * 
 * @param string $token The token to validate
 * @return bool True if valid, false otherwise
 */
function validateCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verify CSRF token from POST request
 * Dies with error if token is invalid
 * 
 * @param bool $ajax Whether this is an AJAX request
 */
function verifyCSRFToken($ajax = false) {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (!validateCSRFToken($token)) {
        // Log CSRF failure if security logger is available
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/security-logger.php')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security-logger.php';
            if (isset($GLOBALS['connection'])) {
                logSecurityEvent($GLOBALS['connection'], SecurityLogger::EVENT_CSRF_FAILED, [
                    'details' => [
                        'provided_token' => substr($token, 0, 10) . '...',
                        'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                        'referer' => $_SERVER['HTTP_REFERER'] ?? ''
                    ]
                ]);
            }
        }
        
        if ($ajax) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'CSRF token validation failed']);
        } else {
            http_response_code(403);
            die('CSRF token validation failed. Please refresh the page and try again.');
        }
        exit();
    }
}

/**
 * Generate CSRF token HTML input field
 * 
 * @return string HTML input field with CSRF token
 */
function csrfField() {
    $token = getCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Generate CSRF token meta tag for AJAX requests
 * 
 * @return string HTML meta tag with CSRF token
 */
function csrfMeta() {
    $token = getCSRFToken();
    return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
}

/**
 * Regenerate CSRF token (use after successful login)
 */
function regenerateCSRFToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Get JavaScript code for including CSRF token in AJAX requests
 * 
 * @return string JavaScript code
 */
function csrfAjaxSetup() {
    return <<<'JS'
<script>
// CSRF token setup for AJAX requests
(function() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) {
        // Setup for fetch API
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            options.headers = options.headers || {};
            if (options.method && options.method.toUpperCase() !== 'GET') {
                options.headers['X-CSRF-Token'] = token;
            }
            return originalFetch.call(this, url, options);
        };
        
        // Setup for XMLHttpRequest
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url) {
            const result = originalOpen.apply(this, arguments);
            if (method.toUpperCase() !== 'GET') {
                this.setRequestHeader('X-CSRF-Token', token);
            }
            return result;
        };
        
        // Setup for jQuery if available
        if (typeof jQuery !== 'undefined') {
            jQuery.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if (settings.type !== 'GET') {
                        xhr.setRequestHeader('X-CSRF-Token', token);
                    }
                }
            });
        }
    }
})();
</script>
JS;
}
?>