<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/logo.php';

// Get token from URL
$token = $_GET['token'] ?? '';

$error = '';
$success = false;
$validToken = false;

// Validate token
if (empty($token)) {
    $error = 'Недействительная ссылка для сброса пароля.';
} else {
    // Check if token is valid
    $db = Database::getInstance();
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW() AND is_active = 1",
        [$token]
    );
    
    if ($user) {
        $validToken = true;
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($newPassword)) {
                $error = 'Введите новый пароль.';
            } elseif (strlen($newPassword) < 6) {
                $error = 'Пароль должен содержать минимум 6 символов.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Пароли не совпадают.';
            } else {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                try {
                    $db->update('users', 
                        [
                            'password' => $hashedPassword,
                            'reset_token' => null,
                            'reset_expires' => null
                        ],
                        'id = ?',
                        [$user['id']]
                    );
                    
                    $success = true;
                    
                    // Log the user in
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_verified'] = $user['email_verified_at'] ? 1 : 0;
                    
                } catch (Exception $e) {
                    $error = 'Произошла ошибка при обновлении пароля.';
                }
            }
        }
    } else {
        $error = 'Ссылка для сброса пароля недействительна или истекла.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый пароль - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --danger-color: #dc3545;
            --text-primary: #333;
            --text-secondary: #666;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #dee2e6;
            --input-focus: #80bdff;
            --shadow: 0 0 20px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
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
        
        .reset-container {
            width: 100%;
            max-width: 400px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideIn 0.4s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-section {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .logo-link {
            display: inline-block;
            text-decoration: none;
            color: var(--primary-color);
            transition: var(--transition);
        }
        
        .logo-link:hover {
            transform: scale(1.05);
            color: var(--primary-hover);
        }
        
        .reset-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        
        .reset-header h1 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .reset-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 13px;
        }
        
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
            transition: var(--transition);
            background-color: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .success-message {
            text-align: center;
            padding: 40px 20px;
        }
        
        .success-icon {
            font-size: 60px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .success-message h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .success-message p {
            color: var(--text-secondary);
            margin-bottom: 25px;
        }
        
        .btn-login {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .password-requirements {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .password-strength.weak {
            color: #dc3545;
        }
        
        .password-strength.medium {
            color: #ffc107;
        }
        
        .password-strength.strong {
            color: #28a745;
        }
        
        .success-container {
            text-align: center;
            padding: 40px 20px;
        }
        
        .success-icon {
            font-size: 64px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .success-message {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .success-submessage {
            color: var(--text-secondary);
            margin-bottom: 30px;
        }
        
        .success-btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .success-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        /* Dark mode styles */
        body.dark-mode {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        }
        
        body.dark-mode .reset-container {
            background: #2d2d2d;
        }
        
        body.dark-mode .logo-section {
            border-color: #555;
        }
        
        body.dark-mode .logo-section h1,
        body.dark-mode .logo-section p {
            color: #e0e0e0;
        }
        
        body.dark-mode .reset-header {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        }
        
        body.dark-mode .form-label {
            color: #e0e0e0;
        }
        
        body.dark-mode .form-input {
            background: #3a3a3a;
            border-color: #555;
            color: #e0e0e0;
        }
        
        body.dark-mode .form-input:focus {
            border-color: #4299e1;
        }
        
        body.dark-mode .submit-btn {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        }
        
        body.dark-mode .success-message {
            color: #e0e0e0;
        }
        
        body.dark-mode .success-submessage {
            color: #b0b0b0;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-section" style="text-align: center; padding: 20px; border-bottom: 1px solid #e1e5e9;">
            <?php logo('small'); ?>
        </div>
        
        <?php if ($success): ?>
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="success-message">Пароль успешно изменён!</div>
                <div class="success-submessage">Вы вошли в систему с новым паролем</div>
                <a href="/profile_modern.php" class="success-btn">
                    <i class="fas fa-user"></i> Перейти в профиль
                </a>
            </div>
        <?php elseif ($validToken): ?>
            <div class="reset-header">
                <h1>Создание нового пароля</h1>
            </div>
            
            <div class="reset-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Ошибка:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <p class="info-text">
                    Введите новый пароль для вашего аккаунта
                </p>
                
                <form method="post">
                    <div class="form-group">
                        <label for="password" class="form-label">Новый пароль</label>
                        <div style="position: relative;">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-input" 
                                   required
                                   minlength="6"
                                   placeholder="Минимум 6 символов">
                            <i id="toggle-password" 
                               class="fas fa-eye password-toggle"
                               style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer; font-size: 18px; transition: color 0.2s;"></i>
                        </div>
                        <div id="password-strength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Подтвердите пароль</label>
                        <div style="position: relative;">
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="form-input" 
                                   required
                                   minlength="6"
                                   placeholder="Введите пароль ещё раз">
                            <i id="toggle-confirm-password" 
                               class="fas fa-eye password-toggle"
                               style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer; font-size: 18px; transition: color 0.2s;"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-lock"></i> Сохранить новый пароль
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="reset-header" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                <h1>Ошибка</h1>
            </div>
            
            <div class="reset-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                
                <p class="info-text">
                    Возможно, срок действия ссылки истёк или она была использована ранее.
                    Вы можете запросить новую ссылку для восстановления пароля.
                </p>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="/forgot-password.php" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                        <i class="fas fa-redo"></i> Запросить новую ссылку
                    </a>
                    <span style="color: #ddd; margin: 0 10px;">•</span>
                    <a href="/login_modern.php" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </a>
                </div>
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
        const togglePassword = document.getElementById('toggle-password');
        const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        
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
        
        if (toggleConfirmPassword && confirmInput) {
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmInput.setAttribute('type', type);
                
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
            toggleConfirmPassword.addEventListener('mouseenter', function() {
                this.style.color = '#666';
            });
            
            toggleConfirmPassword.addEventListener('mouseleave', function() {
                this.style.color = '#999';
            });
        }
        
        // Password strength indicator
        const passwordStrength = document.getElementById('password-strength');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 'weak';
                let message = 'Слабый пароль';
                
                if (password.length >= 8) {
                    if (/[A-Z]/.test(password) && /[a-z]/.test(password) && /[0-9]/.test(password)) {
                        strength = 'strong';
                        message = 'Сильный пароль';
                    } else {
                        strength = 'medium';
                        message = 'Средний пароль';
                    }
                } else if (password.length >= 6) {
                    strength = 'medium';
                    message = 'Средний пароль';
                }
                
                passwordStrength.className = 'password-strength ' + strength;
                passwordStrength.textContent = message;
            });
            
            // Check password match
            confirmInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    this.setCustomValidity('Пароли не совпадают');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    </script>
</body>
</html>