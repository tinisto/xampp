#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

filename = 'test-news-page-direct.php'

try:
    print(f"Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print("✓ Connected")
    
    ftp.cwd(FTP_ROOT)
    
    local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{filename}"
    with open(local_path, 'rb') as f:
        ftp.storbinary(f'STOR {filename}', f)
    print(f"✓ Uploaded: {filename}")
    
    ftp.quit()
    print(f"\nTest URL: https://11klassniki.ru/{filename}")
    
except Exception as e:
    print(f"✗ Error: {e}")