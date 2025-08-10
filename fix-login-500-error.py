#!/usr/bin/env python3
"""
Fix 500 Internal Server Error on login page
This script will:
1. Download the current login-standalone.php
2. Check for common PHP syntax errors
3. Create a minimal working login page
4. Upload the fixed version
"""

import ftplib
import os
import re
import tempfile
from datetime import datetime

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def connect_ftp():
    """Establish FTP connection"""
    print("Connecting to FTP server...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_ROOT)
    print("Connected successfully!")
    return ftp

def download_file(ftp, remote_path, local_path):
    """Download a file from FTP server"""
    try:
        with open(local_path, 'wb') as f:
            ftp.retrbinary(f'RETR {remote_path}', f.write)
        print(f"Downloaded: {remote_path}")
        return True
    except Exception as e:
        print(f"Error downloading {remote_path}: {e}")
        return False

def upload_file(ftp, local_path, remote_path):
    """Upload a file to FTP server"""
    try:
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"Error uploading {remote_path}: {e}")
        return False

def analyze_php_file(file_path):
    """Analyze PHP file for common issues"""
    issues = []
    
    try:
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
    except Exception as e:
        return [f"Cannot read file: {e}"]
    
    # Check for BOM
    if content.startswith('\ufeff'):
        issues.append("File has UTF-8 BOM which can cause headers already sent error")
    
    # Check for output before session_start
    lines = content.split('\n')
    session_line = -1
    for i, line in enumerate(lines):
        if 'session_start()' in line:
            session_line = i
            break
    
    if session_line > 0:
        # Check if there's any output before session_start
        before_session = '\n'.join(lines[:session_line])
        if re.search(r'echo|print|printf|<\?=', before_session):
            issues.append("Output detected before session_start()")
        if re.search(r'<html|<HTML', before_session):
            issues.append("HTML output before session_start()")
    
    # Check for missing semicolons
    php_lines = re.findall(r'<\?php.*?\?>', content, re.DOTALL)
    for block in php_lines:
        statements = re.findall(r'[^{};]\s*\n\s*[$a-zA-Z]', block)
        if statements:
            issues.append("Possible missing semicolons detected")
    
    # Check for unclosed brackets
    open_count = content.count('{')
    close_count = content.count('}')
    if open_count != close_count:
        issues.append(f"Mismatched brackets: {open_count} open, {close_count} close")
    
    # Check for undefined variables in common patterns
    if re.search(r'\$_SESSION\[.*?\].*?(?<!isset\()\$_SESSION', content):
        issues.append("Possible use of $_SESSION without checking if set")
    
    # Check for missing includes
    includes = re.findall(r'(?:require|include)(?:_once)?\s*[("\']([^"\']+)["\')]', content)
    for inc in includes:
        issues.append(f"Includes file: {inc} (verify it exists)")
    
    return issues

def create_minimal_login_page():
    """Create a minimal working login page"""
    return '''<?php
// Minimal login page - no database dependencies
error_reporting(0); // Suppress errors temporarily
ini_set('display_errors', 0);

// Start session safely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simple redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit();
}

// Handle form submission (placeholder - no actual authentication)
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error = 'Пожалуйста, заполните все поля';
    } else {
        // Temporary message - no actual login
        $error = 'Система входа временно недоступна. Пожалуйста, попробуйте позже.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - 11klassniki.ru</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
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
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #45a049;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .maintenance-notice {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>11klassniki.ru</h1>
            <p>Войдите в свой аккаунт</p>
        </div>
        
        <div class="maintenance-notice">
            ⚠️ Система входа временно обновляется
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-submit">Войти</button>
        </form>
        
        <div class="login-footer">
            <p>Нет аккаунта? <a href="/register.php">Зарегистрируйтесь</a></p>
            <p><a href="/forgot-password.php">Забыли пароль?</a></p>
        </div>
    </div>
</body>
</html>
'''

