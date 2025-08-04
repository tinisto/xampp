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
    print("🎯 Deploying Auto-Scroll to Comments Feature")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload file
    print("\n📤 Uploading auto-scroll feature...")
    
    if upload_file(ftp, 'comments/modern-comments-component.php', 'comments/modern-comments-component.php'):
        print("\n✅ Auto-scroll feature deployed!")
        
        print("\n🎯 New Auto-Scroll Features:")
        print("   - ✅ Detects ?comment_success=1 parameter")
        print("   - ✅ Automatically scrolls to comments section")
        print("   - ✅ Smooth scrolling animation")
        print("   - ✅ Blue glow effect to highlight comments")
        print("   - ✅ Success message: 'Ваш комментарий успешно добавлен!'")
        print("   - ✅ Auto-hide success message after 5 seconds")
        print("   - ✅ Clean URL (removes success parameter)")
        
        print("\n✨ User Experience Flow:")
        print("   1. User submits comment")
        print("   2. Page reloads with ?comment_success=1")
        print("   3. Page automatically scrolls to comments")
        print("   4. Comments section glows with blue highlight")
        print("   5. Success message shows at top of comments")
        print("   6. Success message fades out after 5 seconds")
        print("   7. URL is cleaned (parameter removed)")
        print("   8. User sees their new comment immediately")
        
        print("\n🎨 Visual Effects:")
        print("   - Smooth scroll animation")
        print("   - Blue box-shadow highlight (2 seconds)")
        print("   - Green success banner with checkmark")
        print("   - Fade-out animation for success message")
        print("   - Professional, polished experience")
        
        print("\n🌐 Test the Auto-Scroll:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - Submit a comment and watch the magic!")
        print("   - Should scroll automatically to show your comment")
        
    else:
        print("\n❌ Upload failed")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")