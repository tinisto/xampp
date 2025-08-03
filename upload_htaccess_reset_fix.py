#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading .htaccess with reset-password-confirm-process route...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    with open('.htaccess', 'rb') as f:
        ftp.storbinary('STOR .htaccess', f)
    print("✓ Uploaded updated .htaccess")
    
    ftp.quit()
    print("\n✅ Route added successfully!")
    print("URL /reset-password-confirm-process should now work")
    print("Test: https://11klassniki.ru/reset-password-confirm-process")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()