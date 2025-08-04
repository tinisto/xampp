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
    print("🚀 Deploying Login Redirect Updates")
    print("=" * 60)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Files to upload
    files_to_upload = [
        {
            'local': 'pages/login/login_process_enhanced.php',
            'remote': 'pages/login/login_process_enhanced.php',
            'description': 'Enhanced login process'
        },
        {
            'local': 'pages/login/login_process_simple.php',
            'remote': 'pages/login/login_process_simple.php',
            'description': 'Simple login process'
        },
        {
            'local': 'pages/login/login_process.php',
            'remote': 'pages/login/login_process.php',
            'description': 'Main login process'
        },
        {
            'local': 'login_process_clean.php',
            'remote': 'login_process_clean.php',
            'description': 'Clean login process'
        },
        {
            'local': 'login_process_debug.php',
            'remote': 'login_process_debug.php',
            'description': 'Debug login process'
        },
        {
            'local': 'secure-updates/login_process_secure.php',
            'remote': 'secure-updates/login_process_secure.php',
            'description': 'Secure login process'
        }
    ]
    
    print("\n📤 Uploading login redirect updates...")
    success_count = 0
    
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print(f"\n✅ All {success_count} files deployed successfully!")
        
        print("\n🏠 What's Changed:")
        print("   - All users now redirect to home page (/) after login")
        print("   - Removed role-based redirects (admin→dashboard, user→account)")
        print("   - Consistent behavior across all login processes")
        print("   - Better user experience - users choose where to go")
        
        print("\n🔄 Updated Files:")
        print("   - login_process_enhanced.php")
        print("   - login_process_simple.php") 
        print("   - login_process.php")
        print("   - login_process_clean.php")
        print("   - login_process_debug.php")
        print("   - login_process_secure.php")
        
        print("\n🌐 Test the Changes:")
        print("   1. Go to https://11klassniki.ru/login")
        print("   2. Login with any account (admin or regular user)")
        print("   3. You should be redirected to the home page")
        print("   4. From there, users can navigate to their preferred section")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")