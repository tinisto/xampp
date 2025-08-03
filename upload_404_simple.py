#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading simple 404 page...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to 404 directory
    ftp.cwd('/11klassnikiru/pages/404')
    
    # Backup current 404.php
    try:
        ftp.rename('404.php', '404-with-template.php')
        print("✓ Backed up current 404.php")
    except:
        print("Backup already exists or failed")
    
    # Upload simple 404 page as main
    with open('pages/404/404-simple.php', 'rb') as f:
        ftp.storbinary('STOR 404.php', f)
    print("✓ Uploaded simple 404.php")
    
    ftp.quit()
    print("\n✅ 404 page updated!")
    print("- No header/footer")
    print("- Clean design with 'Ой-ой!' message")
    print("- Icon links to main page")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()