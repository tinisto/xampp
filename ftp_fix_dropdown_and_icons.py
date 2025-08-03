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
    print("🎨 FIXING CATEGORIES DROPDOWN & ICON SIZES...")
    
    files_to_upload = [
        # Fixed header with smaller categories and equal icon sizes
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
        
        print("\n📤 Uploading fixes...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n✅ CATEGORIES DROPDOWN & ICONS FIXED!")
            print("\n📋 Categories Dropdown improvements:")
            print("   • Font size reduced: 12px (was larger)")
            print("   • Padding reduced: 6px 14px (was 10px 20px)")
            print("   • Line height tighter: 1.2 (more compact)")
            print("   • No scrollbar: overflow-y: visible")
            print("   • Shows full list without scrolling")
            print("   • Smaller hover indent: 18px (was 25px)")
            print("\n🎯 Icon Size fixes:")
            print("   • Theme toggle button: 36px × 36px")
            print("   • User avatar: 36px × 36px (exactly same size)")
            print("   • Both icons now perfectly aligned")
            print("   • Equal sizes whether hovering or not")
            print("\n🌐 Test now: https://11klassniki.ru")
            print("   • Click 'Категории' - compact list, no scrollbar")
            print("   • Check header icons - same size always")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()