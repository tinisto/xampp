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
    print("🔧 Fixing Content Creation with Correct Column Names")
    print("==================================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading fixed files...")
    
    files_to_upload = {
        'create-process.php': 'create-process.php',
        'dashboard-create-content-unified.php': 'dashboard-create-content-unified.php'
    }
    
    success_count = 0
    for local_file, remote_file in files_to_upload.items():
        if upload_file(ftp, local_file, remote_file):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Column names fix deployed!")
        print("\n🎉 What's fixed:")
        print("   - ✅ News: Uses title_news, text_news, etc.")
        print("   - ✅ Posts: Uses title_post, text_post, etc.")
        print("   - ✅ Handles draft/published status")
        print("   - ✅ Shows success/error messages")
        print("   - ✅ Generates URL slugs automatically")
        
        print("\n📍 Database columns mapped:")
        print("   News: title → title_news, content → text_news")
        print("   Posts: title → title_post, content → text_post")
        
        print("\n🚀 Try creating content now:")
        print("   - https://11klassniki.ru/create/news")
        print("   - https://11klassniki.ru/create/post")
        
    else:
        print(f"\n⚠️  Some files failed to upload")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")