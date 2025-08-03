<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
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
        
        .logo-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .login-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 13px;
        }
        
        .form-label .required {
            color: var(--danger-color);
            margin-left: 2px;
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
        
        .form-input.error {
            border-color: var(--danger-color);
        }
        
        .form-input.error:focus {
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
            transition: var(--transition);
            background: none;
            border: none;
            padding: 4px;
        }
        
        .password-toggle:hover {
            color: var(--text-primary);
        }
        
        .error-message {
            color: var(--danger-color);
            font-size: 13px;
            margin-top: 6px;
            display: none;
        }
        
        .error-message.show {
            display: block;
            animation: shake 0.3s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .form-footer p {
            color: var(--text-secondary);
            font-size: 13px;
            margin: 0;
        }
        
        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
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
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .activation-help {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            border: 1px solid;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-outline-success {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-success:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .form-group-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .form-check {
            display: flex;
            align-items: center;
        }
        
        .form-check-input {
            width: 16px;
            height: 16px;
            margin: 0;
            margin-right: 8px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        .form-check-label {
            font-size: 14px;
            color: var(--text-primary);
            cursor: pointer;
            user-select: none;
        }
        
        .forgot-link {
            font-size: 14px;
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }
            
            .login-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <a href="/" class="logo-link">
                <div class="logo-placeholder">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2"/>
                        <text x="20" y="26" text-anchor="middle" fill="currentColor" font-size="18" font-weight="bold">11</text>
                    </svg>
                </div>
            </a>
        </div>
        <div class="login-header">
            <h1>Вход</h1>
        </div>
        
        <div class="login-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    
                    <?php if (strpos($_SESSION['error'], 'не активирован') !== false): ?>
                        <div class="activation-help">
                            <a href="#" class="btn-sm btn-outline-primary" onclick="alert('Функция отправки кода временно недоступна')">
                                Отправить код еще раз
                            </a>
                            <a href="/activate-user-manual.php" class="btn-sm btn-outline-success">
                                Активировать аккаунт вручную
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['info'])): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($_SESSION['info']) ?>
                    <?php unset($_SESSION['info']); ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" method="post" action="/pages/login/login_process_simple.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        Email адрес <span class="required">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-input" 
                           value="<?= isset($_SESSION['old_email']) ? htmlspecialchars($_SESSION['old_email']) : '' ?>"
                           required>
                    <div class="error-message" id="email-error">Пожалуйста, введите корректный email</div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        Пароль <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input" 
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path class="eye-open" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle class="eye-open" cx="12" cy="12" r="3"/>
                                <path class="eye-closed" style="display: none;" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/>
                            </svg>
                        </button>
                    </div>
                    <div class="error-message" id="password-error">Пожалуйста, введите пароль</div>
                </div>
                
                <div class="form-group-row">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Запомнить меня</label>
                    </div>
                    <a href="/forgot-password" class="forgot-link">Забыли пароль?</a>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    Войти
                </button>
            </form>
            
            <div class="form-footer">
                <p>Нет аккаунта? <a href="/registration">Зарегистрироваться</a></p>
            </div>
        </div>
    </div>
    
    <?php unset($_SESSION['old_email']); ?>
    
    <script>
    // Form validation
    const form = document.getElementById('loginForm');
    const inputs = form.querySelectorAll('.form-input');
    const submitBtn = document.getElementById('submitBtn');
    
    // Real-time validation
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.classList.contains('error')) {
                validateField(input);
            }
        });
    });
    
    function validateField(field) {
        const errorElement = document.getElementById(field.id + '-error');
        let isValid = true;
        
        // Check if required field is empty
        if (field.hasAttribute('required') && !field.value.trim()) {
            isValid = false;
        }
        
        // Email validation
        if (field.type === 'email' && field.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            isValid = emailRegex.test(field.value);
        }
        
        // Update UI
        if (isValid) {
            field.classList.remove('error');
            errorElement.classList.remove('show');
        } else {
            field.classList.add('error');
            errorElement.classList.add('show');
        }
        
        return isValid;
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isFormValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isFormValid = false;
            }
        });
        
        if (isFormValid) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Вход...';
            form.submit();
        }
    });
    
    // Password visibility toggle
    const toggle = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    toggle.addEventListener('click', function() {
        const eyeOpen = this.querySelectorAll('.eye-open');
        const eyeClosed = this.querySelectorAll('.eye-closed');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.forEach(el => el.style.display = 'none');
            eyeClosed.forEach(el => el.style.display = 'block');
        } else {
            passwordInput.type = 'password';
            eyeOpen.forEach(el => el.style.display = 'block');
            eyeClosed.forEach(el => el.style.display = 'none');
        }
    });
    </script>
</body>
</html>