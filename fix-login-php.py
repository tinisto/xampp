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
    print("🔧 Fixing login.php form action...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download login.php
        print("📥 Downloading login.php...")
        login_content = []
        ftp.retrlines('RETR login.php', login_content.append)
        
        # Fix the form action
        print("🔧 Fixing form action...")
        fixed_content = []
        changes_made = 0
        
        for line in login_content:
            if 'action="/login-process.php"' in line:
                fixed_line = line.replace('action="/login-process.php"', 'action="/pages/login/login_process_simple.php"')
                fixed_content.append(fixed_line)
                print(f"  ✅ Fixed: {line.strip()}")
                print(f"  ✅ To: {fixed_line.strip()}")
                changes_made += 1
            else:
                fixed_content.append(line)
        
        if changes_made > 0:
            # Save to temporary file
            with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                tmp.write('\n'.join(fixed_content))
                tmp_path = tmp.name
            
            # Upload fixed file
            print("📤 Uploading fixed login.php...")
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /login.php', file)
            
            os.unlink(tmp_path)
            
            # Verify the fix
            print("🔍 Verifying fix...")
            verify_content = []
            ftp.retrlines('RETR login.php', verify_content.append)
            
            verified = False
            for line in verify_content:
                if 'action=' in line and 'form' in line.lower():
                    if '/pages/login/login_process_simple.php' in line:
                        print("  ✅ VERIFIED: Form now points to correct endpoint!")
                        verified = True
                    elif 'login-process.php' in line:
                        print("  ❌ ERROR: Form still has wrong endpoint!")
                    break
            
            if verified:
                print("\n✅ Successfully fixed login.php!")
                print("🎯 The 404 error should now be resolved!")
            else:
                print("\n⚠️  Could not verify the fix")
                
        else:
            print("❌ Could not find form action to fix in login.php")
            print("The form might be in a different format or file")
        
        ftp.quit()
        
        print("\n🧪 Test the login form now:")
        print("1. Go to: https://11klassniki.ru/login")
        print("2. Try to login - it should no longer give 404 error")
        print("3. Form should submit to: /pages/login/login_process_simple.php")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")

if __name__ == "__main__":
    main()