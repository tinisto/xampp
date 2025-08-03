#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading final fixed post page...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Upload the fixed version
    with open('pages/post/post-fixed-no-construction.php', 'rb') as f:
        ftp.storbinary('STOR post.php', f)
    print("✓ Uploaded fixed post.php (no construction check)")
    
    ftp.quit()
    print("\nPost pages should now work properly!")
    print("The issue was the check_under_construction.php trying to load missing config files.")
    
except Exception as e:
    print(f"Error: {e}")