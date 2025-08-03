<?php
/**
 * Performance optimization utilities
 */

/**
 * Enable output compression
 */
function enable_compression() {
    if (extension_loaded('zlib') && !ob_get_level()) {
        ob_start('ob_gzhandler');
    }
}

/**
 * Set cache headers for static content
 * 
 * @param int $seconds Cache duration in seconds
 */
function set_cache_headers($seconds = 3600) {
    header('Cache-Control: public, max-age=' . $seconds);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $seconds) . ' GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(__FILE__)) . ' GMT');
}

/**
 * Minify HTML output
 * 
 * @param string $html HTML content
 * @return string Minified HTML
 */
function minify_html($html) {
    // Remove HTML comments (except IE conditional comments)
    $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
    
    // Remove whitespace between tags
    $html = preg_replace('/>\s+</', '><', $html);
    
    // Remove extra whitespace
    $html = preg_replace('/\s+/', ' ', $html);
    
    return trim($html);
}

/**
 * Database connection pool (simple implementation)
 */
class DBPool {
    private static $pools = [];
    
    public static function getConnection($host, $user, $pass, $db) {
        $key = md5($host . $user . $db);
        
        if (!isset(self::$pools[$key]) || !self::$pools[$key]->ping()) {
            self::$pools[$key] = new mysqli($host, $user, $pass, $db);
            if (self::$pools[$key]->connect_error) {
                throw new Exception("Database connection failed: " . self::$pools[$key]->connect_error);
            }
            self::$pools[$key]->set_charset('utf8mb4');
        }
        
        return self::$pools[$key];
    }
}

/**
 * Simple asset versioning for cache busting
 * 
 * @param string $asset Asset path
 * @return string Asset path with version parameter
 */
function versioned_asset($asset) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $asset;
    if (file_exists($fullPath)) {
        $version = filemtime($fullPath);
        $separator = (strpos($asset, '?') !== false) ? '&' : '?';
        return $asset . $separator . 'v=' . $version;
    }
    return $asset;
}

/**
 * Lazy loading image helper
 * 
 * @param string $src Image source
 * @param string $alt Alt text
 * @param string $class CSS classes
 * @return string Image HTML with lazy loading
 */
function lazy_image($src, $alt = '', $class = '') {
    return sprintf(
        '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="%s" alt="%s" class="lazy %s" loading="lazy">',
        htmlspecialchars($src),
        htmlspecialchars($alt),
        htmlspecialchars($class)
    );
}

/**
 * Generate critical CSS inline
 * 
 * @param array $selectors Critical CSS selectors
 * @return string CSS styles
 */
function critical_css($selectors) {
    $css = '<style>';
    foreach ($selectors as $selector => $properties) {
        $css .= $selector . '{';
        foreach ($properties as $prop => $value) {
            $css .= $prop . ':' . $value . ';';
        }
        $css .= '}';
    }
    $css .= '</style>';
    return $css;
}