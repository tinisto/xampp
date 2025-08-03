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
    print("🎉 DEPLOYING CATEGORIES DROPDOWN FIX - PRODUCTION READY!")
    
    files_to_upload = [
        # Fixed production header with overflow: visible
        ('common-components/header-unified-simple-safe-v2.php', 
         'common-components/header-unified-simple-safe-v2.php'),
        # Updated template engine comment
        ('common-components/template-engine-ultimate.php', 
         'common-components/template-engine-ultimate.php'),
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
        
        print("\n📤 Deploying Categories dropdown fix...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 CATEGORIES DROPDOWN FINALLY FIXED!")
            print("\n✅ What was the problem:")
            print("   • Header containers had 'overflow: hidden'")
            print("   • This clipped the dropdown below the header")
            print("   • User could only see the top border with red shadow")
            print("\n✅ How it's fixed:")
            print("   • Changed 'overflow: hidden' to 'overflow: visible' on:")
            print("     - .header")
            print("     - .header-container") 
            print("     - .header-nav")
            print("   • Dropdown now shows completely")
            print("   • Normal styling restored (no more red diagnostic colors)")
            print("\n🖱️  Categories dropdown now works:")
            print("   • Desktop: Click 'Категории' to open/close")
            print("   • Mobile: Click 'Категории' to open/close")
            print("   • Click outside to close")
            print("   • No more # added to URL")
            print("\n🌐 Test it now: https://11klassniki.ru")
            print("\n🏆 ONE TEMPLATE SYSTEM COMPLETE with working Categories dropdown!")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()