<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

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
    <title>Сброс пароля - 11классники</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: var(--bg-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-primary);
            line-height: 1.5;
        }
        
        .auth-container {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 440px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-link {
            display: inline-block;
            text-decoration: none;
            color: var(--primary-color);
            transition: var(--transition);
        }
        
        .logo-link:hover {
            transform: scale(1.05);
        }
        
        .logo-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
        }
        
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }
        
        .form-grid {
            display: grid;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 500;
            font-size: 14px;
        }
        
        .input-wrapper {
            position: relative;
            display: flex;
        }
        
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 40px 12px 14px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
            background: white;
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 5px;
            transition: var(--transition);
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .password-toggle svg {
            width: 20px;
            height: 20px;
        }
        
        .password-requirements {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        /* Success state */
        .success-message {
            text-align: center;
            padding: 20px 0;
        }
        
        .success-icon {
            font-size: 60px;
            color: var(--primary-color);
            margin-bottom: 20px;
            line-height: 1;
        }
        
        .success-message h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .success-message p {
            color: var(--text-secondary);
            margin-bottom: 25px;
            font-size: 16px;
        }
        
        /* Eye icon SVGs */
        .eye-open {
            display: block;
        }
        
        .eye-closed {
            display: none;
        }
        
        .password-visible .eye-open {
            display: none;
        }
        
        .password-visible .eye-closed {
            display: block;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo-section">
            <a href="/" class="logo-link">
                <div class="logo-placeholder">
                    <svg width="60" height="60" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2"/>
                        <text x="20" y="26" text-anchor="middle" fill="currentColor" font-size="18" font-weight="bold">11</text>
                    </svg>
                </div>
            </a>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">✓</div>
                <h2>Пароль успешно изменен!</h2>
                <p>Теперь вы можете войти с новым паролем.</p>
                <a href="/login" class="btn btn-primary">Перейти к входу</a>
            </div>
        <?php else: ?>
            <h1>Создайте новый пароль</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($error) || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <form method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="password">Новый пароль</label>
                            <div class="input-wrapper">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required
                                       minlength="6"
                                       placeholder="Минимум 6 символов">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <div class="password-requirements">Минимум 6 символов</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Подтвердите пароль</label>
                            <div class="input-wrapper">
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required
                                       minlength="6"
                                       placeholder="Повторите пароль">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        Изменить пароль
                    </button>
                </form>
                
                <div class="form-footer">
                    <a href="/login">Вернуться к входу</a>
                </div>
            <?php else: ?>
                <div class="form-footer">
                    <a href="/forgot-password">Запросить новую ссылку для сброса пароля</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const wrapper = field.closest('.input-wrapper');
            
            if (field.type === 'password') {
                field.type = 'text';
                wrapper.classList.add('password-visible');
            } else {
                field.type = 'password';
                wrapper.classList.remove('password-visible');
            }
        }
    </script>
</body>
</html>