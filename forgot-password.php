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
    <title>Восстановление пароля - 11-классники</title>
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
        
        .info-text {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
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
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-section">
            <?php 
            require_once __DIR__ . '/includes/components/site-logo.php';
            echo renderSiteLogo(['showText' => false]);
            ?>
        </div>
        <div class="reset-header">
            <h1>Восстановление пароля</h1>
        </div>
        
        <div class="reset-body">
            <?php if (isset($_SESSION['reset_success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['reset_success']) ?>
                    <?php 
                    // Show reset link when email is not configured or fails
                    if (isset($_SESSION['reset_link'])): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 5px; word-break: break-all;">
                            <strong>Ссылка для сброса пароля:</strong><br>
                            <a href="<?= htmlspecialchars($_SESSION['reset_link']) ?>" style="color: #28a745;">
                                <?= htmlspecialchars($_SESSION['reset_link']) ?>
                            </a>
                        </div>
                        <?php unset($_SESSION['reset_link']); ?>
                    <?php endif; ?>
                    
                    <?php // Show info about email configuration
                    if (isset($_SESSION['reset_info'])): ?>
                        <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; color: #856404;">
                            <small><?= htmlspecialchars($_SESSION['reset_info']) ?></small>
                        </div>
                        <?php unset($_SESSION['reset_info']); ?>
                    <?php endif; ?>
                    
                    <?php unset($_SESSION['reset_success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['reset_error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['reset_error']) ?>
                    <?php unset($_SESSION['reset_error']); ?>
                </div>
            <?php endif; ?>
            
            <p class="info-text">
                Введите email адрес, указанный при регистрации. Мы отправим вам инструкции по восстановлению пароля.
            </p>
            
            <form method="post" action="/forgot-password-process.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">Email адрес</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-input" 
                           required
                           placeholder="your@email.com">
                </div>
                
                <button type="submit" class="submit-btn">
                    Отправить инструкции
                </button>
            </form>
            
            <div class="form-footer">
                <p>Вспомнили пароль? <a href="/login">Войти</a></p>
            </div>
        </div>
    </div>
</body>
</html>