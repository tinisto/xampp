<?php
// Standalone registration page without header/footer

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

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - 11klassniki.ru</title>
    
    <!-- New Favicon -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3VnPgo=" type="image/svg+xml">
    
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
            padding: 20px;
        }
        
        .registration-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        
        .site-icon {
            width: 60px;
            height: 60px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
            flex: 1;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 18px;
        }
        
        .checkbox-group {
            margin-bottom: 20px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }
        
        .checkbox-label input[type="checkbox"] {
            margin-right: 8px;
            margin-top: 2px;
        }
        
        .checkbox-label a {
            color: #007bff;
            text-decoration: none;
        }
        
        .checkbox-label a:hover {
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background: #218838;
        }
        
        .submit-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .form-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .form-links p {
            color: #666;
            margin-bottom: 10px;
        }
        
        .form-links a {
            color: #28a745;
            text-decoration: none;
            padding: 10px 24px;
            border: 1px solid #28a745;
            border-radius: 8px;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .form-links a:hover {
            background: #28a745;
            color: white;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .password-strength {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .password-match {
            border-color: #28a745 !important;
        }
        
        .password-nomatch {
            border-color: #dc3545 !important;
        }
        
        @media (max-width: 600px) {
            .registration-container {
                padding: 30px 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <a href="/" class="site-icon">11</a>
        
        <h1>Регистрация</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <form method="POST" action="/pages/registration/registration_process.php" id="registrationForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">Имя</label>
                    <input type="text" id="first_name" name="first_name" required autofocus
                           value="<?= isset($_SESSION['old_first_name']) ? htmlspecialchars($_SESSION['old_first_name']) : '' ?>"
                           autocomplete="given-name">
                    <?php unset($_SESSION['old_first_name']); ?>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Фамилия</label>
                    <input type="text" id="last_name" name="last_name" required
                           value="<?= isset($_SESSION['old_last_name']) ? htmlspecialchars($_SESSION['old_last_name']) : '' ?>"
                           autocomplete="family-name">
                    <?php unset($_SESSION['old_last_name']); ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email адрес</label>
                <input type="email" id="email" name="email" required 
                       value="<?= isset($_SESSION['old_email']) ? htmlspecialchars($_SESSION['old_email']) : '' ?>"
                       autocomplete="email">
                <?php unset($_SESSION['old_email']); ?>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required minlength="6"
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">👁</button>
                </div>
                <div class="password-strength">Минимум 6 символов</div>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Подтвердите пароль</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                       autocomplete="new-password">
            </div>
            
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="agree_terms" required>
                    <span>Я согласен с <a href="/privacy" target="_blank">условиями использования</a> и <a href="/privacy" target="_blank">политикой конфиденциальности</a></span>
                </label>
            </div>
            
            <button type="submit" class="submit-btn" id="submitBtn">Зарегистрироваться</button>
            
            <div class="form-links">
                <p>Уже есть аккаунт?</p>
                <a href="/login">Войти</a>
            </div>
        </form>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggle = passwordField.nextElementSibling;
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggle.textContent = '👁‍🗨';
            } else {
                passwordField.type = 'password';
                toggle.textContent = '👁';
            }
        }
        
        // Password confirmation validation
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.classList.remove('password-match');
                this.classList.add('password-nomatch');
            } else if (confirm && password === confirm) {
                this.classList.remove('password-nomatch');
                this.classList.add('password-match');
            } else {
                this.classList.remove('password-match', 'password-nomatch');
            }
        });
        
        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Пароли не совпадают');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Регистрация...';
            
            // Re-enable after 5 seconds in case of error
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Зарегистрироваться';
            }, 5000);
        });
    </script>
</body>
</html>