<?php
// Modern registration page - standalone without header/footer
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/logo.php';

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
                // Auto login after registration
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                
                // Set success message
                $success = "Регистрация прошла успешно! Добро пожаловать, " . htmlspecialchars($name) . "!";
            } else {
                $error = 'Произошла ошибка при регистрации. Попробуйте позже.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            color: #333;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
            margin-top: 20px;
        }
        
        .logo {
            margin-bottom: 15px;
        }
        
        .logo h1 {
            font-size: 32px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .logo .eleven {
            color: #667eea;
            font-weight: 700;
        }
        
        .logo .ru {
            color: #764ba2;
            font-weight: 500;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-header {
            margin-bottom: 15px;
        }
        
        .form-header h2 {
            font-size: 22px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-header p {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .form-header .icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 28px;
            color: white;
        }
        
        .form-group {
            margin-bottom: 12px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .required {
            color: #dc3545;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #28a745;
            background: white;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }
        
        .form-group small {
            color: #666;
            font-size: 13px;
            display: block;
            margin-top: 5px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            margin-bottom: 12px;
        }
        
        .checkbox-group input {
            margin-right: 10px;
            margin-top: 4px;
            width: auto;
        }
        
        .checkbox-group span {
            color: #666;
            font-size: 14px;
        }
        
        .checkbox-group a {
            color: #667eea;
            text-decoration: none;
        }
        
        .checkbox-group a:hover {
            text-decoration: underline;
        }
        
        .btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 12px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }
        
        .links {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e1e5e9;
        }
        
        .links p {
            color: #666;
            margin-bottom: 10px;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            margin: 0 10px;
            font-size: 14px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .benefits {
            background: #e8f5e9;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            text-align: left;
        }
        
        .benefits h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2e7d32;
        }
        
        .benefits ul {
            margin: 0;
            padding-left: 20px;
            color: #555;
            font-size: 14px;
        }
        
        .benefits li {
            margin-bottom: 5px;
        }
        
        .back-home {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e1e5e9;
        }
        
        .back-home a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-home a:hover {
            color: #333;
        }
        
        /* Dark mode styles */
        body.dark-mode {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        }
        
        body.dark-mode .container {
            background: #2d2d2d;
        }
        
        body.dark-mode .logo h1,
        body.dark-mode .form-header h2,
        body.dark-mode .form-group label,
        body.dark-mode .benefits h3 {
            color: #e0e0e0;
        }
        
        body.dark-mode .logo p,
        body.dark-mode .form-header p,
        body.dark-mode .form-group small,
        body.dark-mode .checkbox-group span,
        body.dark-mode .links p,
        body.dark-mode .benefits li {
            color: #b0b0b0;
        }
        
        body.dark-mode .form-group input {
            background: #3a3a3a;
            border-color: #555;
            color: #e0e0e0;
        }
        
        body.dark-mode .form-group input:focus {
            background: #3a3a3a;
            border-color: #48bb78;
        }
        
        body.dark-mode .checkbox-group a,
        body.dark-mode .links a,
        body.dark-mode .back-home a {
            color: #4299e1;
        }
        
        body.dark-mode .checkbox-group a:hover,
        body.dark-mode .back-home a:hover {
            color: #63b3ed;
        }
        
        body.dark-mode .links,
        body.dark-mode .back-home {
            border-color: #555;
        }
        
        body.dark-mode .benefits {
            background: #2d4a2f;
        }
        
        body.dark-mode .benefits h3 {
            color: #68d391;
        }
        
        body.dark-mode .btn {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container" style="margin-bottom: 15px; text-align: center;">
            <?php logo('normal'); ?>
        </div>
        
        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <strong>Успешно!</strong> <?= $success ?>
                <div style="margin-top: 15px;">
                    <a href="/" class="btn" style="display: inline-block; text-decoration: none; padding: 10px 20px;">
                        <i class="fas fa-home"></i> Перейти на главную
                    </a>
                </div>
            </div>
        <?php else: ?>
        
        <div class="form-header">
            <h2>Регистрация</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Ошибка:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <input type="text" 
                       id="name" 
                       name="name" 
                       required
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       placeholder="Имя"
                       autocomplete="name">
            </div>
            
            <div class="form-group">
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="Email"
                       autocomplete="email">
            </div>
            
            <div class="form-group">
                <div style="position: relative;">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           minlength="8"
                           placeholder="Пароль (минимум 8 символов)"
                           autocomplete="new-password"
                           style="padding-right: 45px;">
                    <i id="toggle-password" 
                       class="fas fa-eye password-toggle"
                       style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer; font-size: 18px; transition: color 0.2s;"></i>
                </div>
            </div>
            
            <div class="form-group">
                <div style="position: relative;">
                    <input type="password" 
                           id="password_confirm" 
                           name="password_confirm" 
                           required
                           minlength="8"
                           placeholder="Подтвердите пароль"
                           autocomplete="new-password"
                           style="padding-right: 45px;">
                    <i id="toggle-confirm-password" 
                       class="fas fa-eye password-toggle"
                       style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer; font-size: 18px; transition: color 0.2s;"></i>
                </div>
            </div>
            
            <label class="checkbox-group">
                <input type="checkbox" name="terms" required>
                <span>
                    Я согласен с <a href="/terms.php">условиями использования</a> и 
                    <a href="/privacy_modern.php">политикой конфиденциальности</a>
                </span>
            </label>
            
            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Создать аккаунт
            </button>
        </form>
        
        
        <div class="links">
            <a href="/login_modern.php">Уже есть аккаунт?</a>
        </div>
        
        <?php endif; ?>
        
    </div>

    <script>
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }
        
        // Password toggle functionality
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            
            if (toggle && input) {
                toggle.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    // Toggle the eye icon
                    if (type === 'password') {
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    } else {
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    }
                });
                
                // Add hover effect
                toggle.addEventListener('mouseenter', function() {
                    this.style.color = '#666';
                });
                
                toggle.addEventListener('mouseleave', function() {
                    this.style.color = '#999';
                });
            }
        }
        
        // Setup both password fields
        setupPasswordToggle('toggle-password', 'password');
        setupPasswordToggle('toggle-confirm-password', 'password_confirm');
    </script>
</body>
</html>