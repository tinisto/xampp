<?php
// Modern registration page
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email адрес';
    } elseif (strlen($password) < 8) {
        $error = 'Пароль должен содержать минимум 8 символов';
    } elseif ($password !== $password_confirm) {
        $error = 'Пароли не совпадают';
    } elseif (!$terms) {
        $error = 'Необходимо принять условия использования';
    } else {
        // Check if email already exists
        $existing = db_fetch_one("SELECT id FROM users WHERE email = ?", [$email]);
        
        if ($existing) {
            $error = 'Пользователь с таким email уже существует';
        } else {
            // Create new user
            $userId = db_insert_id("
                INSERT INTO users (name, email, password, role, is_active, created_at)
                VALUES (?, ?, ?, 'user', 1, datetime('now'))
            ", [$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            
            if ($userId) {
                // Send welcome email and notification
                require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';
                
                EmailNotification::sendWelcomeEmail($email, $name);
                NotificationManager::sendWelcomeNotification($userId, $name);
                
                // Auto login after registration
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                
                // Redirect to welcome page or dashboard
                header('Location: /welcome');
                exit;
            } else {
                $error = 'Произошла ошибка при регистрации. Попробуйте позже.';
            }
        }
    }
}

// Page title
$pageTitle = 'Регистрация';

// Section 1: Registration form
ob_start();
?>
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
    <div style="max-width: 500px; width: 100%;">
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); padding: 40px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
                            border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <i class="fas fa-user-plus" style="font-size: 36px; color: white;"></i>
                </div>
                <h1 style="font-size: 28px; font-weight: 700; color: #333; margin: 0;">Регистрация</h1>
                <p style="color: #666; margin-top: 10px;">Создайте аккаунт для доступа ко всем функциям</p>
            </div>
            
            <?php if ($error): ?>
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="/register">
                <div style="margin-bottom: 20px;">
                    <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Имя <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                           required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Email <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
                    <small style="color: #666; font-size: 13px;">Мы никогда не передадим ваш email третьим лицам</small>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Пароль <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           minlength="8"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
                    <small style="color: #666; font-size: 13px;">Минимум 8 символов</small>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="password_confirm" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Подтвердите пароль <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirm" 
                           name="password_confirm" 
                           required
                           minlength="8"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: start; cursor: pointer;">
                        <input type="checkbox" name="terms" required style="margin-right: 8px; margin-top: 4px;">
                        <span style="color: #666; font-size: 14px;">
                            Я согласен с <a href="/terms" style="color: #007bff;">условиями использования</a> и 
                            <a href="/privacy" style="color: #007bff;">политикой конфиденциальности</a>
                        </span>
                    </label>
                </div>
                
                <button type="submit" 
                        style="width: 100%; padding: 14px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
                               color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; 
                               cursor: pointer; transition: transform 0.2s;">
                    <i class="fas fa-user-plus"></i> Создать аккаунт
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                <p style="color: #666; margin-bottom: 10px;">Уже есть аккаунт?</p>
                <a href="/login" 
                   style="color: #007bff; text-decoration: none; font-weight: 600;">
                    Войти в систему
                </a>
            </div>
        </div>
        
        <div style="background: #e8f5e9; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: #2e7d32;">
                <i class="fas fa-check-circle"></i> Преимущества регистрации:
            </h3>
            <ul style="margin: 0; padding-left: 25px; color: #555;">
                <li>Сохранение избранных учебных заведений</li>
                <li>Подписка на новости по интересующим темам</li>
                <li>Персональные рекомендации</li>
                <li>Участие в обсуждениях</li>
                <li>Доступ к эксклюзивным материалам</li>
            </ul>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Other sections empty for registration page
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>