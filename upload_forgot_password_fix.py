#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading fixed forgot password functionality...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    with open('forgot-password-standalone.php', 'rb') as f:
        ftp.storbinary('STOR forgot-password-standalone.php', f)
    print("✓ Uploaded fixed forgot-password-standalone.php")
    
    ftp.quit()
    print("\n✅ Security fix deployed!")
    print("- Now validates email exists in database before generating reset links")
    print("- Shows generic message for non-existent emails (security best practice)")
    print("- Prevents attackers from discovering valid email addresses")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()