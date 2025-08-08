<?php
// Load environment and database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Hide header and footer for auth pages
$hideHeader = true;
$hideFooter = true;

// Set page title
$pageTitle = 'Восстановление пароля';

// Clear all grey sections except main content
$greyContent1 = '';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';

// Main forgot password form content
ob_start();
?>
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="width: 100%; max-width: 400px;">
        
        <!-- Logo -->
        <h1 style="text-align: center; margin-bottom: 40px;">
            <a href="/" style="color: #28a745; text-decoration: none; font-weight: bold; font-size: 28px;">
                📚 11-классники.ру
            </a>
        </h1>
        
        <!-- Forgot Password Form -->
        <div class="auth-form-container">
            <h2 style="text-align: center; margin-bottom: 10px; color: #333;">Восстановление пароля</h2>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">
                Введите ваш email, и мы отправим вам инструкции по восстановлению пароля
            </p>
            
            <form method="POST" action="/forgot-password-process.php">
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                        Email
                    </label>
                    <input type="email" id="email" name="email" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;"
                           placeholder="your@email.com">
                </div>
                
                <button type="submit" 
                        style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer;">
                    Отправить инструкции
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                <p style="color: #666; margin-bottom: 10px;">
                    Вспомнили пароль?
                </p>
                <a href="/login" style="color: #28a745; text-decoration: none; font-weight: 500;">
                    Войти
                </a>
            </div>
        </div>
        
    </div>
</div>

<style>
/* Reuse auth form styles */
.auth-form-container {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

[data-theme="dark"] .auth-form-container {
    background: #2d3748 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

[data-theme="dark"] .auth-form-container h2 {
    color: #f7fafc !important;
}

[data-theme="dark"] .auth-form-container label {
    color: #f7fafc !important;
}

[data-theme="dark"] .auth-form-container input {
    background: #1a202c !important;
    border-color: #4a5568 !important;
    color: #f7fafc !important;
}

[data-theme="dark"] .auth-form-container input::placeholder {
    color: #718096 !important;
}

[data-theme="dark"] .auth-form-container p {
    color: #cbd5e0 !important;
}

input:focus {
    outline: none;
    border-color: #28a745 !important;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}

button[type="submit"]:hover {
    background: #218838 !important;
}

@media (max-width: 480px) {
    .auth-form-container {
        padding: 30px 20px;
    }
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Clear other sections
$greyContent6 = '';
$blueContent = '';

// Include the real template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>