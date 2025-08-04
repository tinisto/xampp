#!/usr/bin/env python3
import ftplib

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
        print(f"❌ Failed: {str(e)}")
        return False

try:
    print("🔧 Deploying Sidebar Closed by Default")
    print("=====================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading updated dashboard...")
    
    success = upload_file(ftp, 'dashboard-create-content-unified.php', 'dashboard-create-content-unified.php')
    
    ftp.quit()
    
    if success:
        print("\n✅ Sidebar fix deployed!")
        print("\n🎉 Changes:")
        print("   - ✅ Sidebar starts closed")
        print("   - ✅ Toggle button always visible")
        print("   - ✅ Click ☰ to open sidebar")
        print("   - ✅ More space for content creation")
        
        print("\n🔗 Test it:")
        print("   https://11klassniki.ru/create/news")
        print("   https://11klassniki.ru/create/post")
        
    else:
        print("\n❌ Failed to deploy")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")