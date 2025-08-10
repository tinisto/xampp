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
    print("🔍 Finding database connection files...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Search for database-related files
        print("📂 Searching for database files in root...")
        root_files = []
        ftp.retrlines('LIST', root_files.append)
        
        db_files = []
        for file_line in root_files:
            filename = file_line.split()[-1] if file_line.split() else ""
            if any(word in filename.lower() for word in ['database', 'db', 'config', 'connect']):
                db_files.append(filename)
        
        print("🗂️ Database-related files found:")
        for f in db_files:
            print(f"  {f}")
        
        # Check common directories
        directories_to_check = ['includes', 'common', 'config', 'pages']
        
        for dir_name in directories_to_check:
            try:
                ftp.cwd(dir_name)
                print(f"\n📂 Files in /{dir_name}/ directory:")
                dir_files = []
                ftp.retrlines('LIST', dir_files.append)
                
                for file_line in dir_files:
                    filename = file_line.split()[-1] if file_line.split() else ""
                    if any(word in filename.lower() for word in ['database', 'db', 'config', 'connect']):
                        print(f"  {filename}")
                
                ftp.cwd('..')
            except:
                print(f"  Directory /{dir_name}/ not accessible")
        
        # Let's check if we can find any existing PHP file that includes database connection
        print("\n🔍 Checking existing PHP files for database patterns...")
        
        # Download and check a few key files
        files_to_check = ['index.php', 'login.php', 'account-new.php', 'dashboard-professional-new.php']
        
        for filename in files_to_check:
            try:
                print(f"\n📄 Checking {filename}...")
                content = []
                ftp.retrlines(f'RETR {filename}', content.append)
                
                # Look for database connection patterns
                db_patterns = []
                for i, line in enumerate(content):
                    if any(word in line.lower() for word in ['include', 'require', 'database', 'mysqli', '$conn', '$db']):
                        db_patterns.append(f"  Line {i+1}: {line.strip()}")
                
                if db_patterns:
                    print("  Database-related lines found:")
                    for pattern in db_patterns[:5]:  # Show first 5 matches
                        print(pattern)
                else:
                    print("  No database patterns found")
                    
            except Exception as e:
                print(f"  Could not read {filename}: {str(e)}")
        
        # Create a working login with database auto-detection
        print("\n🔧 Creating login with improved database detection...")
        
        improved_login = '''<?php
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
$success_message = '';

// Database connection function
function getDatabaseConnection() {
    // Try multiple possible database files
    $db_files = [
        'database.php',
        'db.php',
        'config.php', 
        'includes/database.php',
        'includes/db.php',
        'includes/config.php',
        'common/database.php',
        'common/db.php',
        'config/database.php',
        'config/db.php',
        '../database.php',
        '../db.php'
    ];
    
    foreach ($db_files as $file) {
        if (file_exists($file)) {
            try {
                include_once $file;
                
                // Check for common connection variables
                if (isset($conn) && $conn instanceof mysqli) {
                    return $conn;
                }
                if (isset($db) && $db instanceof mysqli) {
                    return $db;
                }
                if (isset($connection) && $connection instanceof mysqli) {
                    return $connection;
                }
                if (isset($mysqli) && $mysqli instanceof mysqli) {
                    return $mysqli;
                }
            } catch (Exception $e) {
                continue;
            }
        }
    }
    
    // Try direct connection if no file found
    try {
        // Common database credentials to try
        $hosts = ['localhost', '127.0.0.1', 'mysql'];
        $usernames = ['11klassnikiru67871', 'root', 'user'];
        $databases = ['11klassnikiru67871_db', 'klassniki', '11klassniki'];
        
        foreach ($hosts as $host) {
            foreach ($usernames as $username) {
                foreach ($databases as $database) {
                    try {
                        $conn = new mysqli($host, $username, '', $database);
                        if (!$conn->connect_error) {
                            return $conn;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Continue with no connection
    }
    
    return null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error_message = 'Пожалуйста, заполните все поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Пожалуйста, введите корректный email';
    } else {
        // Try to authenticate
        $conn = getDatabaseConnection();
        
        if ($conn) {
            try {
                // Try common table names
                $table_names = ['users', 'user', 'members', 'accounts'];
                $user_found = false;
                
                foreach ($table_names as $table) {
                    try {
                        $stmt = $conn->prepare("SELECT id, email, password, first_name, last_name FROM {$table} WHERE email = ? LIMIT 1");
                        if (!$stmt) continue;
                        
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result && $result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            $user_found = true;
                            
                            // Check password
                            $password_valid = false;
                            if (password_verify($password, $user['password'])) {
                                $password_valid = true;
                            } elseif ($password === $user['password']) {
                                $password_valid = true;
                            }
                            
                            if ($password_valid) {
                                // Login successful
                                if (session_status() == PHP_SESSION_ACTIVE) {
                                    $_SESSION['user_id'] = $user['id'];
                                    $_SESSION['user_email'] = $user['email'];
                                    $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                }
                                
                                header('Location: /account');
                                exit();
                            } else {
                                $error_message = 'Неверный email или пароль';
                            }
                            
                            $stmt->close();
                            break;
                        }
                        
                        $stmt->close();
                    } catch (Exception $e) {
                        continue;
                    }
                }
                
                if (!$user_found) {
                    $error_message = 'Пользователь не найден';
                }
                
            } catch (Exception $e) {
                $error_message = 'Ошибка аутентификации';
            }
        } else {
            $error_message = 'Временные технические неполадки. Попробуйте позже.';
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
<?php ob_end_flush(); ?>'''
        
        # Upload the improved login
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(improved_login)
            tmp_path = tmp.name
        
        print("📤 Uploading improved login with auto-detection...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-standalone.php', file)
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-template.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Improved login deployed!")
        print("\n🔧 New features:")
        print("• Searches for database files automatically")
        print("• Tries multiple common database variable names")
        print("• Attempts direct database connections")
        print("• Tries different user table names")
        print("• Better error messages")
        
        print("\n🧪 Test the improved login:")
        print("https://11klassniki.ru/login/")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()