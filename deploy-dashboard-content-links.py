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
    print("🔧 Adding Content Creation Links to Dashboard")
    print("==========================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading updated dashboard...")
    
    success = upload_file(ftp, 'dashboard-professional.php', 'dashboard-professional.php')
    
    ftp.quit()
    
    if success:
        print("\n✅ Dashboard updated successfully!")
        print("\n🎉 Added content creation links:")
        print("   - ✅ Sidebar: 'Создать новость' & 'Создать пост'")
        print("   - ✅ User dropdown menu: Quick access links")
        print("   - ✅ Quick actions: Two prominent buttons")
        
        print("\n📍 Where to find them:")
        print("   1. Left sidebar → 'Контент' section")
        print("   2. User menu (top right) → Dropdown")
        print("   3. Quick actions → 'Управление контентом' card")
        
        print("\n🔗 Direct links:")
        print("   Create News: https://11klassniki.ru/create/news")
        print("   Create Post: https://11klassniki.ru/create/post")
        
    else:
        print("\n❌ Failed to deploy")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")