<?php
// Forgot password page - migrated to use real_template.php

// Start session for error handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Восстановление пароля', [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Forgot Password Form
ob_start();
?>
<div style="max-width: 400px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <?php 
            include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
            renderSiteIcon('large', '/', 'forgot-password-logo');
            ?>
        </div>
        
        <p style="text-align: center; color: #666; margin-bottom: 30px;">
            Введите ваш email адрес, и мы отправим вам инструкции по восстановлению пароля
        </p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div style="padding: 12px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="/forgot-password-process">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div style="margin-bottom: 20px;">
                <input type="email" 
                       name="email" 
                       placeholder="Email адрес" 
                       required 
                       autofocus
                       style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
            </div>
            
            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; transition: background 0.3s;">
                Отправить инструкции
            </button>
        </form>
        
        <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 1px solid #eee;">
            <a href="/login" style="color: #6c757d; text-decoration: none; font-size: 14px; margin-right: 20px;">Вернуться к входу</a>
            <a href="/registration" style="color: #28a745; text-decoration: none; font-size: 14px;">Создать аккаунт</a>
        </div>
    </div>
</div>

<style>
[data-theme="dark"] input[type="email"] {
    background: var(--surface-dark, #2d3748);
    border-color: var(--border-dark, #4a5568);
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] input[type="email"]::placeholder {
    color: var(--text-secondary, #a0aec0);
}

button[type="submit"]:hover {
    background: #218838 !important;
}

a:hover {
    text-decoration: underline !important;
}

@media (max-width: 600px) {
    div[style*="padding: 40px"] {
        padding: 30px 20px !important;
    }
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Восстановление пароля - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>