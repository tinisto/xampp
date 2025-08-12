<?php
// Reset password confirmation page - migrated to use real_template.php

// Get token and email from URL
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);

// Validate parameters
if (empty($token) || empty($email)) {
    header('Location: /forgot-password');
    exit();
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Сброс пароля', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Создайте новый пароль для вашего аккаунта'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Reset Password Form
ob_start();
?>
<div style="max-width: 500px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <form id="reset-password-form" action="/reset-password-confirm-process" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            
            <div style="margin-bottom: 20px; text-align: center;">
                <i class="fas fa-key" style="font-size: 48px; color: #28a745;"></i>
            </div>
            
            <p style="text-align: center; color: var(--text-secondary, #666); margin-bottom: 30px;">
                Введите новый пароль для аккаунта<br>
                <strong><?= htmlspecialchars($email) ?></strong>
            </p>
            
            <div style="margin-bottom: 25px;">
                <label for="password" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Новый пароль
                </label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required
                           pattern=".{6,}" title="Минимум 6 символов"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                                  background: var(--input-bg, #fff); color: var(--text-primary, #333);">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                   background: none; border: none; color: #666; cursor: pointer; padding: 5px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small style="color: #666; font-size: 14px;">Минимум 6 символов</small>
            </div>
            
            <div style="margin-bottom: 30px;">
                <label for="password_confirm" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Подтвердите пароль
                </label>
                <div style="position: relative;">
                    <input type="password" id="password_confirm" name="password_confirm" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                                  background: var(--input-bg, #fff); color: var(--text-primary, #333);">
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirm')"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                   background: none; border: none; color: #666; cursor: pointer; padding: 5px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div id="error-message" style="display: none; background: #f8d7da; color: #721c24; padding: 12px; 
                                           border-radius: 8px; margin-bottom: 20px;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 14px; background: #28a745; color: white; 
                                         border: none; border-radius: 8px; font-size: 16px; font-weight: 500; 
                                         cursor: pointer; transition: background 0.3s;">
                Сбросить пароль
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <p style="color: var(--text-secondary, #666);">
                Вспомнили пароль? 
                <a href="/login" style="color: #28a745;">Войти</a>
            </p>
        </div>
        
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.getElementById('reset-password-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    const errorDiv = document.getElementById('error-message');
    
    // Hide error message
    errorDiv.style.display = 'none';
    
    // Validate passwords match
    if (password !== passwordConfirm) {
        errorDiv.textContent = 'Пароли не совпадают';
        errorDiv.style.display = 'block';
        return;
    }
    
    // Validate password length
    if (password.length < 6) {
        errorDiv.textContent = 'Пароль должен содержать минимум 6 символов';
        errorDiv.style.display = 'block';
        return;
    }
    
    // Submit form
    this.submit();
});
</script>

<style>
[data-theme="dark"] input {
    background: var(--surface-dark, #3a4252) !important;
    color: var(--text-primary, #e4e6eb) !important;
    border-color: #4a5568 !important;
}

[data-theme="dark"] label {
    color: var(--text-primary, #e4e6eb) !important;
}

[data-theme="dark"] small {
    color: var(--text-secondary, #b0b3b8) !important;
}

[data-theme="dark"] div[style*="background: var(--surface, #ffffff)"] {
    background: var(--surface-dark, #2d3748) !important;
}

button[type="submit"]:hover {
    background: #218838 !important;
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Сброс пароля - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>