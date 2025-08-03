#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def create_remote_directory(ftp, path):
    """Create directory structure on remote server"""
    dirs = path.split('/')
    for i in range(1, len(dirs)):
        dir_path = '/'.join(dirs[:i])
        if dir_path:
            try:
                ftp.mkd(dir_path)
                print(f"📁 Created directory: {dir_path}")
            except ftplib.error_perm:
                pass  # Directory might already exist

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        # Create remote directory if needed
        create_remote_directory(ftp, remote_file)
        
        # Upload the file
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

# Files to deploy
FILES_TO_DEPLOY = {
    'pages/privacy/privacy.php': 'pages/privacy/privacy.php',
    'pages/terms/terms.php': 'pages/terms/terms.php',
    '.htaccess': '.htaccess'
}

try:
    print("🚀 Deploying Legal Pages (Privacy & Terms)")
    print("==========================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    success_count = 0
    total_files = len(FILES_TO_DEPLOY)
    
    for local_file, remote_file in FILES_TO_DEPLOY.items():
        if os.path.exists(local_file):
            if upload_file(ftp, local_file, remote_file):
                success_count += 1
        else:
            print(f"⚠️  Local file not found: {local_file}")
    
    ftp.quit()
    
    print(f"\n📊 Deployment Summary:")
    print(f"   - Total files: {total_files}")
    print(f"   - Successfully uploaded: {success_count}")
    print(f"   - Failed: {total_files - success_count}")
    
    if success_count == total_files:
        print("\n✅ All legal pages deployed successfully!")
        print("\n📋 Legal pages now available:")
        print("   - Privacy Policy: https://11klassniki.ru/privacy")
        print("   - Terms of Service: https://11klassniki.ru/terms")
        print("\n🇷🇺 Both pages are compliant with Russian laws:")
        print("   - Federal Law 152-FZ (Personal Data)")
        print("   - Federal Law 38-FZ (Advertising)")
        print("   - Federal Law 273-FZ (Education)")
        print("   - Russian Civil Code")
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")
    
print("\n🔗 Don't forget to add links to these pages in your footer!")