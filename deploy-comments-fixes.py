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
    print("💬 Deploying Comments System Fixes")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading fixed files...")
    
    files_to_upload = [
        {
            'local': 'pages/post/post-content-professional.php',
            'remote': 'pages/post/post-content-professional.php',
            'description': 'Added comments integration to professional post template'
        },
        {
            'local': 'comments/load_comments.php',
            'remote': 'comments/load_comments.php',
            'description': 'Updated XSS protection for main comments'
        },
        {
            'local': 'comments/load_child_comments.php',
            'remote': 'comments/load_child_comments.php',
            'description': 'Updated XSS protection for reply comments'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Comments system fixes deployed successfully!")
        print("\n🌟 What's Fixed:")
        print("   - ✅ Comments now appear on all post pages")
        print("   - ✅ Enhanced XSS protection using strip_tags()")
        print("   - ✅ Consistent security with posts/news content")
        print("   - ✅ Modern styling integrated with post design")
        
        print("\n🔧 Fixes Applied:")
        print("   - Added comments section to post-content-professional.php")
        print("   - Updated load_comments.php with strip_tags() protection")
        print("   - Updated load_child_comments.php with strip_tags() protection")
        print("   - Maintained safe HTML tags: <p>, <br>, <strong>, <em>, etc.")
        
        print("\n🌐 Test Comments On:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - https://11klassniki.ru/post/prinosit-dobro-lyudyam")
        print("   - Any other post page")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")