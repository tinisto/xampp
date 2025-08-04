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
    print("🔧 Fixing Entity ID Detection Issue")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading entity ID fixes...")
    
    files_to_upload = [
        {
            'local': 'comments/load_comments_simple.php',
            'remote': 'comments/load_comments_simple.php',
            'description': 'Fixed direct post ID lookup from URL'
        },
        {
            'local': 'comments/modern-comments-component.php',
            'remote': 'comments/modern-comments-component.php',
            'description': 'Fixed entity ID detection in main component'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Entity ID detection fix deployed!")
        
        print("\n🔧 What's Fixed:")
        print("   - ✅ Removed dependency on getEntityIdFromURL.php")
        print("   - ✅ Direct database lookup for post ID")
        print("   - ✅ Better error messages for debugging")
        print("   - ✅ Proper error handling for database queries")
        print("   - ✅ Fixed 'No valid post found' error")
        
        print("\n🎯 Technical Changes:")
        print("   - Direct SQL query: SELECT id_post FROM posts WHERE url_post = ?")
        print("   - Proper prepared statement handling")
        print("   - Clear error messages for troubleshooting")
        print("   - Removed external function dependencies")
        
        print("\n🌐 Test the Fix:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - Should now show comments instead of 'No valid post found'")
        print("   - Comments form should work properly")
        print("   - All 6+ comments should be visible")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")