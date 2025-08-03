#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Downloading current news.php from server...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Change to the correct directory
    ftp.cwd('/11klassnikiru/pages/common/news')
    
    # Download the current file
    with open('news_from_server.php', 'wb') as f:
        ftp.retrbinary('RETR news.php', f.write)
    
    print("✓ Downloaded current news.php")
    
    # Check lines 25-50
    with open('news_from_server.php', 'r') as f:
        lines = f.readlines()
        print("\nLines 25-50 of current server file:")
        for i in range(24, min(50, len(lines))):
            if 'categoryFilter' in lines[i] or 'case' in lines[i]:
                print(f"{i+1}: {lines[i].rstrip()}")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")