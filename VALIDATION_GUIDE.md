# Input Validation Guide for 11klassniki.ru

## Overview
A comprehensive input validation system has been implemented to protect against various security vulnerabilities and ensure data integrity.

## Input Validator Class Location
`/includes/input-validator.php`

## Available Validation Methods

### 1. Email Validation
```php
$email = InputValidator::validateEmail($input);
// Returns validated email or false
// Checks format and MX records
```

### 2. Username Validation
```php
$username = InputValidator::validateUsername($input);
// Allows: letters, numbers, underscore, dash (3-20 chars)
```

### 3. Password Validation
```php
$result = InputValidator::validatePassword($password);
// Returns: ['valid' => bool, 'message' => string]
// Requires: 8+ chars, uppercase, lowercase, number
```

### 4. Text Validation
```php
$text = InputValidator::validateText($input, $minLength, $maxLength);
// Strips tags, applies htmlspecialchars
```

### 5. HTML Content Validation
```php
$html = InputValidator::validateHTML($input, $allowedTags);
// Default allowed tags: <p><br><strong><em><u><ul><ol><li><a><blockquote><h3><h4>
// Removes dangerous attributes
```

### 6. URL Validation
```php
$url = InputValidator::validateURL($input);
// Only allows http/https URLs
```

### 7. Phone Validation (Russian format)
```php
$phone = InputValidator::validatePhone($input);
// Returns normalized +7 format
```

### 8. Integer Validation
```php
$int = InputValidator::validateInt($input, $min, $max);
```

### 9. Date Validation
```php
$date = InputValidator::validateDate($input, 'Y-m-d');
```

### 10. File Upload Validation
```php
$result = InputValidator::validateFile($_FILES['upload'], $allowedTypes, $maxSize);
// Returns: ['valid' => bool, 'message' => string]
```

### 11. Search Query Validation
```php
$query = InputValidator::validateSearchQuery($input);
// Removes dangerous characters, limits to 100 chars
```

### 12. URL Slug Validation
```php
$slug = InputValidator::validateSlug($input);
// Only lowercase letters, numbers, hyphens
```

## Batch Validation
```php
$rules = [
    'email' => ['type' => 'email', 'required' => true],
    'name' => ['type' => 'text', 'required' => true, 'min' => 2, 'max' => 100],
    'age' => ['type' => 'int', 'min' => 18, 'max' => 120]
];

$result = InputValidator::validateBatch($_POST, $rules);
// Returns: ['valid' => bool, 'data' => array, 'errors' => array]
```

## Quick Helper Function
```php
$value = validate_input($input, 'email');
$value = validate_input($input, 'text', ['min' => 5, 'max' => 200]);
```

## Files Already Updated

1. **Login Process** (`/pages/login/login_process_simple.php`)
   - Email validation with domain check
   - Password validation
   - Redirect URL validation

2. **Registration Process** (`/pages/registration/registration_process.php`)
   - Email validation
   - Strong password requirements
   - Timezone and occupation validation

3. **Search Process** (`/pages/search/search-process.php`)
   - Search query sanitization
   - Special character removal

4. **Comment System** (`/comments/process_comments.php`)
   - Comment text validation (1-2000 chars)
   - Entity type validation
   - ID validation

## Usage Example
See `/includes/validation-example.php` for comprehensive examples including:
- Form processing with validation
- Error display
- Old value retention
- CSRF protection integration

## Security Benefits
1. **SQL Injection Prevention**: All inputs are properly validated/sanitized
2. **XSS Prevention**: HTML content is filtered, text is escaped
3. **Path Traversal Prevention**: Filenames are sanitized
4. **Data Integrity**: Ensures data meets expected format/length
5. **User Experience**: Clear error messages in Russian

## Next Steps for Developers
When creating new forms or processing user input:
1. Always include `/includes/input-validator.php`
2. Use appropriate validation method for each input type
3. Display validation errors to users
4. Never trust user input, even after validation
5. Use prepared statements for database queries

## Testing
To test validation:
1. Try submitting forms with invalid data
2. Check error messages are displayed
3. Verify data is properly sanitized in database
4. Test edge cases (very long input, special characters, etc.)