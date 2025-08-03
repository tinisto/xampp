<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$oldData = $_SESSION['oldData'] ?? [];
unset($_SESSION['oldData']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - 11-классники</title>
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
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .registration-container {
            width: 100%;
            max-width: 480px;
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
        
        .registration-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 20px 30px;
            text-align: center;
        }
        
        .registration-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        
        .registration-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .form-label .required {
            color: var(--danger-color);
            margin-left: 2px;
        }
        
        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
            background-color: white;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        
        .form-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
            cursor: pointer;
        }
        
        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
        }
        
        .form-input.error,
        .form-select.error {
            border-color: var(--danger-color);
        }
        
        .form-input.error:focus,
        .form-select.error:focus {
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
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
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: -9999px;
        }
        
        .file-input-label {
            display: block;
            padding: 12px 16px;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-secondary);
        }
        
        .file-input-label:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: rgba(40, 167, 69, 0.05);
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
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
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }
        
        .form-footer p {
            color: var(--text-secondary);
            font-size: 14px;
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
        
        .password-strength {
            height: 4px;
            background-color: var(--border-color);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
            display: none;
        }
        
        .password-strength.show {
            display: block;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .password-strength-bar.weak {
            width: 33%;
            background-color: var(--danger-color);
        }
        
        .password-strength-bar.medium {
            width: 66%;
            background-color: #ffc107;
        }
        
        .password-strength-bar.strong {
            width: 100%;
            background-color: var(--primary-color);
        }
        
        @media (max-width: 480px) {
            .registration-container {
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }
            
            .registration-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-header">
            <h1>Регистрация</h1>
        </div>
        
        <div class="registration-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <p style="margin: 0;"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['errors']); ?>
                </div>
            <?php endif; ?>
            
            <form id="registrationForm" method="post" action="/pages/registration/registration_process_simple.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="form-group">
                    <label for="firstname" class="form-label">
                        Имя <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="firstname" 
                           name="firstname" 
                           class="form-input" 
                           value="<?= isset($oldData['firstname']) ? htmlspecialchars($oldData['firstname']) : '' ?>"
                           required>
                    <div class="error-message" id="firstname-error">Пожалуйста, введите ваше имя</div>
                </div>
                
                <div class="form-group">
                    <label for="lastname" class="form-label">
                        Фамилия <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="lastname" 
                           name="lastname" 
                           class="form-input" 
                           value="<?= isset($oldData['lastname']) ? htmlspecialchars($oldData['lastname']) : '' ?>"
                           required>
                    <div class="error-message" id="lastname-error">Пожалуйста, введите вашу фамилию</div>
                </div>
                
                <div class="form-group">
                    <label for="occupation" class="form-label">
                        Род деятельности <span class="required">*</span>
                    </label>
                    <select name="occupation" id="occupation" class="form-select" required>
                        <option value="">Выберите из списка</option>
                        <option value="Представитель ВУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ВУЗа' ? 'selected' : '' ?>>Представитель ВУЗа</option>
                        <option value="Представитель ССУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ССУЗа' ? 'selected' : '' ?>>Представитель ССУЗа</option>
                        <option value="Представитель школы" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель школы' ? 'selected' : '' ?>>Представитель школы</option>
                        <option value="Родитель" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Родитель' ? 'selected' : '' ?>>Родитель</option>
                        <option value="Учащийся/учащаяся" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Учащийся/учащаяся' ? 'selected' : '' ?>>Учащийся</option>
                        <option value="Другое" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Другое' ? 'selected' : '' ?>>Другое</option>
                    </select>
                    <div class="error-message" id="occupation-error">Пожалуйста, выберите род деятельности</div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        Email адрес <span class="required">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-input" 
                           value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>"
                           required>
                    <div class="error-message" id="email-error">Пожалуйста, введите корректный email</div>
                </div>
                
                <div class="form-group">
                    <label for="newPassword" class="form-label">
                        Пароль <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               id="newPassword" 
                               name="newPassword" 
                               class="form-input" 
                               required>
                        <button type="button" class="password-toggle" id="togglePassword1">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path class="eye-open" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle class="eye-open" cx="12" cy="12" r="3"/>
                                <path class="eye-closed" style="display: none;" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/>
                            </svg>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="error-message" id="password-error">Пароль должен содержать минимум 8 символов</div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword" class="form-label">
                        Подтвердите пароль <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               id="confirmPassword" 
                               name="confirmPassword" 
                               class="form-input" 
                               required>
                        <button type="button" class="password-toggle" id="togglePassword2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path class="eye-open" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle class="eye-open" cx="12" cy="12" r="3"/>
                                <path class="eye-closed" style="display: none;" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/>
                            </svg>
                        </button>
                    </div>
                    <div class="error-message" id="confirmPassword-error">Пароли не совпадают</div>
                </div>
                
                
                <input type="hidden" name="timezone" id="timezone" value="">
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    Зарегистрироваться
                </button>
            </form>
            
            <div class="form-footer">
                <p>Уже есть аккаунт? <a href="/login">Войдите здесь</a></p>
            </div>
        </div>
    </div>
    
    <script>
    // Timezone detection
    (function() {
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        document.getElementById('timezone').value = timezone;
    })();
    
    // Form validation
    const form = document.getElementById('registrationForm');
    const inputs = form.querySelectorAll('.form-input, .form-select');
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
        
        // Password validation
        if (field.id === 'newPassword' && field.value) {
            isValid = field.value.length >= 8;
        }
        
        // Confirm password validation
        if (field.id === 'confirmPassword') {
            const password = document.getElementById('newPassword').value;
            isValid = field.value === password;
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
            submitBtn.textContent = 'Регистрация...';
            form.submit();
        }
    });
    
    // Password visibility toggle
    function setupPasswordToggle(toggleId, inputId) {
        const toggle = document.getElementById(toggleId);
        const input = document.getElementById(inputId);
        
        toggle.addEventListener('click', function() {
            const eyeOpen = this.querySelectorAll('.eye-open');
            const eyeClosed = this.querySelectorAll('.eye-closed');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.forEach(el => el.style.display = 'none');
                eyeClosed.forEach(el => el.style.display = 'block');
            } else {
                input.type = 'password';
                eyeOpen.forEach(el => el.style.display = 'block');
                eyeClosed.forEach(el => el.style.display = 'none');
            }
        });
    }
    
    setupPasswordToggle('togglePassword1', 'newPassword');
    setupPasswordToggle('togglePassword2', 'confirmPassword');
    
    // Password strength indicator
    const passwordInput = document.getElementById('newPassword');
    const strengthContainer = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        if (password.length > 0) {
            strengthContainer.classList.add('show');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            if (strength <= 1) {
                strengthBar.classList.add('weak');
            } else if (strength === 2) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        } else {
            strengthContainer.classList.remove('show');
        }
    });
    
    </script>
</body>
</html>