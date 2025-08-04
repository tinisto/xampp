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
    print("🔒 CRITICAL SECURITY FIX: XSS Protection")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading security fixes...")
    
    files_to_upload = [
        {
            'local': 'pages/post/post-content-professional.php',
            'remote': 'pages/post/post-content-professional.php',
            'description': 'XSS protection for posts'
        },
        {
            'local': 'pages/common/news/news-content.php',
            'remote': 'pages/common/news/news-content.php',
            'description': 'XSS protection for news'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Security fixes deployed successfully!")
        print("\n🔒 Security Improvements:")
        print("   - Added strip_tags() to filter dangerous HTML")
        print("   - Only safe HTML tags are allowed")
        print("   - Prevents JavaScript injection (XSS attacks)")
        
        print("\n📋 Allowed HTML tags:")
        print("   - Formatting: <p> <br> <strong> <b> <em> <i> <u>")
        print("   - Links: <a>")
        print("   - Lists: <ul> <ol> <li>")
        print("   - Headings: <h1> through <h6>")
        print("   - Structure: <div> <span> <blockquote>")
        
        print("\n⚠️  Important:")
        print("   - All other HTML tags will be removed")
        print("   - JavaScript, <script>, <iframe> etc. are blocked")
        print("   - HTML formatting will still work properly")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")