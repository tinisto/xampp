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
    print("🔧 Final Categories dropdown fix - simple click implementation...")
    
    files_to_upload = [
        # Simplified header with click-only dropdown
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
        
        print("\n📤 Uploading simple click dropdown...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 Categories dropdown now works with SIMPLE CLICK!")
            print("\n✅ Changes made:")
            print("   • Removed dropdown arrow icon (▼)")
            print("   • Changed href from '#' to 'javascript:void(0)' - NO MORE # in URL!")
            print("   • Simple click toggle on both desktop and mobile")
            print("   • Removed complex hover/click conflict logic")
            print("\n🖱️  How to use:")
            print("   • Desktop: Click 'Категории' to open/close dropdown")
            print("   • Mobile: Click 'Категории' to open/close dropdown")
            print("   • Click outside dropdown to close it")
            print("\n🌐 Test now: https://11klassniki.ru")
            print("   Click 'Категории' - it should open dropdown without adding # to URL!")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()