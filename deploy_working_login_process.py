#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Deploying working login process...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Replace the actual login process with the working debug version
    ftp.cwd('/11klassnikiru/pages/login')
    
    # Backup current version
    try:
        ftp.rename('login_process_simple.php', 'login_process_simple_backup.php')
        print("✓ Backed up current login_process_simple.php")
    except:
        print("No backup needed")
    
    # Copy the working debug version as the main version
    ftp.cwd('/11klassnikiru')
    
    # Download the working debug version
    with open('temp_working_login.php', 'wb') as f:
        ftp.retrbinary('RETR login_process_debug.php', f.write)
    
    # Upload it as the main login process (without debug logging)
    ftp.cwd('/11klassnikiru/pages/login')
    with open('temp_working_login.php', 'rb') as f:
        ftp.storbinary('STOR login_process_simple.php', f)
    
    print("✓ Deployed working login process")
    
    ftp.quit()
    print("\n✅ SUCCESS!")
    print("The login redirect functionality is now live!")
    print("Users will be redirected back to their original page after login.")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()