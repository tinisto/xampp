#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, content, filename):
    with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
        tmp.write(content)
        tmp_path = tmp.name
    
    with open(tmp_path, 'rb') as file:
        ftp.storbinary(f'STOR {filename}', file)
    os.unlink(tmp_path)

def main():
    print("🔧 CONVERTING LOGIN PAGE TO USE TEMPLATE SYSTEM")
    print("No more standalone page with different logo!")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create login page that uses template
        login_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check if already logged in
if (session_status() == PHP_SESSION_NONE) {
    try { session_start(); } catch (Exception $e) { /* Continue */ }
}

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    header('Location: /account');
    exit();
}

$page_title = 'Вход в систему';
$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($connection && !empty($email) && !empty($password)) {
        $stmt = $connection->prepare("SELECT id, username, password FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header('Location: /account');
                    exit();
                } else {
                    $error_message = 'Неверный пароль';
                }
            } else {
                $error_message = 'Пользователь не найден';
            }
            $stmt->close();
        }
    } else {
        $error_message = 'Заполните все поля';
    }
}

// Template content
$greyContent1 = '';
$greyContent2 = '';
$greyContent3 = '
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Вход в систему</h2>
                    
                    ' . (!empty($error_message) ? '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>' : '') . '
                    
                    <form method="POST" action="/login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Запомнить меня</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Войти</button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <a href="/forgot-password" class="d-block mb-2">Забыли пароль?</a>
                        <span>Нет аккаунта? <a href="/registration">Зарегистрироваться</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

// Use template system
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Delete standalone login page first
        try:
            ftp.delete('login-standalone.php')
            print("   ✅ Deleted login-standalone.php")
        except:
            print("   ⚪ Could not delete login-standalone.php")
        
        # Create new template-based login
        upload_file(ftp, login_page, 'login-template.php')
        print("   ✅ Created login-template.php (uses template system)")
        
        # Update .htaccess to use login-template.php
        print("\n   📝 Updating .htaccess routing...")
        
        # Download current .htaccess
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        # Update the login routing
        updated_htaccess = []
        for line in htaccess_content:
            if 'login/?$ login-standalone.php' in line:
                # Change to login-template.php
                updated_htaccess.append('RewriteRule ^login/?$ login-template.php [QSA,NC,L]')
                print("   ✅ Updated routing to use login-template.php")
            else:
                updated_htaccess.append(line)
        
        # Upload updated .htaccess
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write('\n'.join(updated_htaccess))
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR .htaccess', file)
        os.unlink(tmp_path)
        
        print("   ✅ Updated .htaccess")
        
        ftp.quit()
        
        print("\n✅ LOGIN PAGE NOW USES TEMPLATE SYSTEM!")
        
        print("\n🎯 What changed:")
        print("• Deleted standalone login page with different logo")
        print("• Created template-based login page")
        print("• Now shows RED header, GREEN content, YELLOW footer")
        print("• Uses same favicon.svg as all other pages")
        
        print("\n🧪 Test the login page:")
        print("• https://11klassniki.ru/login/")
        print("• Should now show same logo/header as other pages")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()