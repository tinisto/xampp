<?php
// Registration page - migrated to use real_template.php

// Start session for error handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Регистрация', [
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

// Section 5: Registration Form
ob_start();
?>
<div style="max-width: 500px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <?php 
            include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
            renderSiteIcon('large', '/', 'registration-logo');
            ?>
        </div>
        
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
        
        <form method="post" action="/pages/registration/registration_process.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <input type="text" 
                           name="first_name" 
                           placeholder="Имя" 
                           required 
                           autofocus
                           value="<?= isset($_SESSION['old_first_name']) ? htmlspecialchars($_SESSION['old_first_name']) : '' ?>"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
                    <?php unset($_SESSION['old_first_name']); ?>
                </div>
                
                <div style="flex: 1;">
                    <input type="text" 
                           name="last_name" 
                           placeholder="Фамилия" 
                           required
                           value="<?= isset($_SESSION['old_last_name']) ? htmlspecialchars($_SESSION['old_last_name']) : '' ?>"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
                    <?php unset($_SESSION['old_last_name']); ?>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <input type="email" 
                       name="email" 
                       placeholder="Email адрес" 
                       required
                       value="<?= isset($_SESSION['old_email']) ? htmlspecialchars($_SESSION['old_email']) : '' ?>"
                       style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
                <?php unset($_SESSION['old_email']); ?>
            </div>
            
            <div style="margin-bottom: 20px;">
                <div style="position: relative;">
                    <input type="password" 
                           name="password" 
                           id="passwordInput"
                           placeholder="Пароль" 
                           required
                           minlength="6"
                           style="width: 100%; padding: 12px 16px; padding-right: 45px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
                    <button type="button" 
                            id="togglePassword"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #666; cursor: pointer; padding: 5px;">
                        <i class="fa fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <small style="color: #666; font-size: 12px;">Минимум 6 символов</small>
            </div>
            
            <div style="margin-bottom: 20px;">
                <input type="password" 
                       name="password_confirm" 
                       id="passwordConfirmInput"
                       placeholder="Подтвердите пароль" 
                       required
                       minlength="6"
                       style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: flex-start; cursor: pointer; font-size: 14px; color: #666;">
                    <input type="checkbox" 
                           name="agree_terms" 
                           required
                           style="margin-right: 8px; margin-top: 2px;">
                    <span>Я согласен с <a href="/privacy" target="_blank" style="color: #28a745;">условиями использования</a> и <a href="/privacy" target="_blank" style="color: #28a745;">политикой конфиденциальности</a></span>
                </label>
            </div>
            
            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; transition: background 0.3s;">
                Зарегистрироваться
            </button>
        </form>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <p style="color: #666; margin-bottom: 10px;">Уже есть аккаунт?</p>
            <a href="/login" 
               style="display: inline-block; padding: 10px 24px; background: transparent; color: #28a745; border: 1px solid #28a745; border-radius: 8px; text-decoration: none; font-size: 14px; transition: all 0.3s;">
                Войти
            </a>
        </div>
    </div>
</div>

<script>
// Password toggle
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('passwordInput');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Password confirmation validation
document.getElementById('passwordConfirmInput').addEventListener('input', function() {
    const password = document.getElementById('passwordInput').value;
    const confirm = this.value;
    
    if (confirm && password !== confirm) {
        this.style.borderColor = '#dc3545';
    } else if (confirm && password === confirm) {
        this.style.borderColor = '#28a745';
    } else {
        this.style.borderColor = '#ddd';
    }
});
</script>

<style>
[data-theme="dark"] input[type="email"],
[data-theme="dark"] input[type="password"],
[data-theme="dark"] input[type="text"] {
    background: var(--surface-dark, #2d3748);
    border-color: var(--border-dark, #4a5568);
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] input::placeholder {
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
    
    div[style*="display: flex"][style*="gap: 15px"] {
        flex-direction: column !important;
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
$pageTitle = 'Регистрация - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>