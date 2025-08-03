#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading HTML entities cleaner...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('clean_html_entities.php', 'rb') as f:
        ftp.storbinary('STOR clean_html_entities.php', f)
    print("✓ Uploaded clean_html_entities.php")
    
    ftp.quit()
    print("\nTo clean HTML entities:")
    print("1. Go to: https://11klassniki.ru/clean_html_entities.php")
    print("2. Review the warning")
    print("3. Click 'Proceed with Cleaning'")
    print("\nThe script will:")
    print("- Create a full backup of the posts table")
    print("- Convert all HTML entities to UTF-8 characters")
    print("- Show progress and results")
    
except Exception as e:
    print(f"Error: {e}")