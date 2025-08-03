#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading clean production version...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Upload clean version
    ftp.cwd('/11klassnikiru/pages/login')
    with open('login_process_clean.php', 'rb') as f:
        ftp.storbinary('STOR login_process_simple.php', f)
    
    print("✓ Uploaded clean production version")
    
    ftp.quit()
    print("\n✅ COMPLETE!")
    print("Login redirect functionality is now live and ready for production!")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()