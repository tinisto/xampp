#!/usr/bin/env python3
"""
Comprehensive fix for login routing issue on 11klassniki.ru
"""

import ftplib
import os
import sys
from datetime import datetime
import tempfile

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def connect_ftp():
    """Connect to FTP server"""
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        print(f"✓ Connected to FTP server")
        return ftp
    except Exception as e:
        print(f"✗ Failed to connect to FTP: {e}")
        sys.exit(1)

def find_best_login_file(ftp):
    """Find the best login file from all available options"""
    print("\n=== Finding best login file ===")
    
    # Get all files in root directory
    files = []
    ftp.retrlines('LIST', lambda x: files.append(x))
    
    login_files = []
    for file_info in files:
        parts = file_info.split()
        if len(parts) >= 9:
            filename = ' '.join(parts[8:])
            # Look for login PHP files
            if 'login' in filename.lower() and filename.endswith('.php') and not filename.startswith('.'):
                # Skip debug and process files
                if 'debug' not in filename and 'process' not in filename and 'test' not in filename:
                    try:
                        size = ftp.size(filename)
                        if size and size > 100:  # Must be a real file, not empty
                            login_files.append((filename, size))
                            print(f"  Found: {filename} ({size} bytes)")
                    except:
                        pass
    
    if not login_files:
        return None
    
    # Prioritize files
    priority_order = [
        'login.php',
        'login-new.php', 
        'login-simple.php',
        'login-modern.php',
        'login-template.php'
    ]
    
    # Try priority files first
    for priority_file in priority_order:
        for file, size in login_files:
            if file == priority_file:
                print(f"\n✓ Selected priority file: {file}")
                return file
    
    # If no priority file, use the largest one (likely most complete)
    login_files.sort(key=lambda x: x[1], reverse=True)
    selected = login_files[0][0]
    print(f"\n✓ Selected largest file: {selected}")
    return selected

def create_login_standalone_copy(ftp, source_file):
    """Create login-standalone.php by copying existing login file"""
    print(f"\n=== Creating login-standalone.php from {source_file} ===")
    
    try:
        # Download source file
        content_lines = []
        ftp.retrlines(f'RETR {source_file}', content_lines.append)
        content = '\n'.join(content_lines)
        print(f"✓ Downloaded {source_file} ({len(content_lines)} lines)")
        
        # Write to temporary file
        with tempfile.NamedTemporaryFile(mode='w', delete=False) as f:
            f.write(content)
            temp_file = f.name
        
        # Upload as login-standalone.php
        with open(temp_file, 'rb') as f:
            ftp.storbinary('STOR login-standalone.php', f)
        print("✓ Created login-standalone.php")
        
        # Clean up
        os.unlink(temp_file)
        return True
        
    except Exception as e:
        print(f"✗ Failed to create login-standalone.php: {e}")
        return False

def create_simple_login_standalone(ftp):
    """Create a simple login-standalone.php file"""
    print("\n=== Creating simple login-standalone.php ===")
    
    login_content = """<?php
session_start();
require_once 'database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /account/');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: /account/');
                exit();
            } else {
                $error = 'Неверный email или пароль';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка при входе. Попробуйте позже.';
        }
    } else {
        $error = 'Заполните все поля';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - 11klassniki.ru</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Вход</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/login/">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Войти</button>
        </form>
        
        <p><a href="/registration/">Регистрация</a> | <a href="/forgot-password/">Забыли пароль?</a></p>
    </div>
</body>
</html>"""
    
    # Write to temporary file
    with tempfile.NamedTemporaryFile(mode='w', delete=False) as f:
        f.write(login_content)
        temp_file = f.name
    
    # Upload to server
    try:
        with open(temp_file, 'rb') as f:
            ftp.storbinary('STOR login-standalone.php', f)
        print("✓ Created simple login-standalone.php")
        
        # Clean up
        os.unlink(temp_file)
        return True
    except Exception as e:
        print(f"✗ Failed to create login-standalone.php: {e}")
        return False

def verify_fix(ftp):
    """Verify the fix worked"""
    print("\n=== Verifying fix ===")
    
    # Check login-standalone.php exists
    try:
        size = ftp.size('login-standalone.php')
        if size is not None:
            print(f"✓ login-standalone.php exists (size: {size} bytes)")
            
            # Download .htaccess to verify routing
            htaccess_lines = []
            ftp.retrlines('RETR .htaccess', htaccess_lines.append)
            
            for line in htaccess_lines:
                if 'RewriteRule ^login/?$ login-standalone.php' in line:
                    print("✓ .htaccess routing is correct")
                    return True
                    
            print("✗ .htaccess routing not found")
            return False
    except:
        print("✗ login-standalone.php not found")
        return False

def main():
    print("=== Comprehensive Login Routing Fix ===")
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    ftp = connect_ftp()
    
    try:
        # Find best existing login file
        best_login_file = find_best_login_file(ftp)
        
        if best_login_file:
            # Option 1: Copy existing login file to login-standalone.php
            if create_login_standalone_copy(ftp, best_login_file):
                print("\n✓ Successfully created login-standalone.php from existing file")
            else:
                print("\n⚠️  Failed to copy, creating simple login file instead")
                create_simple_login_standalone(ftp)
        else:
            # Option 2: Create a simple login-standalone.php
            print("\n⚠️  No existing login files found, creating simple login file")
            create_simple_login_standalone(ftp)
        
        # Verify the fix
        if verify_fix(ftp):
            print("\n✅ LOGIN ROUTING FIXED!")
            print("\nYou can now access the login page at:")
            print("  https://11klassniki.ru/login/")
            print("  https://11klassniki.ru/login")
        else:
            print("\n⚠️  Fix may not have worked completely")
            print("Please check manually")
        
    finally:
        ftp.quit()
        print("\n✓ FTP connection closed")

if __name__ == "__main__":
    main()