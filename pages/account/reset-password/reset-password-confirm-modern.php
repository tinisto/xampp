<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get token and email from URL
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

$error = '';
$success = false;

// Validate token
if (empty($token) || empty($email)) {
    $error = 'Недействительная ссылка для сброса пароля.';
} else {
    // Check if token is valid
    $hashedToken = hash('sha256', $token);
    
    $stmt = $connection->prepare("
        SELECT pr.*, u.email 
        FROM password_resets pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.token = ? AND u.email = ? AND pr.expires_at > NOW() AND pr.used = FALSE
    ");
    $stmt->bind_param("ss", $hashedToken, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = 'Ссылка для сброса пароля недействительна или устарела.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($newPassword)) {
        $error = 'Введите новый пароль.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'Пароль должен содержать минимум 8 символов.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/', $newPassword)) {
        $error = 'Пароль должен содержать минимум одну строчную букву, одну заглавную букву, одну цифру и один специальный символ.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Пароли не совпадают.';
    } else {
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Get user ID
        $stmt = $connection->prepare("
            SELECT u.id 
            FROM users u 
            JOIN password_resets pr ON u.id = pr.user_id 
            WHERE pr.token = ? AND u.email = ?
        ");
        $stmt->bind_param("ss", $hashedToken, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            // Update password
            $stmt = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $user['id']);
            $stmt->execute();
            
            // Mark token as used
            $stmt = $connection->prepare("UPDATE password_resets SET used = TRUE WHERE token = ?");
            $stmt->bind_param("s", $hashedToken);
            $stmt->execute();
            
            $success = true;
            $_SESSION['password_reset_success'] = 'Пароль успешно изменен! Теперь вы можете войти с новым паролем.';
        }
    }
}
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
        
        .input-group {
            position: relative;
            display: flex;
        }
        
        .form-input {
            width: 100%;
            padding: 10px 14px;
            padding-right: 45px;
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
        
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
            padding: 5px;
        }
        
        .toggle-password:hover {
            color: var(--text-primary);
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
        
        .password-error {
            color: var(--danger-color);
            font-size: 13px;
            margin-top: 5px;
            display: none;
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
        
        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">✓</div>
                <h2>Пароль успешно изменен!</h2>
                <p>Теперь вы можете войти с новым паролем.</p>
                <a href="/login" class="btn-login">Перейти к входу</a>
            </div>
        <?php else: ?>
            <div class="reset-header">
                <h1>Создайте новый пароль</h1>
            </div>
            
            <div class="reset-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($error) || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <form method="post" id="resetPasswordForm" action="/reset-password-confirm-process">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Новый пароль</label>
                            <div class="input-group">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-input" 
                                       required
                                       minlength="8">
                                <span class="toggle-password" id="togglePassword">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>
                            <p class="password-requirements">Минимум 8 символов, включая заглавную букву, строчную букву, цифру и специальный символ</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword" class="form-label">Подтвердите пароль</label>
                            <div class="input-group">
                                <input type="password" 
                                       id="confirmPassword" 
                                       name="confirmPassword" 
                                       class="form-input" 
                                       required
                                       minlength="8">
                                <span class="toggle-password" id="toggleConfirmPassword">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>
                            <div id="passwordError" class="password-error"></div>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            Изменить пароль
                        </button>
                    </form>
                <?php else: ?>
                    <p style="text-align: center; margin: 20px 0;">
                        <a href="/forgot-password" style="color: var(--primary-color); text-decoration: none;">
                            Запросить новую ссылку для сброса пароля
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        function togglePasswordVisibility(passwordId, toggleId) {
            const passwordInput = document.getElementById(passwordId);
            const toggleBtn = document.getElementById(toggleId);
            
            toggleBtn.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                } else {
                    passwordInput.type = 'password';
                    toggleBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                }
            });
        }
        
        togglePasswordVisibility('password', 'togglePassword');
        togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword');
        
        // Form validation
        const form = document.getElementById('resetPasswordForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const passwordError = document.getElementById('passwordError');
        
        form.addEventListener('submit', function(event) {
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