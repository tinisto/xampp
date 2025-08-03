#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading simple error page...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to error directory
    ftp.cwd('/11klassnikiru/pages/error')
    
    # Backup current error.php
    try:
        ftp.rename('error.php', 'error-with-template.php')
        print("✓ Backed up current error.php")
    except:
        print("Backup already exists or failed")
    
    # Upload simple error page as main
    with open('pages/error/error-simple.php', 'rb') as f:
        ftp.storbinary('STOR error.php', f)
    print("✓ Uploaded simple error.php")
    
    ftp.quit()
    print("\n✅ Error page updated!")
    print("- No header/footer")
    print("- Clean design with icon")
    print("- Icon links to main page")
    
except Exception as e:
    print(f"Error: {e}")