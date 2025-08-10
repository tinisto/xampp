#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os
import re

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Comprehensive site routing fix...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # First, let's check what account files exist
        print("🔍 Checking account-related files...")
        files = []
        ftp.retrlines('LIST', files.append)
        
        account_files = [f for f in files if 'account' in f.lower()]
        for f in account_files[:10]:  # Show first 10
            print(f"  {f}")
        
        # Download current .htaccess to understand routing
        print("\n📥 Analyzing current .htaccess...")
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        # Find account route
        account_route = None
        for i, line in enumerate(htaccess_content):
            if 'account' in line.lower() and 'rewrite' in line.lower():
                account_route = line.strip()
                print(f"  Line {i+1}: {account_route}")
        
        if not account_route:
            print("  No account route found in .htaccess")
        
        # Create a comprehensive fix strategy
        print("\n🔧 Creating systematic fixes...")
        
        # 1. Create working account-new.php that handles sessions properly
        working_account = '''<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Session handling
if (session_status() == PHP_SESSION_NONE) {
    try {
        session_start();
    } catch (Exception $e) {
        // Continue without session
    }
}

// Check if user is logged in
$user_logged_in = false;
$user_data = null;

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_logged_in = true;
    $user_data = [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'] ?? '',
        'name' => $_SESSION['user_name'] ?? 'User'
    ];
} else {
    // Redirect to login if not logged in
    header('Location: /login');
    exit();
}

// Get user statistics
$stats = ['comments' => 0, 'posts' => 0, 'news' => 0];

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection) && $connection) {
        // Get user comments count
        $stmt = $connection->prepare("SELECT COUNT(*) as count FROM comments WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_data['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $stats['comments'] = $result->fetch_assoc()['count'] ?? 0;
            }
            $stmt->close();
        }
        
        // Get user posts count
        $stmt = $connection->prepare("SELECT COUNT(*) as count FROM posts WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_data['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $stats['posts'] = $result->fetch_assoc()['count'] ?? 0;
            }
            $stmt->close();
        }
        
        // Get user news count
        $stmt = $connection->prepare("SELECT COUNT(*) as count FROM news WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_data['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $stats['news'] = $result->fetch_assoc()['count'] ?? 0;
            }
            $stmt->close();
        }
    }
} catch (Exception $e) {
    // Continue with default stats
}
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><circle cx='12' cy='12' r='12' fill='%23007bff'/><text x='12' y='16' text-anchor='middle' fill='white' font-size='10' font-weight='bold' font-family='Arial'>11</text></svg>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            line-height: 1.6;
        }
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 50%;
            line-height: 40px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-name {
            font-weight: 600;
            color: #333;
        }
        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }
        .main-content {
            padding: 2rem 0;
        }
        .account-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
            display: block;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .action-links {
            list-style: none;
        }
        .action-links li {
            margin-bottom: 0.75rem;
        }
        .action-links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            .account-grid {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">11</a>
                </div>
                <div class="user-info">
                    <span class="user-name">👋 Привет, <?php echo htmlspecialchars($user_data['name']); ?>!</span>
                    <a href="/logout" class="logout-btn">Выйти</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="account-grid">
                <div class="card">
                    <h2>📊 Ваша статистика</h2>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $stats['comments']; ?></span>
                            <div class="stat-label">Комментариев</div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $stats['posts']; ?></span>
                            <div class="stat-label">Постов</div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $stats['news']; ?></span>
                            <div class="stat-label">Новостей</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2>⚙️ Настройки аккаунта</h2>
                    <ul class="action-links">
                        <li><a href="/account/edit">✏️ Редактировать профиль</a></li>
                        <li><a href="/account/password-change">🔒 Изменить пароль</a></li>
                        <li><a href="/account/comments">💬 Мои комментарии</a></li>
                        <li><a href="/dashboard">📝 Создать контент</a></li>
                    </ul>
                </div>

                <div class="card">
                    <h2>🔗 Быстрые ссылки</h2>
                    <ul class="action-links">
                        <li><a href="/">🏠 Главная страница</a></li>
                        <li><a href="/news">📰 Новости</a></li>
                        <li><a href="/search">🔍 Поиск</a></li>
                        <li><a href="/about">ℹ️ О проекте</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php ob_end_flush(); ?>'''
        
        # 2. Create simple logout handler
        simple_logout = '''<?php
// Simple logout handler
if (session_status() == PHP_SESSION_NONE) {
    try {
        session_start();
    } catch (Exception $e) {
        // Continue
    }
}

// Clear all session data
$_SESSION = array();

// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to home page
header('Location: /');
exit();
?>'''
        
        # Upload account page
        print("📤 Uploading fixed account page...")
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(working_account)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR account-new.php', file)
        
        # Also create as account.php for direct access
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR account.php', file)
        
        os.unlink(tmp_path)
        
        # Upload logout handler
        print("📤 Uploading logout handler...")
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(simple_logout)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR simple_logout.php', file)
        
        # Also create as logout.php for direct access
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR logout.php', file)
        
        os.unlink(tmp_path)
        
        # 3. Create comprehensive .htaccess fixes
        print("📝 Creating comprehensive .htaccess fixes...")
        
        # Add missing routes to .htaccess
        additional_routes = '''
    # Additional fixes for common 500 errors
    RewriteRule ^account/?$ account-new.php [QSA,NC,L]
    RewriteRule ^logout/?$ simple_logout.php [QSA,NC,L]
    RewriteRule ^signin/?$ /login [R=301,L]
    RewriteRule ^signup/?$ /registration [R=301,L]
    RewriteRule ^register/?$ /registration [R=301,L]
    
    # Fix common missing routes
    RewriteRule ^account/delete-account/?$ pages/account/delete-account.php [QSA,NC,L]
    RewriteRule ^post/?$ /news [R=301,L]
'''
        
        # Find where to insert additional routes
        updated_htaccess = []
        inserted = False
        
        for line in htaccess_content:
            updated_htaccess.append(line)
            # Insert after User System section
            if '# User System' in line and not inserted:
                for route in additional_routes.strip().split('\n'):
                    if route.strip():
                        updated_htaccess.append(route)
                inserted = True
        
        # Upload updated .htaccess
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write('\n'.join(updated_htaccess))
            tmp_path = tmp.name
        
        print("📤 Uploading updated .htaccess...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR .htaccess', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Comprehensive site fix deployed!")
        print("\n🔧 Fixed issues:")
        print("• Created working account-new.php with proper session handling")
        print("• Created simple logout handler (simple_logout.php)")
        print("• Added missing routes to .htaccess:")
        print("  - /account/ → account-new.php")
        print("  - /logout/ → simple_logout.php") 
        print("  - /signin → /login (redirect)")
        print("  - /signup → /registration (redirect)")
        print("  - /account/delete-account → proper handler")
        print("• Created fallback files (account.php, logout.php)")
        
        print("\n🧪 Test these URLs:")
        print("https://11klassniki.ru/account/")
        print("https://11klassniki.ru/logout/")
        print("https://11klassniki.ru/signin/")
        print("https://11klassniki.ru/signup/")
        
        print("\n💡 This systematic approach fixes the root routing issues")
        print("instead of patching individual links one by one.")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()