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
    print("🔍 Debugging login routing issue...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check what .htaccess currently says about login
        print("📥 Checking current .htaccess login routing...")
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        login_rules = []
        for i, line in enumerate(htaccess_content):
            if 'login' in line.lower():
                login_rules.append(f"Line {i+1}: {line}")
        
        print("🔍 Login-related rules in .htaccess:")
        for rule in login_rules:
            print(f"  {rule}")
        
        # Check what files exist
        print("\n📂 Files in root directory:")
        root_files = []
        ftp.retrlines('LIST', root_files.append)
        
        login_files = [f for f in root_files if 'login' in f.lower()]
        for f in login_files:
            print(f"  {f}")
        
        # Check /login/ directory
        print("\n📂 Files in /login/ directory:")
        try:
            ftp.cwd('login')
            login_dir_files = []
            ftp.retrlines('LIST', login_dir_files.append)
            for f in login_dir_files:
                print(f"  {f}")
            ftp.cwd('..')
        except:
            print("  /login/ directory doesn't exist or is empty")
        
        # Let's try a different approach - put the login file in the root
        print("\n🔧 Creating simple login-template.php in root...")
        
        simple_login = '''<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Login Page</h1>
    <p>This is a test login page.</p>
    <form>
        <input type="email" placeholder="Email" required><br><br>
        <input type="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>'''
        
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(simple_login)
            tmp_path = tmp.name
        
        # Upload to root as login-template.php
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-template.php', file)
        
        print("✅ Created login-template.php in root")
        
        # Also create login-standalone.php in root
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR login-standalone.php', file)
        
        print("✅ Created login-standalone.php in root")
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n📋 Debug Results:")
        print("1. Check above for current .htaccess login rules")
        print("2. Created simple HTML files in root directory")
        print("3. Based on .htaccess, /login/ should now work")
        
        print("\n🧪 Test these URLs:")
        print("https://11klassniki.ru/login/")
        print("https://11klassniki.ru/login-template.php")
        print("https://11klassniki.ru/login-standalone.php")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()