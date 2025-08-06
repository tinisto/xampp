<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/site-logo.css">
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
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .input-group {
            position: relative;
            display: flex;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 45px 10px 14px;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
            transition: var(--transition);
            background-color: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
        }
        
        .input-group-text {
            position: absolute;
            right: 1px;
            top: 1px;
            bottom: 1px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            border-radius: 0 6px 6px 0;
        }
        
        .input-group-text:hover {
            color: var(--text-primary);
        }
        
        .btn-success {
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
        
        .btn-success:hover {
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
        
        .text-danger {
            color: var(--danger-color);
            font-size: 13px;
            margin-top: 5px;
        }
        
        .text-muted {
            color: var(--text-secondary);
            font-size: 12px;
            margin-top: 5px;
        }
        
        .fas {
            font-family: sans-serif;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-section">
            <?php 
            include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
            renderSiteIcon('medium', '/', 'reset-logo');
            ?>
        </div>
        
        <div class="reset-header">
            <h1>Подтверждение сброса пароля</h1>
        </div>
        
        <div class="reset-body">
            <?php
            $isValidToken = true; // Placeholder for database validation
            
            if ($isValidToken) {
                ?>
                <form action="/reset-password-confirm-process" method="post" id="resetPasswordForm">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="hidden" name="email" value="<?php echo $email; ?>">
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Новый пароль</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Введите ваш новый пароль" required minlength="8">
                            <span class="input-group-text" id="togglePassword">
                                <i class="fas fa-eye">👁</i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Подтвердите пароль</label>
                        <div class="input-group">
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control"
                                placeholder="Повторите ваш новый пароль" required minlength="8">
                            <span class="input-group-text" id="toggleConfirmPassword">
                                <i class="fas fa-eye">👁</i>
                            </span>
                        </div>
                    </div>
                    
                    <div id="passwordError" class="text-danger" style="display: none; margin-bottom: 15px;">Пароли не совпадают.</div>
                    <div class="text-muted" style="margin-bottom: 20px;">
                        Пароль должен содержать минимум 8 символов, включая заглавную букву, строчную букву, цифру и специальный символ
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <span class="fw-bold">Сохранить новый пароль</span>
                    </button>
                </form>
                <?php
            } else {
                ?>
                <div class="alert alert-danger" role="alert">
                    Неверный или устаревший токен. Пожалуйста, запросите сброс пароля снова.
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle password visibility
        function togglePasswordVisibility(passwordId, toggleId) {
            var passwordInput = document.getElementById(passwordId);
            var icon = document.getElementById(toggleId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.innerHTML = '<i class="fas fa-eye-slash">🙈</i>';
            } else {
                passwordInput.type = 'password';
                icon.innerHTML = '<i class="fas fa-eye">👁</i>';
            }
        }
        
        document.getElementById('togglePassword').addEventListener('click', function () {
            togglePasswordVisibility('password', 'togglePassword');
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword');
        });
        
        // Validate password match
        const form = document.getElementById('resetPasswordForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const passwordError = document.getElementById('passwordError');
        
        form.addEventListener('submit', function (event) {
            let error = '';
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/;
            
            if (password.value.length < 8) {
                error = 'Пароль должен содержать минимум 8 символов.';
            } else if (!passwordRegex.test(password.value)) {
                error = 'Пароль должен содержать минимум одну строчную букву, одну заглавную букву, одну цифру и один специальный символ.';
            } else if (password.value !== confirmPassword.value) {
                error = 'Пароли не совпадают.';
            }
            
            if (error) {
                passwordError.textContent = error;
                passwordError.style.display = 'block';
                event.preventDefault();
            } else {
                passwordError.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>