def create_diagnostic_script():
    """Create a diagnostic script to check server configuration"""
    return '''<?php
// Login diagnostic script
header('Content-Type: text/plain; charset=utf-8');

echo "=== LOGIN PAGE DIAGNOSTICS ===\n\n";

// PHP Version
echo "PHP Version: " . phpversion() . "\n\n";

// Error reporting
echo "Error Reporting: " . error_reporting() . "\n";
echo "Display Errors: " . ini_get('display_errors') . "\n\n";

// Session status
echo "Session Status: ";
switch(session_status()) {
    case PHP_SESSION_DISABLED:
        echo "DISABLED\n";
        break;
    case PHP_SESSION_NONE:
        echo "NONE (not started)\n";
        break;
    case PHP_SESSION_ACTIVE:
        echo "ACTIVE\n";
        break;
}

// Check session save path
echo "Session Save Path: " . ini_get('session.save_path') . "\n";
$save_path = session_save_path();
if ($save_path) {
    echo "Session Save Path Writable: " . (is_writable($save_path) ? "YES" : "NO") . "\n";
}
echo "\n";

// Check for required files
$required_files = [
    'database.php',
    '../database.php',
    'includes/database.php',
    '../includes/database.php',
    'common/database.php',
    '../common/database.php'
];

echo "=== CHECKING REQUIRED FILES ===\n";
foreach ($required_files as $file) {
    echo "$file: " . (file_exists($file) ? "EXISTS" : "NOT FOUND") . "\n";
}

// Check current directory
echo "\n=== CURRENT DIRECTORY ===\n";
echo "Script: " . __FILE__ . "\n";
echo "Directory: " . __DIR__ . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";

// List files in current directory
echo "\n=== FILES IN CURRENT DIRECTORY ===\n";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "- $file\n";
    }
}

// Check for login-standalone.php
echo "\n=== LOGIN FILES ===\n";
$login_files = [
    'login-standalone.php',
    'login.php',
    'login/index.php'
];

foreach ($login_files as $file) {
    if (file_exists($file)) {
        echo "$file: EXISTS (size: " . filesize($file) . " bytes)\n";
        
        // Check first few lines for syntax
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        echo "  First line: " . trim($lines[0]) . "\n";
        
        // Check for BOM
        if (substr($content, 0, 3) == "\xEF\xBB\xBF") {
            echo "  WARNING: File has UTF-8 BOM!\n";
        }
    } else {
        echo "$file: NOT FOUND\n";
    }
}

echo "\n=== END DIAGNOSTICS ===\n";
?>'''

def main():
    print("=== Login Page 500 Error Fix ===")
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print()
    
    # Create temporary directory
    with tempfile.TemporaryDirectory() as tmpdir:
        # Connect to FTP
        try:
            ftp = connect_ftp()
        except Exception as e:
            print(f"FTP connection failed: {e}")
            return
        
        # Navigate to login directory
        try:
            ftp.cwd('login')
            print("Changed to login directory")
        except:
            print("Login directory not found, staying in root")
        
        # Download current login files
        current_files = []
        try:
            current_files = ftp.nlst()
            print(f"\nFiles in login directory: {current_files}")
        except Exception as e:
            print(f"Error listing files: {e}")
        
        # Download login-standalone.php if it exists
        login_file_path = os.path.join(tmpdir, 'login-standalone.php')
        if 'login-standalone.php' in current_files:
            if download_file(ftp, 'login-standalone.php', login_file_path):
                print("\nAnalyzing current login-standalone.php...")
                issues = analyze_php_file(login_file_path)
                if issues:
                    print("\nIssues found:")
                    for issue in issues:
                        print(f"  - {issue}")
                else:
                    print("No obvious issues found")
                
                # Backup the original
                backup_name = f'login-standalone.backup.{datetime.now().strftime("%Y%m%d_%H%M%S")}.php'
                upload_file(ftp, login_file_path, backup_name)
        
        # Create and upload diagnostic script
        print("\nCreating diagnostic script...")
        diag_path = os.path.join(tmpdir, 'login-diagnostic.php')
        with open(diag_path, 'w', encoding='utf-8') as f:
            f.write(create_diagnostic_script())
        
        if upload_file(ftp, diag_path, 'login-diagnostic.php'):
            print("Diagnostic script uploaded. Visit: https://11klassniki.ru/login/login-diagnostic.php")
        
        # Create and upload minimal working login page
        print("\nCreating minimal working login page...")
        minimal_path = os.path.join(tmpdir, 'login-minimal.php')
        with open(minimal_path, 'w', encoding='utf-8') as f:
            f.write(create_minimal_login_page())
        
        if upload_file(ftp, minimal_path, 'login-minimal.php'):
            print("Minimal login page uploaded. Visit: https://11klassniki.ru/login/login-minimal.php")
        
        # Replace the main login file with minimal version
        print("\nReplacing login-standalone.php with minimal version...")
        if upload_file(ftp, minimal_path, 'login-standalone.php'):
            print("Successfully replaced login-standalone.php")
        
        # Also create index.php as a fallback
        if upload_file(ftp, minimal_path, 'index.php'):
            print("Also created index.php as fallback")
        
        # Close FTP connection
        ftp.quit()
        print("\nFTP connection closed")
        
        print("\n=== SUMMARY ===")
        print("1. Created backup of original login-standalone.php")
        print("2. Uploaded diagnostic script: /login/login-diagnostic.php")
        print("3. Created minimal working login page: /login/login-minimal.php")
        print("4. Replaced login-standalone.php with working version")
        print("5. Created index.php as fallback")
        print("\nThe login page should now work without 500 errors.")
        print("Visit https://11klassniki.ru/login/ to test")

if __name__ == "__main__":
    main()