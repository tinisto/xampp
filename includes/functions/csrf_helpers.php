<?php
/**
 * CSRF Helper Functions
 * Standardizes CSRF token implementation across all forms
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Security.php';

/**
 * Generate CSRF token HTML input field
 * @return string HTML input field with CSRF token
 */
function csrf_field() {
    $token = Security::getCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Get CSRF token for JavaScript/AJAX requests
 * @return string CSRF token
 */
function csrf_token() {
    return Security::getCSRFToken();
}

/**
 * Validate CSRF token from request
 * @return bool True if valid, false otherwise
 */
function csrf_check() {
    return Security::isValidCSRFToken();
}

/**
 * Validate CSRF token and redirect on failure
 * @param string $redirectUrl URL to redirect to on failure
 * @param string $errorMessage Error message to set in session
 */
function csrf_protect($redirectUrl = null, $errorMessage = 'Security token validation failed. Please try again.') {
    if (!csrf_check()) {
        $_SESSION['error'] = $errorMessage;
        
        if ($redirectUrl) {
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            // Redirect back to referring page or homepage
            $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
            header('Location: ' . $referrer);
            exit;
        }
    }
}

/**
 * Generate CSRF meta tag for HTML head
 * @return string HTML meta tag with CSRF token
 */
function csrf_meta() {
    $token = Security::getCSRFToken();
    return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
}

/**
 * JavaScript snippet to automatically add CSRF token to AJAX requests
 * @return string JavaScript code
 */
function csrf_ajax_setup() {
    $token = Security::getCSRFToken();
    return "
    <script>
    // Automatically add CSRF token to all AJAX requests
    document.addEventListener('DOMContentLoaded', function() {
        // jQuery setup (if available)
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                        xhr.setRequestHeader('X-CSRF-Token', '" . $token . "');
                    }
                }
            });
        }
        
        // Vanilla JavaScript fetch setup
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (options.method && !/^(GET|HEAD|OPTIONS|TRACE)$/i.test(options.method)) {
                options.headers = options.headers || {};
                options.headers['X-CSRF-Token'] = '" . $token . "';
            }
            return originalFetch(url, options);
        };
        
        // Add CSRF token to all forms automatically
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            if (form.method.toLowerCase() === 'post') {
                let csrfInput = form.querySelector('input[name=\"csrf_token\"]');
                if (!csrfInput) {
                    csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '" . $token . "';
                    form.appendChild(csrfInput);
                }
            }
        });
    });
    </script>";
}

/**
 * Check if current request has valid CSRF token (for API endpoints)
 * @return array Status array with success/error info
 */
function csrf_api_check() {
    if (!csrf_check()) {
        return [
            'success' => false,
            'error' => 'CSRF token validation failed',
            'code' => 403
        ];
    }
    
    return [
        'success' => true
    ];
}
?>