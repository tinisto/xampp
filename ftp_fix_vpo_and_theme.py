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
    print("🔧 Fixing VPO/SPO pages and theme toggle issues...")
    
    files_to_upload = [
        # Fix for VPO/SPO pages with debug output
        ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php', 
         'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php'),
        ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-debug.php', 
         'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-debug.php'),
        
        # Fix theme toggle button onclick handlers
        ('common-components/header-modern.php', 
         'common-components/header-modern.php'),
    ]
    
    try:
        # Connect to FTP
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully")
        
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
            print("\n🎉 Fixes deployed successfully!")
            print("\nWhat's fixed:")
            print("   ✅ VPO/SPO pages - Added debug output to diagnose empty content")
            print("   ✅ Theme toggle - Fixed onclick handlers to call toggleTheme() directly")
            print("   ✅ Removed duplicate toggleTheme function definitions")
            print("\nCheck:")
            print("   🔍 https://11klassniki.ru/vpo-all-regions - Look for HTML comments with debug info")
            print("   🔍 https://11klassniki.ru/spo-all-regions - Look for HTML comments with debug info")
            print("   🔍 https://11klassniki.ru/tests - Theme toggle should work")
            print("   🔍 https://11klassniki.ru/news - Theme toggle should work")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()