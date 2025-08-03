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
    print("🚀 Deploying PURE vanilla CSS header - NO Bootstrap naming!")
    
    files_to_upload = [
        # Pure vanilla CSS header with proper class names
        ('common-components/header-unified-simple-safe.php', 
         'common-components/header-unified-simple-safe.php'),
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
        
        print("\n📤 Uploading pure vanilla header...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 Pure vanilla CSS header deployed!")
            print("\n✅ Class name changes:")
            print("   • .unified-navbar → .header")
            print("   • .navbar-container → .header-container")  
            print("   • .navbar-brand → .header-brand")
            print("   • .navbar-nav → .header-nav")
            print("   • <nav> → <header> (semantic HTML)")
            print("   • #navbarNav → #headerNav")
            print("\n🚫 NO Bootstrap dependencies:")
            print("   • No Bootstrap CSS classes")
            print("   • No Bootstrap JavaScript")
            print("   • Pure vanilla CSS and JS only")
            print("   • Compact single-row layout")
            print("\n🔍 Test the clean vanilla header:")
            print("   • https://11klassniki.ru/schools-all-regions")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()