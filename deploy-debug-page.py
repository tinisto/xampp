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
    print("🔍 Deploying Debug Page")
    print("=" * 50)
    
    # Connect to FTP
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    if upload_file(ftp, 'debug-news.php', 'debug-news.php'):
        print("\n✅ Debug page deployed!")
        print("\n🌐 Test at: https://11klassniki.ru/debug-news.php?url_news=dsadas")
        print("This will show all the data from the news table")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")