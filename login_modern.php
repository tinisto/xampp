<?php
// Modern login page - standalone without header/footer
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/logo.php';

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
            $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему - 11klassniki.ru</title>
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
            align-items: center;
            justify-content: center;
            color: #333;
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 20px;
        }
        
        .logo h1 {
            font-size: 36px;
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
            margin-bottom: 20px;
        }
        
        .form-header h2 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 36px;
            color: white;
        }
        
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #666;
        }
        
        .checkbox-label input {
            margin-right: 8px;
            width: auto;
        }
        
        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
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
        
        .back-home {
            margin-top: 30px;
            padding-top: 20px;
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
        body.dark-mode .form-group label {
            color: #e0e0e0;
        }
        
        body.dark-mode .logo p {
            color: #b0b0b0;
        }
        
        body.dark-mode .form-group input {
            background: #3a3a3a;
            border-color: #555;
            color: #e0e0e0;
        }
        
        body.dark-mode .form-group input:focus {
            background: #3a3a3a;
            border-color: #4299e1;
        }
        
        body.dark-mode .checkbox-label {
            color: #b0b0b0;
        }
        
        body.dark-mode .forgot-link,
        body.dark-mode .links a,
        body.dark-mode .back-home a {
            color: #4299e1;
        }
        
        body.dark-mode .forgot-link:hover,
        body.dark-mode .back-home a:hover {
            color: #63b3ed;
        }
        
        body.dark-mode .links,
        body.dark-mode .back-home {
            border-color: #555;
        }
        
        body.dark-mode .links p {
            color: #b0b0b0;
        }
        
        body.dark-mode .btn {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container" style="margin-bottom: 20px; text-align: center;">
            <?php logo('normal'); ?>
        </div>
        
        <div class="form-header">
            <h2>Вход в систему</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Ошибка:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="your@email.com"
                       autocomplete="email">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <div style="position: relative;">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="Введите пароль"
                           autocomplete="current-password"
                           style="padding-right: 45px;">
                    <i id="toggle-password" 
                       class="fas fa-eye password-toggle"
                       style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer; font-size: 18px; transition: color 0.2s;"></i>
                </div>
            </div>
            
            <div class="form-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    Запомнить меня
                </label>
                <a href="/forgot-password.php" class="forgot-link">Забыли пароль?</a>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Войти
            </button>
        </form>
        
        <div class="links">
            <p>Нет аккаунта?</p>
            <a href="/register_modern.php"><i class="fas fa-user-plus"></i> Зарегистрироваться</a>
        </div>
        
    </div>

    <script>
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }
        
        // Password toggle functionality
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
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
            togglePassword.addEventListener('mouseenter', function() {
                this.style.color = '#666';
            });
            
            togglePassword.addEventListener('mouseleave', function() {
                this.style.color = '#999';
            });
        }
    </script>
</body>
</html>