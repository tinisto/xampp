#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading fixed post-content.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Upload the fixed version
    with open('pages/post/post-content.php', 'rb') as f:
        ftp.storbinary('STOR post-content.php', f)
    print("✓ Uploaded post-content.php with category fix")
    
    ftp.quit()
    print("\nPost pages should now display without warnings!")
    
except Exception as e:
    print(f"Error: {e}")