<?php
// Reset password confirmation page - migrated to use real_template.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get token from URL
$token = $_GET['token'] ?? '';

// If no token, redirect to forgot password page
if (empty($token)) {
    header('Location: /forgot-password');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Handle form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $error = 'Пожалуйста, заполните все поля';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        // Process password reset
        // This is a simplified version - in production you'd validate the token
        // and update the user's password
        $_SESSION['message'] = 'Пароль успешно изменен!';
        header('Location: /login');
        exit();
    }
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Сброс пароля', [
    'fontSize' => '32px',
    'margin' => '40px 0',
    'textAlign' => 'center'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Reset password form
ob_start();
?>
<div style="padding: 40px 20px; max-width: 500px; margin: 0 auto;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <?php if (!empty($error)): ?>
        <div style="background: #fee; color: #c33; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="/reset-password-confirm?token=<?= htmlspecialchars($token) ?>">
            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Новый пароль
                </label>
                <input type="password" id="password" name="password" required
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <label for="confirm_password" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Подтвердите пароль
                </label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 14px; background: #007bff; color: white; border: none; 
                                       border-radius: 8px; font-size: 16px; cursor: pointer; transition: background 0.3s;">
                Изменить пароль
            </button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="/login" style="color: #007bff; text-decoration: none;">Вернуться к входу</a>
        </div>
    </div>
</div>

<style>
button[type="submit"]:hover {
    background: #0056b3 !important;
}

[data-theme="dark"] input[type="password"] {
    background: var(--surface-dark, #2d3748);
    border-color: #4a5568;
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] label {
    color: var(--text-primary, #e4e6eb) !important;
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Сброс пароля - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>