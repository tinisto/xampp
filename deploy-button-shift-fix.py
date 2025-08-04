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
    print("🔧 Fixing Button Shifting Issue")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload file
    print("\n📤 Uploading button shift fix...")
    
    if upload_file(ftp, 'comments/modern-comments-component.php', 'comments/modern-comments-component.php'):
        print("\n✅ Button shift fix deployed!")
        
        print("\n🔧 What's Fixed:")
        print("   - ✅ Reserved space for button (min-height: 45px)")
        print("   - ✅ Changed from display:none to opacity + visibility")
        print("   - ✅ Button stays in document flow")
        print("   - ✅ No layout shifting when typing")
        print("   - ✅ Smooth fade transition maintained")
        
        print("\n🎨 Technical Changes:")
        print("   - Wrapped button in flex container")
        print("   - Set min-height to reserve space")
        print("   - Uses opacity: 0/1 instead of display: none/block")
        print("   - Uses visibility: hidden/visible")
        print("   - Maintains transform animation")
        
        print("\n🌐 Test the Fix:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - Type in comment box - NO shifting should occur")
        print("   - Button should smoothly fade in/out in place")
        print("   - Layout should remain stable")
        
    else:
        print("\n❌ Upload failed")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")