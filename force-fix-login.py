#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Force-fixing login form endpoint issue...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # First, rename the old login-standalone.php
        try:
            ftp.rename('login-standalone.php', 'login-standalone-old.php')
            print("📋 Renamed old login-standalone.php to backup")
        except:
            print("ℹ️  Could not rename old file")
        
        # Upload the corrected version
        print("📤 Uploading corrected login-standalone.php...")
        
        local_file = 'login-standalone.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as file:
                ftp.storbinary('STOR /login-standalone.php', file)
            
            # Verify the upload
            size = ftp.size('login-standalone.php')
            print(f"✅ Uploaded login-standalone.php ({size} bytes)")
            
            # Double-check the content
            print("\n🔍 Verifying uploaded file content...")
            content = []
            ftp.retrlines('RETR login-standalone.php', content.append)
            
            correct_action = False
            for line in content:
                if 'action=' in line and 'form' in line.lower():
                    print(f"  Form action: {line.strip()}")
                    if '/pages/login/login_process_simple.php' in line:
                        correct_action = True
                        print("  ✅ CORRECT form action!")
                    elif 'login-process.php' in line:
                        print("  ❌ WRONG form action still present!")
                    break
            
            if correct_action:
                print("\n✅ Successfully fixed login form endpoint!")
            else:
                print("\n❌ Form action still incorrect - may need manual intervention")
                
        else:
            print("❌ Local login-standalone.php not found!")
        
        # Also check if we need to update login.php or other files
        print("\n🔍 Checking other login files...")
        
        # Check login.php
        try:
            login_content = []
            ftp.retrlines('RETR login.php', login_content.append)
            for line in login_content:
                if 'action=' in line and 'form' in line.lower():
                    print(f"  login.php form: {line.strip()}")
                    if 'login-process.php' in line:
                        print("  ⚠️  login.php also has wrong form action!")
                    break
        except:
            pass
        
        ftp.quit()
        
        print("\n🎯 Next Steps:")
        print("1. Clear browser cache and test: https://11klassniki.ru/login")
        print("2. Check diagnostic page: https://11klassniki.ru/login-diagnostic.php")
        print("3. If still not working, server may be caching or using different file")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")

if __name__ == "__main__":
    main()