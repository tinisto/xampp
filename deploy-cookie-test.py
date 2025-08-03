#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

try:
    print("🧪 Deploying Cookie Test Page")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    # Upload test file
    with open('test-cookies.php', 'rb') as f:
        ftp.storbinary('STOR test-cookies.php', f)
        print('✅ Uploaded test-cookies.php')
    
    ftp.quit()
    print("\n🔗 Test the cookie banner at:")
    print("https://11klassniki.ru/test-cookies.php")
    print("\n📋 This will show:")
    print("- Current cookie status")
    print("- Cookie consent banner (if working)")
    print("- Browser console debugging info")
    
except Exception as e:
    print(f"❌ Error: {e}")