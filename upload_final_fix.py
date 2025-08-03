#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading final fix for post.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Upload the fixed version
    with open('pages/post/post-final-fix.php', 'rb') as f:
        ftp.storbinary('STOR post.php', f)
    print("✓ Uploaded post.php with proper database connection")
    
    ftp.quit()
    print("\nPost pages should now work correctly!")
    
except Exception as e:
    print(f"Error: {e}")