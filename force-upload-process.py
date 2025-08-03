#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

try:
    print("🔄 Force uploading corrected forgot-password-process.php")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    # Upload ONLY the process file
    with open('forgot-password-process.php', 'rb') as f:
        ftp.storbinary('STOR forgot-password-process.php', f)
        print('✅ Force uploaded forgot-password-process.php')
    
    ftp.quit()
    print('🚀 Process file updated!')
    
except Exception as e:
    print(f"Error: {e}")