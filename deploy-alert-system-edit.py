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
    print("🎨 Deploying Modern Alert System to Edit Content Page")
    print("=" * 60)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload file
    print("\n📤 Uploading modern alert system...")
    
    if upload_file(ftp, 'dashboard-edit-content.php', 'dashboard-edit-content.php'):
        print("\n✅ Modern alert system deployed to edit content page!")
        
        print("\n🎨 What's New:")
        print("   - ✅ Replaced browser alerts with modern alert system")
        print("   - ✅ Professional slide-in/out animations")
        print("   - ✅ Color-coded alerts (success/error/warning)")
        print("   - ✅ Auto-dismiss after 5 seconds")
        print("   - ✅ Manual close button")
        print("   - ✅ Consistent with create content page")
        
        print("\n🔄 Alert Types:")
        print("   - ✅ Success alerts (green)")
        print("   - ❌ Error alerts (red)")
        print("   - ⚠️ Warning alerts (yellow)")
        print("   - Positioned in top-right corner")
        print("   - Responsive and accessible")
        
        print("\n🌐 Test the Alerts:")
        print("   - Edit any news/post content")
        print("   - Try submitting with empty fields")
        print("   - Upload invalid images")
        print("   - Success/error messages now use modern alerts")
        
    else:
        print("\n❌ Upload failed")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")