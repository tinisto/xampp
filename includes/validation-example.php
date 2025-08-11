<?php
/**
 * Example of how to use the InputValidator class
 * This demonstrates best practices for form validation
 */

// Include the validator
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/input-validator.php';

// Example 1: Validate a contact form
function validateContactForm($postData) {
    $rules = [
        'name' => [
            'type' => 'text',
            'required' => true,
            'min' => 2,
            'max' => 100
        ],
        'email' => [
            'type' => 'email',
            'required' => true
        ],
        'message' => [
            'type' => 'text',
            'required' => true,
            'min' => 10,
            'max' => 1000
        ],
        'phone' => [
            'type' => 'phone',
            'required' => false
        ]
    ];
    
    return InputValidator::validateBatch($postData, $rules);
}

// Example 2: Validate a post/article form
function validatePostForm($postData) {
    $validated = [];
    $errors = [];
    
    // Title validation
    $title = InputValidator::validateText($postData['title'] ?? '', 5, 200);
    if (!$title) {
        $errors['title'] = 'Заголовок должен быть от 5 до 200 символов';
    } else {
        $validated['title'] = $title;
    }
    
    // Content validation (allow HTML)
    $content = InputValidator::validateHTML($postData['content'] ?? '');
    if (mb_strlen($content) < 50) {
        $errors['content'] = 'Содержание должно быть не менее 50 символов';
    } else {
        $validated['content'] = $content;
    }
    
    // Category validation
    $categoryId = InputValidator::validateInt($postData['category_id'] ?? '', 1, 1000);
    if (!$categoryId) {
        $errors['category_id'] = 'Выберите категорию';
    } else {
        $validated['category_id'] = $categoryId;
    }
    
    // URL slug validation
    if (!empty($postData['url_slug'])) {
        $slug = InputValidator::validateSlug($postData['url_slug']);
        if (!$slug) {
            $errors['url_slug'] = 'URL должен содержать только латинские буквы, цифры и дефисы';
        } else {
            $validated['url_slug'] = $slug;
        }
    }
    
    return [
        'valid' => empty($errors),
        'data' => $validated,
        'errors' => $errors
    ];
}

// Example 3: Validate file upload
function validateFileUpload($file) {
    // Allow only images
    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $result = InputValidator::validateFile($file, $allowedTypes, $maxSize);
    
    if ($result['valid']) {
        // Sanitize filename
        $filename = InputValidator::sanitizeFilename($file['name']);
        
        // Generate unique filename
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $uniqueName = uniqid() . '_' . time() . '.' . $extension;
        
        return [
            'valid' => true,
            'filename' => $uniqueName
        ];
    }
    
    return $result;
}

// Example 4: Using validation in actual form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include CSRF protection
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf-protection.php';
    
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }
    
    // Validate the form
    $validation = validateContactForm($_POST);
    
    if ($validation['valid']) {
        // Process the validated data
        $name = $validation['data']['name'];
        $email = $validation['data']['email'];
        $message = $validation['data']['message'];
        $phone = $validation['data']['phone'] ?? null;
        
        // Save to database or send email
        // ... your processing code here ...
        
        $_SESSION['success'] = 'Форма успешно отправлена!';
        header('Location: /success');
        exit;
    } else {
        // Handle validation errors
        $_SESSION['errors'] = $validation['errors'];
        $_SESSION['old_data'] = $_POST;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Example 5: Display errors in form
function displayValidationError($field) {
    if (isset($_SESSION['errors'][$field])) {
        echo '<div class="error-message" style="color: #dc3545; font-size: 14px; margin-top: 5px;">';
        echo htmlspecialchars($_SESSION['errors'][$field]);
        echo '</div>';
    }
}

// Example 6: Get old input value
function oldValue($field, $default = '') {
    return htmlspecialchars($_SESSION['old_data'][$field] ?? $default);
}

// Example form HTML
?>
<form method="POST" action="/process-contact">
    <?= generateCSRFTokenField() ?>
    
    <div class="form-group">
        <label>Имя:</label>
        <input type="text" name="name" value="<?= oldValue('name') ?>" class="form-control">
        <?php displayValidationError('name'); ?>
    </div>
    
    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" value="<?= oldValue('email') ?>" class="form-control">
        <?php displayValidationError('email'); ?>
    </div>
    
    <div class="form-group">
        <label>Телефон (необязательно):</label>
        <input type="tel" name="phone" value="<?= oldValue('phone') ?>" class="form-control">
        <?php displayValidationError('phone'); ?>
    </div>
    
    <div class="form-group">
        <label>Сообщение:</label>
        <textarea name="message" class="form-control" rows="5"><?= oldValue('message') ?></textarea>
        <?php displayValidationError('message'); ?>
    </div>
    
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

<?php
// Clear errors and old data after displaying
unset($_SESSION['errors']);
unset($_SESSION['old_data']);
?>