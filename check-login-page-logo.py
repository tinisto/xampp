#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 CHECKING LOGIN PAGE FOR DIFFERENT LOGO")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check what file handles /login route
        print("\n1. Checking .htaccess routing for /login...")
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        for line in htaccess_content:
            if 'login' in line.lower() and 'RewriteRule' in line:
                print(f"   Found: {line.strip()}")
        
        # Check what files exist
        print("\n2. Checking login-related files...")
        login_files = []
        
        files_to_check = [
            'login-template.php',
            'login-standalone.php',
            'login.php',
            'pages/login/login.php'
        ]
        
        for file_path in files_to_check:
            try:
                ftp.nlst(file_path)
                login_files.append(file_path)
                print(f"   ✅ Found: {file_path}")
                
                # Check first few lines of the file
                try:
                    content = []
                    ftp.retrlines(f'RETR {file_path}', lambda x: content.append(x) if len(content) < 20 else None)
                    
                    # Look for favicon/logo references
                    for line in content:
                        if 'favicon' in line.lower() or 'logo' in line.lower() or 'icon' in line.lower():
                            print(f"      → Logo reference: {line.strip()}")
                except:
                    pass
                    
            except:
                print(f"   ❌ Not found: {file_path}")
        
        print(f"\n📊 Login files found: {len(login_files)}")
        
        if 'login-standalone.php' in login_files:
            print("\n⚠️  PROBLEM: login-standalone.php exists!")
            print("This is a self-contained page with its own HTML and logo")
            print("It does NOT use the template system")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()