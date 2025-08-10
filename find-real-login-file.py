#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 Finding which login file has the wrong form action...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Get all PHP files
        files = []
        ftp.retrlines('LIST', files.append)
        
        login_files = []
        for file_info in files:
            filename = file_info.split()[-1] if file_info.split() else ""
            if filename.endswith('.php') and ('login' in filename.lower() or filename in ['index.php', 'real_template.php']):
                login_files.append(filename)
        
        print(f"🔍 Checking {len(login_files)} potential files...")
        
        # Check each file for the wrong form action
        for filename in login_files:
            try:
                content = []
                ftp.retrlines(f'RETR {filename}', content.append)
                
                found_form = False
                for i, line in enumerate(content):
                    if 'action=' in line and ('login-process.php' in line or 'form' in line.lower()):
                        if 'login-process.php' in line and 'action=' in line:
                            print(f"\n❌ FOUND WRONG ACTION in {filename}:")
                            print(f"  Line {i+1}: {line.strip()}")
                            # Show surrounding lines for context
                            for j in range(max(0, i-2), min(len(content), i+3)):
                                if j != i:
                                    print(f"  Line {j+1}: {content[j].strip()[:80]}...")
                            found_form = True
                            break
                        elif 'action=' in line and 'form' in line.lower() and 'login' in filename:
                            if '/pages/login/login_process_simple.php' in line:
                                print(f"✅ {filename} has CORRECT form action")
                            else:
                                print(f"ℹ️  {filename} form action: {line.strip()[:100]}...")
                            found_form = True
                            break
                
                if not found_form and 'login' in filename and filename.endswith('.php'):
                    # Check if it might have includes
                    for line in content:
                        if 'include' in line.lower() and ('login' in line or 'template' in line):
                            print(f"ℹ️  {filename} includes: {line.strip()}")
                            break
                            
            except Exception as e:
                if '550' not in str(e):  # Ignore file not found errors
                    print(f"⚠️  Could not read {filename}: {e}")
        
        # Check if the issue might be in an included file
        print("\n🔍 Checking common included files...")
        include_files = ['real_template.php', 'header.php', 'common-components/header.php']
        
        for filename in include_files:
            try:
                content = []
                ftp.retrlines(f'RETR {filename}', content.append)
                
                for i, line in enumerate(content):
                    if 'login-process.php' in line:
                        print(f"\n❌ FOUND in {filename}:")
                        print(f"  Line {i+1}: {line.strip()}")
                        
            except:
                pass
        
        ftp.quit()
        
        print("\n💡 Summary:")
        print("The wrong form action must be coming from one of the files above.")
        print("Need to update the file that contains action='/login-process.php'")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")

if __name__ == "__main__":
    main()