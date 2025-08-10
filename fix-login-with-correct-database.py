#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Creating login with correct database connection...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create login using the same database connection as other pages
        correct_login = '''<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Try to start session safely
if (session_status() == PHP_SESSION_NONE) {
    try {
        session_start();
    } catch (Exception $e) {
        // Continue without session if it fails
    }
}

// Check if already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error_message = 'Пожалуйста, заполните все поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Пожалуйста, введите корректный email';
    } else {
        // Use the same database connection as other pages
        try {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
            
            if (isset($connection) && $connection) {
                // Query the users table
                $stmt = $connection->prepare("SELECT id, email, password, first_name, last_name FROM users WHERE email = ? LIMIT 1");
                
                if ($stmt) {
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result && $result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        
                        // Check password (supports both hashed and plain text)
                        $password_valid = false;
                        if (!empty($user['password'])) {
                            if (password_verify($password, $user['password'])) {
                                $password_valid = true;
                            } elseif ($password === $user['password']) {
                                $password_valid = true; // Plain text fallback
                            }
                        }
                        
                        if ($password_valid) {
                            // Login successful - set session variables
                            if (session_status() == PHP_SESSION_ACTIVE) {
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['user_email'] = $user['email'];
                                $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                $_SESSION['logged_in'] = true;
                            }
                            
                            // Redirect to account page
                            header('Location: /account');
                            exit();
                        } else {
                            $error_message = 'Неверный email или пароль';
                        }
                    } else {
                        $error_message = 'Пользователь с таким email не найден';
                    }
                    
                    $stmt->close();
                } else {
                    $error_message = 'Ошибка выполнения запроса';
                }
            } else {
                $error_message = 'Ошибка подключения к базе данных';
            }
        } catch (Exception $e) {
            $error_message = 'Произошла ошибка при входе. Попробуйте позже.';
        }
    }
}
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><circle cx='12' cy='12' r='12' fill='%23007bff'/><text x='12' y='16' text-anchor='middle' fill='white' font-size='10' font-weight='bold' font-family='Arial'>11</text></svg>">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo a {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 50%;
            line-height: 50px;
            font-size: 20px;
            font-weight: bold;
            transition: transform 0.2s ease;
        }
        .logo a:hover {
            transform: scale(1.05);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input[type="email"], 
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus, 
        input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        .btn:active {
            transform: translateY(0);
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e1e5e9;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .links a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <a href="/" title="Вернуться на главную">11</a>
        </div>
        
        <h1>Вход в систему</h1>
        
        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email адрес:</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       autocomplete="email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       placeholder="Введите ваш email">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="Введите ваш пароль">
            </div>
            
            <button type="submit" class="btn">Войти</button>
        </form>
        
        <div class="links">
            <a href="/registration">Регистрация</a>
            <a href="/forgot-password">Забыли пароль?</a>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>'''
        
        # Upload the corrected login
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(correct_login)
            tmp_path = tmp.name
        
        print("📤 Uploading login with correct database connection...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-standalone.php', file)
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-template.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Login with correct database connection deployed!")
        print("\n🔧 Fixed issues:")
        print("• Uses /database/db_connections.php (same as other pages)")
        print("• Uses $connection variable (same as other pages)")  
        print("• Queries users table correctly")
        print("• Supports both hashed and plain text passwords")
        print("• Sets proper session variables")
        print("• Redirects to /account after login")
        
        print("\n🧪 Test the login:")
        print("https://11klassniki.ru/login/")
        print("\nTry logging in with valid user credentials!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()