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
    print("✨ Deploying Comments UX Improvements")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading UX improvements...")
    
    files_to_upload = [
        {
            'local': 'comments/load_comments_simple.php',
            'remote': 'comments/load_comments_simple.php',
            'description': 'Fixed timezone for correct time display'
        },
        {
            'local': 'comments/modern-comments-component.php',
            'remote': 'comments/modern-comments-component.php',
            'description': 'Added animated submit button toggle'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ UX improvements deployed successfully!")
        
        print("\n🕐 Time Display Fixes:")
        print("   - ✅ Fixed timezone to Europe/Moscow")
        print("   - ✅ Correct 'только что', 'мин назад', 'ч назад'")
        print("   - ✅ Proper UTC to local time conversion")
        print("   - ✅ Accurate time calculations")
        
        print("\n✨ Submit Button UX:")
        print("   - ✅ Hidden by default")
        print("   - ✅ Appears when user starts typing")
        print("   - ✅ Smooth fade-in animation")
        print("   - ✅ Disappears when textarea is cleared")
        print("   - ✅ Elegant slide-up effect")
        
        print("\n🎨 Animation Features:")
        print("   - Opacity transition (0 → 1)")
        print("   - Transform animation (slide up)")
        print("   - 300ms smooth transition")
        print("   - Responsive feedback")
        
        print("\n🌐 Test the Improvements:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - Check if time shows correctly (not '7 ч назад')")
        print("   - Try typing in comment box - button should appear")
        print("   - Clear the text - button should disappear")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")