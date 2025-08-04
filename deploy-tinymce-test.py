#!/usr/bin/env python3
import ftplib
from datetime import datetime

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

try:
    print("🔍 Deploying TinyMCE Display Test")
    print("=" * 50)
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    if upload_file(ftp, 'fix-tinymce-display.php', 'fix-tinymce-display.php'):
        print("\n✅ Test script deployed!")
        print("\n🌐 Visit: https://11klassniki.ru/fix-tinymce-display.php")
        print("\nThis will show:")
        print("- Raw database content")
        print("- Encoding detection")
        print("- Three different TinyMCE loading methods")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")