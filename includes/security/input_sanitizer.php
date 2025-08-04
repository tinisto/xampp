<?php
/**
 * Input Validation and Sanitization for 11klassniki
 * Provides secure input handling functions
 */

class InputSanitizer {
    
    /**
     * Sanitize string input
     * @param string $input Input string
     * @param bool $allow_html Whether to allow HTML tags
     * @param array $allowed_tags Array of allowed HTML tags
     * @return string Sanitized string
     */
    public static function sanitizeString($input, $allow_html = false, $allowed_tags = []) {
        if (!is_string($input)) {
            return '';
        }
        
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        if ($allow_html && !empty($allowed_tags)) {
            // Strip tags except allowed ones
            $input = strip_tags($input, '<' . implode('><', $allowed_tags) . '>');
        } elseif (!$allow_html) {
            // Strip all HTML tags
            $input = strip_tags($input);
        }
        
        // Trim whitespace
        return trim($input);
    }
    
    /**
     * Sanitize HTML content for safe display
     * @param string $html HTML content
     * @param array $allowed_tags Allowed HTML tags
     * @return string Sanitized HTML
     */
    public static function sanitizeHTML($html, $allowed_tags = ['p', 'br', 'strong', 'em', 'u', 'a', 'ul', 'ol', 'li']) {
        if (!is_string($html)) {
            return '';
        }
        
        // Remove dangerous tags and attributes
        $html = strip_tags($html, '<' . implode('><', $allowed_tags) . '>');
        
        // Remove dangerous attributes from allowed tags
        $html = preg_replace('/(<[^>]*)(on\w+|javascript:|vbscript:|data:)([^>]*>)/i', '$1$3', $html);
        
        // Clean up links
        $html = preg_replace_callback('/<a\s+([^>]*)href\s*=\s*["\']([^"\']*)["\']([^>]*)>/i', function($matches) {
            $url = $matches[2];
            
            // Only allow http, https, and mailto links
            if (!preg_match('/^(https?:\/\/|mailto:)/i', $url)) {
                return '<a' . $matches[1] . $matches[3] . '>';
            }
            
            return $matches[0];
        }, $html);
        
        return $html;
    }
    
    /**
     * Validate and sanitize email
     * @param string $email Email address
     * @return string|false Sanitized email or false if invalid
     */
    public static function sanitizeEmail($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        
        return false;
    }
    
    /**
     * Validate and sanitize URL
     * @param string $url URL
     * @return string|false Sanitized URL or false if invalid
     */
    public static function sanitizeURL($url) {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        return false;
    }
    
    /**
     * Sanitize integer input
     * @param mixed $input Input value
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @return int|false Sanitized integer or false if invalid
     */
    public static function sanitizeInt($input, $min = null, $max = null) {
        $int = filter_var($input, FILTER_VALIDATE_INT);
        
        if ($int === false) {
            return false;
        }
        
        if ($min !== null && $int < $min) {
            return false;
        }
        
        if ($max !== null && $int > $max) {
            return false;
        }
        
        return $int;
    }
    
    /**
     * Sanitize float input
     * @param mixed $input Input value
     * @param float $min Minimum value
     * @param float $max Maximum value
     * @return float|false Sanitized float or false if invalid
     */
    public static function sanitizeFloat($input, $min = null, $max = null) {
        $float = filter_var($input, FILTER_VALIDATE_FLOAT);
        
        if ($float === false) {
            return false;
        }
        
        if ($min !== null && $float < $min) {
            return false;
        }
        
        if ($max !== null && $float > $max) {
            return false;
        }
        
        return $float;
    }
    
    /**
     * Sanitize filename for safe file operations
     * @param string $filename Original filename
     * @return string Safe filename
     */
    public static function sanitizeFilename($filename) {
        // Remove path separators
        $filename = basename($filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Prevent hidden files
        $filename = ltrim($filename, '.');
        
        // Limit length
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }
        
        return $filename;
    }
    
    /**
     * Validate password strength
     * @param string $password Password
     * @param int $min_length Minimum length
     * @return array Validation result with score and errors
     */
    public static function validatePassword($password, $min_length = 8) {
        $errors = [];
        $score = 0;
        
        if (strlen($password) < $min_length) {
            $errors[] = "Password must be at least {$min_length} characters long";
        } else {
            $score += 1;
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        } else {
            $score += 1;
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        } else {
            $score += 1;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        } else {
            $score += 1;
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        } else {
            $score += 1;
        }
        
        return [
            'valid' => empty($errors),
            'score' => $score,
            'errors' => $errors
        ];
    }
    
    /**
     * Sanitize array of inputs
     * @param array $inputs Input array
     * @param array $rules Sanitization rules
     * @return array Sanitized array
     */
    public static function sanitizeArray($inputs, $rules = []) {
        $sanitized = [];
        
        foreach ($inputs as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                
                switch ($rule['type']) {
                    case 'string':
                        $sanitized[$key] = self::sanitizeString($value, $rule['allow_html'] ?? false, $rule['allowed_tags'] ?? []);
                        break;
                    case 'email':
                        $sanitized[$key] = self::sanitizeEmail($value);
                        break;
                    case 'url':
                        $sanitized[$key] = self::sanitizeURL($value);
                        break;
                    case 'int':
                        $sanitized[$key] = self::sanitizeInt($value, $rule['min'] ?? null, $rule['max'] ?? null);
                        break;
                    case 'float':
                        $sanitized[$key] = self::sanitizeFloat($value, $rule['min'] ?? null, $rule['max'] ?? null);
                        break;
                    default:
                        $sanitized[$key] = self::sanitizeString($value);
                }
            } else {
                // Default sanitization
                $sanitized[$key] = self::sanitizeString($value);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Escape output for HTML context
     * @param string $string String to escape
     * @param string $encoding Character encoding
     * @return string Escaped string
     */
    public static function escapeHTML($string, $encoding = 'UTF-8') {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, $encoding);
    }
    
    /**
     * Escape output for JavaScript context
     * @param string $string String to escape
     * @return string Escaped string
     */
    public static function escapeJS($string) {
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    
    /**
     * Escape output for CSS context
     * @param string $string String to escape
     * @return string Escaped string
     */
    public static function escapeCSS($string) {
        return preg_replace('/[^a-zA-Z0-9\-_]/', '', $string);
    }
}