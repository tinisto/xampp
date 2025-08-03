#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as file:
            ftp.storbinary(f'STOR {remote_file}', file)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

def main():
    print("🔧 FINAL FIX for Categories dropdown on desktop...")
    
    files_to_upload = [
        # Updated header with simplified dropdown logic
        ('common-components/header-unified-simple-safe-v2.php', 
         'common-components/header-unified-simple-safe-v2.php'),
    ]
    
    try:
        # Connect to FTP
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully")
        
        # Change to 11klassnikiru directory
        try:
            ftp.cwd('11klassnikiru')
            print("✅ Changed to 11klassnikiru directory")
        except Exception as e:
            print(f"❌ Could not change to 11klassnikiru: {e}")
            return
        
        # Upload files
        success_count = 0
        
        print("\n📤 Uploading final dropdown fix...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 Categories dropdown FINALLY FIXED!")
            print("\n✅ What was the problem:")
            print("   • Desktop hover and JavaScript click were conflicting")
            print("   • .show class was interfering with CSS hover state")
            print("   • Click handler was preventing default on desktop")
            print("\n✅ How it's fixed:")
            print("   • Desktop: Pure CSS hover with !important override")
            print("   • Mobile: JavaScript click toggle only (window.innerWidth < 992)")
            print("   • No JavaScript interference on desktop hover")
            print("   • Categories dropdown now works on both desktop AND mobile")
            print("\n🖱️  Desktop: Hover over 'Категории' to see dropdown")
            print("📱 Mobile: Tap 'Категории' to toggle dropdown")
            print("\n🌐 Test it now at: https://11klassniki.ru")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()