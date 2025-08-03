#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading login redirect functionality...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Upload write page with redirect parameter
    ftp.cwd('/11klassnikiru/pages/write')
    with open('pages/write/write-simple.php', 'rb') as f:
        ftp.storbinary('STOR write-simple.php', f)
    print("✓ Uploaded write-simple.php")
    
    # Upload login form with redirect handling
    ftp.cwd('/11klassnikiru')
    with open('login-modern.php', 'rb') as f:
        ftp.storbinary('STOR login-modern.php', f)
    print("✓ Uploaded login-modern.php")
    
    # Upload login process with redirect logic
    ftp.cwd('/11klassnikiru/pages/login')
    with open('pages/login/login_process_simple.php', 'rb') as f:
        ftp.storbinary('STOR login_process_simple.php', f)
    print("✓ Uploaded login_process_simple.php")
    
    ftp.quit()
    print("\n✅ Login redirect functionality deployed!")
    print("- Write page now includes redirect parameter in login link")
    print("- Login form preserves redirect parameter")
    print("- After successful login, user returns to original page")
    print("- Error redirects also preserve the redirect parameter")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()