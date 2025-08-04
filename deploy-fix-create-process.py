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
    print("🔧 Fixing Content Creation Process Handler")
    print("========================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading fixed files...")
    
    files_to_upload = {
        'create-process.php': 'create-process.php',
        'check-content-tables.php': 'check-content-tables.php'
    }
    
    success_count = 0
    for local_file, remote_file in files_to_upload.items():
        if upload_file(ftp, local_file, remote_file):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Fix deployed successfully!")
        print("\n🎉 What's fixed:")
        print("   - ✅ Better error handling for SQL prepare")
        print("   - ✅ Checks if tables exist before inserting")
        print("   - ✅ Simplified insert queries")
        print("   - ✅ Shows meaningful error messages")
        
        print("\n🔍 Debug tool available:")
        print("   https://11klassniki.ru/check-content-tables.php")
        print("   (Shows actual table structure)")
        
        print("\n📍 Try creating content again:")
        print("   - https://11klassniki.ru/create/news")
        print("   - https://11klassniki.ru/create/post")
        
    else:
        print(f"\n⚠️  Some files failed to upload")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")