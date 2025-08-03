#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading test_post_route.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to web root
    ftp.cwd('/11klassnikiru')
    
    # Upload the file
    with open('test_post_route.php', 'rb') as f:
        ftp.storbinary('STOR test_post_route.php', f)
    print("✓ Uploaded test_post_route.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/test_post_route.php")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()