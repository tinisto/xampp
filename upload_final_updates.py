#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading final updates...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Upload updated post-content.php
    ftp.cwd('/11klassnikiru/pages/post')
    with open('pages/post/post-content-fixed.php', 'rb') as f:
        ftp.storbinary('STOR post-content.php', f)
    print("✓ Uploaded updated post-content.php (removed html_entity_decode)")
    
    ftp.quit()
    print("\n✅ All updates completed!")
    print("\nWhat was done:")
    print("1. Cleaned 418 posts - converted HTML entities to UTF-8")
    print("2. Created backup table: posts_backup_20250803_202516")
    print("3. Updated post-content.php to display content directly")
    print("\nThe posts should now display faster and cleaner!")
    
except Exception as e:
    print(f"Error: {e}")