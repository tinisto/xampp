#!/usr/bin/env python3
"""
Deploy a simple login page without session dependencies
"""

import ftplib
import os
import sys
from io import BytesIO

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def create_simple_login_page():
    """Create a simple login page without session dependencies"""
    return '''<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему - 11klassniki.ru</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color: #45a049;
        }
        
        .message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .message.error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
        }
        
        .message.success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>11klassniki.ru</h1>
            <p>Вход в панель управления</p>
        </div>
        
        <?php
        // Simple form handling without sessions
        $message = '';
        $messageType = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            // Simple validation
            if (empty($username) || empty($password)) {
                $message = 'Пожалуйста, заполните все поля';
                $messageType = 'error';
            } else {
                // For now, just show a message
                // In production, this would validate against database
                $message = 'Форма отправлена. Логин: ' . htmlspecialchars($username);
                $messageType = 'success';
            }
        }
        
        if (!empty($message)) {
            echo '<div class="message ' . $messageType . '">' . $message . '</div>';
        }
        ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="submit-btn">Войти</button>
        </form>
        
        <div class="links">
            <a href="/">Вернуться на главную</a>
        </div>
    </div>
</body>
</html>'''

def create_simple_index():
    """Create a simple index.php that redirects to login-standalone.php"""
    return '''<?php
// Simple redirect without using sessions
header('Location: login-standalone.php');
exit;
?>'''

def upload_file_via_ftp(ftp, remote_path, content):
    """Upload a single file via FTP"""
    try:
        bio = BytesIO(content.encode('utf-8'))
        ftp.storbinary(f'STOR {remote_path}', bio)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    print("Starting deployment of simple login page...")
    print("=" * 50)
    
    # Connect to FTP
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        print("✓ Connected to FTP server")
    except Exception as e:
        print(f"✗ Failed to connect to FTP: {str(e)}")
        sys.exit(1)
    
    # Ensure login directory exists
    try:
        ftp.cwd('login')
    except:
        try:
            ftp.mkd('login')
            ftp.cwd('login')
            print("✓ Created login directory")
        except:
            print("✗ Failed to create/access login directory")
            sys.exit(1)
    
    # Upload files
    files_to_upload = [
        ('login-standalone.php', create_simple_login_page()),
        ('index.php', create_simple_index())
    ]
    
    success_count = 0
    for filename, content in files_to_upload:
        if upload_file_via_ftp(ftp, filename, content):
            success_count += 1
    
    # Close FTP connection
    ftp.quit()
    
    print("=" * 50)
    print(f"Deployment complete: {success_count}/{len(files_to_upload)} files uploaded")
    print("\nYou can now access the login page at:")
    print("- http://11klassniki.ru/login/")
    print("- http://11klassniki.ru/login/login-standalone.php")
    print("\nThis simple version does not use sessions and should work despite the session path issue.")

if __name__ == "__main__":
    main()