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
    print("🔧 Creating functional login system...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create a functional login page that connects to existing system
        functional_login = '''<?php
// Start output buffering to prevent header issues
ob_start();

// Error reporting (can be removed in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users

// Try to start session safely
if (session_status() == PHP_SESSION_NONE) {
    try {
        session_start();
    } catch (Exception $e) {
        // If session fails, continue without it
    }
}

// Check if user is already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error_message = 'Пожалуйста, заполните все поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Пожалуйста, введите корректный email';
    } else {
        // Try to connect to database and authenticate
        try {
            // Look for database connection file
            $db_file = null;
            $possible_db_files = [
                'database.php',
                'includes/database.php',
                'common/database.php',
                'config/database.php',
                'db.php',
                'includes/db.php'
            ];
            
            foreach ($possible_db_files as $file) {
                if (file_exists($file)) {
                    $db_file = $file;
                    break;
                }
            }
            
            if ($db_file) {
                include_once $db_file;
                
                // Try to get database connection
                if (isset($conn) && $conn) {
                    // Prepare statement to prevent SQL injection
                    $stmt = $conn->prepare("SELECT id, email, password, first_name, last_name FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result && $result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        
                        // Verify password (supports both hash and plain text for compatibility)
                        $password_valid = false;
                        if (password_verify($password, $user['password'])) {
                            $password_valid = true;
                        } elseif ($password === $user['password']) {
                            // Fallback for plain text passwords (should be updated)
                            $password_valid = true;
                        }
                        
                        if ($password_valid) {
                            // Login successful
                            if (session_status() == PHP_SESSION_ACTIVE) {
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['user_email'] = $user['email'];
                                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                            }
                            
                            // Redirect to account page
                            header('Location: /account');
                            exit();
                        } else {
                            $error_message = 'Неверный email или пароль';
                        }
                    } else {
                        $error_message = 'Неверный email или пароль';
                    }
                    
                    $stmt->close();
                } else {
                    $error_message = 'Ошибка подключения к базе данных';
                }
            } else {
                $error_message = 'Система временно недоступна. Попробуйте позже.';
            }
        } catch (Exception $e) {
            $error_message = 'Произошла ошибка. Попробуйте позже.';
        }
    }
}
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - 11klassniki.ru</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
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
            transition: transform 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
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
        }
        .links a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <a href="/">11</a>
        </div>
        
        <h1>Вход</h1>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
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
<?php
// End output buffering and send content
ob_end_flush();
?>'''
        
        # Upload the functional login page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(functional_login)
            tmp_path = tmp.name
        
        print("📤 Uploading functional login-standalone.php...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-standalone.php', file)
        
        # Also upload as login-template.php for .htaccess routing
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-template.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Functional login system deployed!")
        print("\n📋 Features implemented:")
        print("• Real database authentication")
        print("• Session management") 
        print("• Password verification (hashed and plain text)")
        print("• Form validation and error handling")
        print("• Redirect to /account after successful login")
        print("• Beautiful responsive design")
        print("• XSS protection and secure coding")
        
        print("\n🧪 Test the login:")
        print("https://11klassniki.ru/login/")
        print("https://11klassniki.ru/login-standalone.php")
        
        print("\n🔧 Login will now:")
        print("1. Check credentials against users table")
        print("2. Set session variables on success")
        print("3. Redirect to /account page")
        print("4. Show error messages for invalid credentials")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()