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
    print("🔧 Deploying HTML Entity Decoding Fix")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading fixed editor...")
    
    if upload_file(ftp, 'dashboard-edit-content.php', 'dashboard-edit-content.php'):
        print("\n✅ Fix deployed successfully!")
        print("\n🌟 What's Fixed:")
        print("   - Added html_entity_decode() to textarea content")
        print("   - TinyMCE now properly decodes HTML entities")
        print("   - HTML tags display as formatted content")
        print("   - No more visible <p>, <strong> tags in editor")
        
        print("\n📝 Technical Details:")
        print("   - Decodes &lt; to < and &gt; to >")
        print("   - Decodes &quot; to \" and &amp; to &")
        print("   - Uses ENT_QUOTES | ENT_HTML5 flags")
        print("   - UTF-8 charset support")
        
        print("\n🌐 Test at:")
        print("   https://11klassniki.ru/edit/post/451")
        
    else:
        print("\n❌ Deployment failed")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")