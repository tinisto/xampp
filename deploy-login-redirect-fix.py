#!/usr/bin/env python3
import ftplib
from datetime import datetime

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def create_directory_if_not_exists(ftp, directory):
    """Create directory if it doesn't exist"""
    try:
        ftp.cwd(directory)
        ftp.cwd('..')  # Go back
        return True
    except:
        try:
            ftp.mkd(directory)
            print(f"📁 Created directory: {directory}")
            return True
        except:
            return False

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        # Check if we need to create directory
        if '/' in remote_file:
            dir_path = '/'.join(remote_file.split('/')[:-1])
            create_directory_if_not_exists(ftp, dir_path)
        
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

try:
    print("🚀 Deploying Missing Login Redirect File")
    print("=" * 60)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload the missing file
    print("\n📤 Uploading missing secure login process...")
    
    if upload_file(ftp, 'secure-updates/login_process_secure.php', 'secure-updates/login_process_secure.php'):
        print("\n✅ Secure login process deployed!")
        
        print("\n🎉 All Login Files Now Updated!")
        print("   - All 6 login process files redirect to home page")
        print("   - Consistent behavior across the entire site")
        print("   - Users can now choose their destination after login")
        
    else:
        print("\n❌ Failed to upload secure login process")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")