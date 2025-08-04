<?php
/**
 * Security Headers for 11klassniki
 * Implements various security headers to protect against attacks
 */

class SecurityHeaders {
    
    /**
     * Set all security headers
     */
    public static function setAll() {
        self::setCSP();
        self::setXSSProtection();
        self::setContentTypeOptions();
        self::setFrameOptions();
        self::setReferrerPolicy();
        self::setHSTS();
        self::setPermissionsPolicy();
    }
    
    /**
     * Content Security Policy
     */
    public static function setCSP() {
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tiny.cloud https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://ajax.googleapis.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: https: http:",
            "media-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests"
        ];
        
        header("Content-Security-Policy: " . implode('; ', $csp));
    }
    
    /**
     * X-XSS-Protection header
     */
    public static function setXSSProtection() {
        header("X-XSS-Protection: 1; mode=block");
    }
    
    /**
     * X-Content-Type-Options header
     */
    public static function setContentTypeOptions() {
        header("X-Content-Type-Options: nosniff");
    }
    
    /**
     * X-Frame-Options header
     */
    public static function setFrameOptions() {
        header("X-Frame-Options: DENY");
    }
    
    /**
     * Referrer-Policy header
     */
    public static function setReferrerPolicy() {
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }
    
    /**
     * Strict-Transport-Security header (HSTS)
     */
    public static function setHSTS() {
        // Only set HSTS if using HTTPS
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        }
    }
    
    /**
     * Permissions-Policy header
     */
    public static function setPermissionsPolicy() {
        $policies = [
            "geolocation=()",
            "microphone=()",
            "camera=()",
            "magnetometer=()",
            "gyroscope=()",
            "speaker=(self)",
            "fullscreen=(self)",
            "payment=()"
        ];
        
        header("Permissions-Policy: " . implode(', ', $policies));
    }
    
    /**
     * Server header removal
     */
    public static function removeServerHeader() {
        header_remove("Server");
        header_remove("X-Powered-By");
    }
    
    /**
     * Set cache control headers
     * @param int $max_age Cache max age in seconds
     * @param bool $public Whether cache is public
     */
    public static function setCacheHeaders($max_age = 3600, $public = true) {
        $visibility = $public ? 'public' : 'private';
        header("Cache-Control: {$visibility}, max-age={$max_age}");
        header("Expires: " . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
    }
    
    /**
     * Prevent caching (for sensitive pages)
     */
    public static function preventCaching() {
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
    }
    
    /**
     * Set headers for file downloads
     * @param string $filename File name
     * @param string $content_type MIME type
     */
    public static function setDownloadHeaders($filename, $content_type = 'application/octet-stream') {
        header("Content-Type: {$content_type}");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
    }
    
    /**
     * Check if request is from HTTPS
     * @return bool True if HTTPS
     */
    public static function isHTTPS() {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }
    
    /**
     * Force HTTPS redirect
     */
    public static function forceHTTPS() {
        if (!self::isHTTPS()) {
            $redirect_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: {$redirect_url}", true, 301);
            exit;
        }
    }
}

// Auto-set basic security headers
if (!headers_sent()) {
    SecurityHeaders::removeServerHeader();
}