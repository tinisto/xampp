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
    print("🚨 Deploying Alert System & Validation Fixes")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading files...")
    
    files_to_upload = [
        {
            'local': 'dashboard-create-content-unified.php',
            'remote': 'dashboard-create-content-unified.php',
            'description': 'Create content with improved validation & alerts'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ All files deployed successfully!")
        print("\n🌟 What's New:")
        print("   - Modern alert system with animations")
        print("   - Better form validation for empty TinyMCE content")
        print("   - Specific error messages for title and content")
        print("   - Auto-dismissing alerts after 5 seconds")
        print("   - Focus on empty fields when validation fails")
        
        print("\n🎨 Alert Types:")
        print("   - Error: Red alerts for validation failures")
        print("   - Success: Green alerts for successful operations")
        print("   - Warning: Yellow alerts for warnings")
        
        print("\n📝 Test the improvements:")
        print("   1. Try submitting with empty title")
        print("   2. Try submitting with empty content")
        print("   3. Create content successfully")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")