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
    print("🚨 Fixing Comments 500 Error")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload the completely rewritten process_comments.php
    print("\n📤 Uploading completely rewritten process_comments.php...")
    
    if upload_file(ftp, 'comments/process_comments.php', 'comments/process_comments.php'):
        print("\n✅ 500 Error fix deployed!")
        
        print("\n🔧 What's Fixed:")
        print("   - ✅ Added proper session management")
        print("   - ✅ Added comprehensive error handling")
        print("   - ✅ Removed dependency on missing includes")
        print("   - ✅ Added input validation and sanitization")
        print("   - ✅ Added user authentication checks")
        print("   - ✅ Graceful fallbacks for missing functions")
        print("   - ✅ Proper database error handling")
        print("   - ✅ Safe redirects with error messages")
        
        print("\n🛡️ Security Improvements:")
        print("   - Login verification before comment submission")
        print("   - Entity type validation")
        print("   - Input sanitization and length limits")
        print("   - SQL injection prevention")
        print("   - Error logging for debugging")
        
        print("\n🔄 User Experience:")
        print("   - Redirects back to original page")
        print("   - Success/error message parameters")
        print("   - Handles empty comments gracefully")
        print("   - Login redirect for non-authenticated users")
        
        print("\n🌐 Test Comment Submission:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - Try submitting a comment - should work without 500 error!")
        
    else:
        print("\n❌ Upload failed")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")