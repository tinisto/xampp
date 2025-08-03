#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading fixed post.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Upload the file
    with open('pages/post/post.php', 'rb') as f:
        ftp.storbinary('STOR post.php', f)
    print("✓ Uploaded post.php with fallback template handling")
    
    ftp.quit()
    print("\nPost links should now work properly")
    
except Exception as e:
    print(f"Error: {e}")