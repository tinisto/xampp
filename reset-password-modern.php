<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Get token and email from URL
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

$error = '';
$success = false;

// Get current theme
$currentTheme = $_COOKIE['theme'] ?? 'light';

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

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="ru" data-theme="<?= htmlspecialchars($currentTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - 11-классники</title>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php'; ?>
    
    <!-- Theme Variables -->
    <link rel="stylesheet" href="/css/theme-variables.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Login Fix CSS -->
    <link rel="stylesheet" href="/css/login-fix.css">
    
    <style>
        /* Base styles using CSS variables */
        body {
            background-color: var(--color-surface-primary);
            color: var(--color-text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .form-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        
        .form-wrapper {
            width: 100%;
            max-width: 500px;
        }
        
        .form-card {
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-border-primary);
            border-radius: 0.375rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem var(--color-shadow-sm);
        }
        
        .form-title {
            color: var(--color-primary);
            font-weight: 600;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .btn-submit {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
            font-weight: bold;
            padding: 10px 30px;
            display: block;
            width: 100%;
            margin-top: 1.5rem;
        }
        
        .btn-submit:hover {
            background-color: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
        }
        
        .form-link {
            color: var(--color-link);
            text-decoration: none;
        }
        
        .form-link:hover {
            color: var(--color-link-hover);
            text-decoration: underline;
        }
        
        /* Form controls */
        .form-control {
            background-color: var(--color-surface-secondary);
            color: var(--color-text-primary);
            border-color: var(--color-border-primary);
            padding: 0.5rem 0.75rem;
        }
        
        .form-control:focus {
            background-color: var(--color-surface-primary);
            color: var(--color-text-primary);
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .form-control::placeholder {
            color: var(--color-text-tertiary);
            opacity: 1;
        }
        
        /* Logo section */
        .logo-section {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .logo-link {
            display: inline-block;
            text-decoration: none;
            color: var(--color-primary);
            transition: transform 0.3s ease;
        }
        
        .logo-link:hover {
            transform: scale(1.05);
            color: var(--color-primary-hover);
        }
        
        .logo-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
        }
        
        /* Success state */
        .success-message {
            text-align: center;
            padding: 2rem 1rem;
        }
        
        .success-icon {
            font-size: 4rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }
        
        .success-message h2 {
            color: var(--color-primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .success-message p {
            color: var(--color-text-secondary);
            margin-bottom: 1.5rem;
        }
        
        /* Password requirements */
        .password-requirements {
            font-size: 0.875rem;
            color: var(--color-text-secondary);
            margin-top: 0.25rem;
        }
        
        /* Alerts */
        .alert {
            background-color: var(--color-alert-bg);
            border-color: var(--color-alert-border);
            color: var(--color-alert-text);
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        /* Input group for password visibility toggle */
        .input-group > .form-control {
            border-right: none;
        }
        
        .input-group-text {
            background: var(--color-surface-secondary);
            color: var(--color-text-secondary);
            border-left: none;
            border-color: var(--color-border-primary);
            cursor: pointer;
        }
        
        .input-group-text:hover {
            background: var(--color-bg-hover);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-card">
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
                        <a href="/login" class="btn btn-primary btn-submit">Перейти к входу</a>
                    </div>
                <?php else: ?>
                    <h1 class="form-title">Создайте новый пароль</h1>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($error) || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Новый пароль</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           required
                                           minlength="6"
                                           placeholder="Минимум 6 символов">
                                    <span class="input-group-text" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </span>
                                </div>
                                <div class="password-requirements">Минимум 6 символов</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Подтвердите пароль</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           required
                                           minlength="6"
                                           placeholder="Повторите пароль">
                                    <span class="input-group-text" onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye" id="confirm_password-eye"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-submit">
                                Изменить пароль
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center mt-3">
                            <a href="/forgot-password" class="form-link">
                                Запросить новую ссылку для сброса пароля
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>