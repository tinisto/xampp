<?php
/**
 * Input Validation Library
 * Provides comprehensive input validation and sanitization functions
 */

class InputValidator {
    
    /**
     * Validate and sanitize email
     */
    public static function validateEmail($email) {
        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Additional validation - check domain
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            return false;
        }
        
        return $email;
    }
    
    /**
     * Validate username
     */
    public static function validateUsername($username) {
        $username = trim($username);
        
        // Allow only letters, numbers, underscore, dash
        if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username)) {
            return false;
        }
        
        return $username;
    }
    
    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        // At least 8 characters
        if (strlen($password) < 8) {
            return ['valid' => false, 'message' => 'Пароль должен содержать минимум 8 символов'];
        }
        
        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return ['valid' => false, 'message' => 'Пароль должен содержать хотя бы одну заглавную букву'];
        }
        
        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return ['valid' => false, 'message' => 'Пароль должен содержать хотя бы одну строчную букву'];
        }
        
        // At least one number
        if (!preg_match('/[0-9]/', $password)) {
            return ['valid' => false, 'message' => 'Пароль должен содержать хотя бы одну цифру'];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    
    /**
     * Validate and sanitize text input
     */
    public static function validateText($text, $minLength = 1, $maxLength = 1000) {
        $text = trim($text);
        $text = strip_tags($text);
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        $length = mb_strlen($text);
        
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }
        
        return $text;
    }
    
    /**
     * Validate and sanitize HTML content (for posts/comments)
     */
    public static function validateHTML($html, $allowedTags = null) {
        if ($allowedTags === null) {
            // Default allowed tags
            $allowedTags = '<p><br><strong><em><u><ul><ol><li><a><blockquote><h3><h4>';
        }
        
        $html = trim($html);
        $html = strip_tags($html, $allowedTags);
        
        // Remove dangerous attributes
        $html = preg_replace('/<(\w+)([^>]*)(on\w+)=[^>]*>/i', '<$1$2>', $html);
        $html = preg_replace('/<(\w+)([^>]*)(javascript:)[^>]*>/i', '<$1$2>', $html);
        
        return $html;
    }
    
    /**
     * Validate URL
     */
    public static function validateURL($url) {
        $url = trim($url);
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Only allow http and https
        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'], ['http', 'https'])) {
            return false;
        }
        
        return $url;
    }
    
    /**
     * Validate phone number (Russian format)
     */
    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Russian phone format
        if (!preg_match('/^(\+7|8|7)?[\s-]?\(?[0-9]{3}\)?[\s-]?[0-9]{3}[\s-]?[0-9]{2}[\s-]?[0-9]{2}$/', $phone)) {
            return false;
        }
        
        // Normalize to +7 format
        $phone = preg_replace('/^8/', '+7', $phone);
        if (strpos($phone, '+') !== 0) {
            $phone = '+7' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Validate integer
     */
    public static function validateInt($value, $min = null, $max = null) {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            return false;
        }
        
        $value = (int) $value;
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return $value;
    }
    
    /**
     * Validate date
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date ? $date : false;
    }
    
    /**
     * Validate file upload
     */
    public static function validateFile($file, $allowedTypes = [], $maxSize = 5242880) { // 5MB default
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'message' => 'Ошибка загрузки файла'];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'message' => 'Файл слишком большой'];
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                return ['valid' => false, 'message' => 'Недопустимый тип файла'];
            }
        }
        
        // Check for PHP in filename
        if (preg_match('/\.php/i', $file['name'])) {
            return ['valid' => false, 'message' => 'Недопустимое имя файла'];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    
    /**
     * Sanitize filename
     */
    public static function sanitizeFilename($filename) {
        // Remove any path info
        $filename = basename($filename);
        
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Ensure it doesn't start with a dot
        $filename = ltrim($filename, '.');
        
        return $filename;
    }
    
    /**
     * Validate search query
     */
    public static function validateSearchQuery($query) {
        $query = trim($query);
        
        // Remove potentially dangerous characters
        $query = preg_replace('/[<>\"\'%;()&+]/', '', $query);
        
        // Limit length
        if (mb_strlen($query) > 100) {
            $query = mb_substr($query, 0, 100);
        }
        
        return $query;
    }
    
    /**
     * Validate slug (URL-friendly string)
     */
    public static function validateSlug($slug) {
        $slug = trim($slug);
        
        // Only allow lowercase letters, numbers, and hyphens
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            return false;
        }
        
        // No double hyphens
        if (strpos($slug, '--') !== false) {
            return false;
        }
        
        // Not starting or ending with hyphen
        if (substr($slug, 0, 1) === '-' || substr($slug, -1) === '-') {
            return false;
        }
        
        return $slug;
    }
    
    /**
     * Batch validate multiple inputs
     */
    public static function validateBatch($inputs, $rules) {
        $errors = [];
        $validated = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($inputs[$field]) && isset($rule['required']) && $rule['required']) {
                $errors[$field] = 'Поле обязательно для заполнения';
                continue;
            }
            
            if (!isset($inputs[$field])) {
                continue;
            }
            
            $value = $inputs[$field];
            $isValid = true;
            
            switch ($rule['type']) {
                case 'email':
                    $result = self::validateEmail($value);
                    if ($result === false) {
                        $errors[$field] = 'Неверный формат email';
                        $isValid = false;
                    } else {
                        $validated[$field] = $result;
                    }
                    break;
                    
                case 'text':
                    $min = $rule['min'] ?? 1;
                    $max = $rule['max'] ?? 1000;
                    $result = self::validateText($value, $min, $max);
                    if ($result === false) {
                        $errors[$field] = "Длина должна быть от $min до $max символов";
                        $isValid = false;
                    } else {
                        $validated[$field] = $result;
                    }
                    break;
                    
                case 'int':
                    $min = $rule['min'] ?? null;
                    $max = $rule['max'] ?? null;
                    $result = self::validateInt($value, $min, $max);
                    if ($result === false) {
                        $errors[$field] = 'Должно быть целым числом';
                        $isValid = false;
                    } else {
                        $validated[$field] = $result;
                    }
                    break;
                    
                case 'password':
                    $result = self::validatePassword($value);
                    if (!$result['valid']) {
                        $errors[$field] = $result['message'];
                        $isValid = false;
                    } else {
                        $validated[$field] = $value;
                    }
                    break;
                    
                default:
                    $validated[$field] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
        
        return [
            'valid' => empty($errors),
            'data' => $validated,
            'errors' => $errors
        ];
    }
}

// Helper function for quick validation
function validate_input($value, $type = 'text', $options = []) {
    switch ($type) {
        case 'email':
            return InputValidator::validateEmail($value);
        case 'text':
            $min = $options['min'] ?? 1;
            $max = $options['max'] ?? 1000;
            return InputValidator::validateText($value, $min, $max);
        case 'int':
            $min = $options['min'] ?? null;
            $max = $options['max'] ?? null;
            return InputValidator::validateInt($value, $min, $max);
        case 'html':
            $tags = $options['tags'] ?? null;
            return InputValidator::validateHTML($value, $tags);
        case 'url':
            return InputValidator::validateURL($value);
        case 'phone':
            return InputValidator::validatePhone($value);
        case 'search':
            return InputValidator::validateSearchQuery($value);
        default:
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
?>