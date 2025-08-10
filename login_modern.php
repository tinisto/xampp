<?php
// Modern login page
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Пожалуйста, заполните все поля';
    } else {
        // Fetch user from database
        $user = db_fetch_one("
            SELECT * FROM users 
            WHERE email = ? AND is_active = 1
        ", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Update last login
            db_execute("
                UPDATE users 
                SET last_login = datetime('now') 
                WHERE id = ?
            ", [$user['id']]);
            
            // Redirect to dashboard or previous page
            $redirect = $_SESSION['redirect_after_login'] ?? '/';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Неверный email или пароль';
        }
    }
}

// Page title
$pageTitle = 'Вход в систему';

// Section 1: Login form
ob_start();
?>
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
    <div style="max-width: 400px; width: 100%;">
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); padding: 40px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); 
                            border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <i class="fas fa-user" style="font-size: 36px; color: white;"></i>
                </div>
                <h1 style="font-size: 28px; font-weight: 700; color: #333; margin: 0;">Вход в систему</h1>
            </div>
            
            <?php if ($error): ?>
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="/login">
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Пароль
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
                </div>
                
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="remember" style="margin-right: 8px;">
                        <span style="color: #666;">Запомнить меня</span>
                    </label>
                    <a href="/forgot-password" style="color: #007bff; text-decoration: none; font-size: 14px;">
                        Забыли пароль?
                    </a>
                </div>
                
                <button type="submit" 
                        style="width: 100%; padding: 14px; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); 
                               color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; 
                               cursor: pointer; transition: transform 0.2s;">
                    <i class="fas fa-sign-in-alt"></i> Войти
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                <p style="color: #666; margin-bottom: 10px;">Нет аккаунта?</p>
                <a href="/register" 
                   style="color: #007bff; text-decoration: none; font-weight: 600;">
                    Зарегистрироваться
                </a>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <p style="color: #999; font-size: 14px;">
                Входя в систему, вы соглашаетесь с нашими<br>
                <a href="/terms" style="color: #007bff; text-decoration: none;">Условиями использования</a> и 
                <a href="/privacy" style="color: #007bff; text-decoration: none;">Политикой конфиденциальности</a>
            </p>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Other sections empty for login page
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>