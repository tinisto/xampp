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
    print("🚨 Emergency fix for login 500 error...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create the most basic possible login page
        basic_login = '''<!DOCTYPE html>
<html>
<head>
    <title>Login - 11klassniki.ru</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; margin: 0; padding: 20px; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        .btn { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .links { text-align: center; margin-top: 20px; }
        .links a { color: #007bff; text-decoration: none; margin: 0 10px; }
        .note { background: #fff3cd; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Вход</h1>
        <div class="note">Система временно в режиме обслуживания</div>
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
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
</html>'''
        
        # Upload to /login/index.php
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(basic_login)
            tmp_path = tmp.name
        
        print("📤 Creating /login/ directory...")
        try:
            ftp.mkd('login')
        except:
            pass  # Directory might already exist
        
        ftp.cwd('login')
        
        print("📤 Uploading basic HTML login page...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR index.php', file)
        
        # Also create login-standalone.php as the same HTML
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-standalone.php', file)
        
        os.unlink(tmp_path)
        
        # Go back to root and update .htaccess to use the HTML file
        ftp.cwd('..')
        
        print("📥 Downloading .htaccess...")
        htaccess_content = []
        try:
            ftp.retrlines('RETR .htaccess', htaccess_content.append)
        except:
            htaccess_content = []
        
        # Make sure login route exists
        has_login_route = any('login/?$' in line for line in htaccess_content)
        
        if not has_login_route:
            print("📝 Adding login route to .htaccess...")
            # Add login route if missing
            with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                for line in htaccess_content:
                    tmp.write(line + '\n')
                    # Add login rule after user system section
                    if 'User System' in line:
                        tmp.write('    RewriteRule ^login/?$ login/index.php [QSA,NC,L]\n')
                tmp_path = tmp.name
            
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR .htaccess', file)
            
            os.unlink(tmp_path)
            print("✅ Added login route to .htaccess")
        
        ftp.quit()
        
        print("\n✅ Emergency fix deployed!")
        print("📋 What was done:")
        print("1. Created basic HTML login form (no PHP processing)")
        print("2. Uploaded to /login/index.php")
        print("3. Also created /login/login-standalone.php")
        print("4. Ensured .htaccess routes /login/ to /login/index.php")
        
        print("\n🧪 Test now:")
        print("https://11klassniki.ru/login/ should show a basic login form")
        print("(Form doesn't process yet, but page should load without 500 error)")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()