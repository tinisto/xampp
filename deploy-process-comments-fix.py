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
    print("🔧 Fixing Comments Database Error")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload the fixed process_comments.php
    print("\n📤 Uploading fixed process_comments.php...")
    
    if upload_file(ftp, 'comments/process_comments.php', 'comments/process_comments.php'):
        print("\n✅ Database error fix deployed!")
        
        print("\n🔧 What's Fixed:")
        print("   - Changed users.avatar → users.avatar_url")
        print("   - Updated database column references")
        print("   - Fixed comment submission process")
        
        print("\n🌐 Test Comment Submission:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - https://11klassniki.ru/post/prinosit-dobro-lyudyam")
        print("   - Try submitting a comment - should work now!")
        
    else:
        print("\n❌ Upload failed")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")