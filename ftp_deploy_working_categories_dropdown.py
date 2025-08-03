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
    print("🔧 Deploying WORKING Categories dropdown solution...")
    
    files_to_upload = [
        # New cleaner header with working Categories
        ('common-components/header-unified-simple-safe-v2.php', 
         'common-components/header-unified-simple-safe-v2.php'),
        
        # Updated template engine to use new header
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
        
        print("\n📤 Uploading working Categories solution...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 Working Categories dropdown deployed!")
            print("\n✅ Solution features:")
            print("   • Simple CSS - display: none/block approach")
            print("   • Desktop: Hover to show dropdown")
            print("   • Mobile: Click to toggle dropdown")
            print("   • Proper event handling with stopPropagation")
            print("   • Click outside to close on all devices")
            print("\n🔧 How it works:")
            print("   • Desktop (≥992px): CSS hover shows dropdown")
            print("   • Mobile (<992px): JavaScript toggle on click")
            print("   • Both: .show class toggles visibility")
            print("\n🔍 Test the Categories dropdown:")
            print("   • Desktop: Hover over 'Категории'")
            print("   • Mobile: Click 'Категории' to open/close")
            print("   • VPO/SPO links also fixed!")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()