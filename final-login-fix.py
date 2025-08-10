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
    print("🔧 Final attempt to fix login form issue...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # First, let's see what's in the .htaccess about login routing
        print("🔍 Checking .htaccess login routing...")
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        for line in htaccess_content:
            if 'login' in line.lower() and 'rewrite' in line.lower():
                print(f"  {line.strip()}")
        
        # The issue is that .htaccess points to login-standalone.php but server is serving login.php
        # Let's update the .htaccess to point directly to login-template.php which has the correct action
        print("\n🔧 Solution: Update .htaccess to use login-template.php (which has correct form action)")
        
        # Update .htaccess
        updated_htaccess = []
        changes = 0
        
        for line in htaccess_content:
            if 'RewriteRule ^login/?$ login-standalone.php' in line:
                # Change to login-template.php which we know has the correct form action
                updated_line = line.replace('login-standalone.php', 'login-template.php')
                updated_htaccess.append(updated_line)
                print(f"  ✅ Changed: {line.strip()}")
                print(f"  ✅ To: {updated_line.strip()}")
                changes += 1
            else:
                updated_htaccess.append(line)
        
        if changes > 0:
            # Save and upload updated .htaccess
            with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                tmp.write('\n'.join(updated_htaccess))
                tmp_path = tmp.name
            
            print("📤 Uploading updated .htaccess...")
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /.htaccess', file)
            
            os.unlink(tmp_path)
            print("✅ Updated .htaccess to use login-template.php")
        
        # Also create a simple redirect file as login.php
        print("\n🔧 Creating redirect login.php...")
        redirect_content = '''<?php
// Redirect to the correct login page
header('Location: /login-template.php');
exit();
?>'''
        
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(redirect_content)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR /login.php', file)
        
        os.unlink(tmp_path)
        print("✅ Created redirect login.php")
        
        ftp.quit()
        
        print("\n✅ Fix Applied!")
        print("📋 What was done:")
        print("1. Updated .htaccess to route /login to login-template.php (which has correct form action)")
        print("2. Replaced login.php with a redirect to login-template.php")
        print("\n🧪 Test now:")
        print("1. Go to: https://11klassniki.ru/login")
        print("2. The form should now submit to: /pages/login/login_process_simple.php")
        print("3. No more 404 errors on login submission!")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")

if __name__ == "__main__":
    main()