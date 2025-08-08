<?php
// Password change page - migrated to use real_template.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Изменить пароль', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Обновите пароль для вашего аккаунта'
]);
$greyContent1 = ob_get_clean();

// Section 2: Account Navigation
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="/account" class="account-nav-item">Обзор</a>
        <a href="/account/edit" class="account-nav-item">Редактировать профиль</a>
        <a href="/account/password-change" class="account-nav-item active">Изменить пароль</a>
        <a href="/account/comments" class="account-nav-item">Мои комментарии</a>
        <?php if ($_SESSION['occupation'] === 'admin'): ?>
        <a href="/dashboard" class="account-nav-item">Панель управления</a>
        <?php endif; ?>
        <a href="/logout" class="account-nav-item" style="color: #dc3545;">Выйти</a>
    </div>
</div>

<style>
.account-nav-item {
    padding: 10px 20px;
    background: var(--surface, #f8f9fa);
    color: var(--text-primary, #333);
    text-decoration: none;
    border-radius: 25px;
    transition: all 0.3s;
    font-weight: 500;
}

.account-nav-item:hover {
    background: #28a745;
    color: white;
    transform: translateY(-2px);
}

.account-nav-item.active {
    background: #28a745;
    color: white;
}

[data-theme="dark"] .account-nav-item {
    background: var(--surface-dark, #2d3748);
    color: var(--text-primary, #e4e6eb);
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Password Change Form
ob_start();
?>
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <form id="password-change-form" method="POST" action="/account/password-change-process">
            
            <div style="margin-bottom: 25px;">
                <label for="current_password" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Текущий пароль
                </label>
                <div style="position: relative;">
                    <input type="password" id="current_password" name="current_password" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                                  background: var(--input-bg, #fff); color: var(--text-primary, #333);">
                    <button type="button" class="password-toggle" onclick="togglePassword('current_password')"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                   background: none; border: none; color: #666; cursor: pointer; padding: 5px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label for="new_password" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Новый пароль
                </label>
                <div style="position: relative;">
                    <input type="password" id="new_password" name="new_password" required
                           pattern=".{6,}" title="Минимум 6 символов"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                                  background: var(--input-bg, #fff); color: var(--text-primary, #333);">
                    <button type="button" class="password-toggle" onclick="togglePassword('new_password')"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                   background: none; border: none; color: #666; cursor: pointer; padding: 5px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small style="color: #666; font-size: 14px;">Минимум 6 символов</small>
            </div>
            
            <div style="margin-bottom: 30px;">
                <label for="confirm_password" style="display: block; margin-bottom: 8px; color: var(--text-primary, #333); font-weight: 500;">
                    Подтвердите новый пароль
                </label>
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;
                                  background: var(--input-bg, #fff); color: var(--text-primary, #333);">
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); 
                                   background: none; border: none; color: #666; cursor: pointer; padding: 5px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div id="error-message" style="display: none; background: #f8d7da; color: #721c24; padding: 12px; 
                                           border-radius: 8px; margin-bottom: 20px;">
            </div>
            
            <div id="success-message" style="display: none; background: #d4edda; color: #155724; padding: 12px; 
                                             border-radius: 8px; margin-bottom: 20px;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 14px; background: #28a745; color: white; 
                                         border: none; border-radius: 8px; font-size: 16px; font-weight: 500; 
                                         cursor: pointer; transition: background 0.3s;">
                Изменить пароль
            </button>
        </form>
        
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

document.getElementById('password-change-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');
    
    // Hide messages
    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';
    
    // Validate passwords match
    if (newPassword !== confirmPassword) {
        errorDiv.textContent = 'Пароли не совпадают';
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
$pageTitle = 'Изменить пароль - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>