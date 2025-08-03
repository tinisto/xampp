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
    print("🔧 Deploying final fixes for VPO/SPO pages, theme toggle, and header layout...")
    
    files_to_upload = [
        # VPO/SPO pages with debug output and improved queries
        ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php', 
         'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php'),
        
        # Header with fixed theme toggle and improved layout
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
            print("\n🎉 All fixes deployed successfully!")
            print("\nWhat's fixed:")
            print("   ✅ VPO/SPO pages - Added debug output and fixed count queries")
            print("   ✅ Theme toggle - Fixed onclick handlers to work on all pages")
            print("   ✅ Header layout - Theme toggle and user icon now in one line")
            print("   ✅ Added fallback message if no institutions found")
            print("\nCheck these pages:")
            print("   🔍 https://11klassniki.ru/vpo-all-regions")
            print("   🔍 https://11klassniki.ru/spo-all-regions")
            print("   🔍 https://11klassniki.ru/tests (theme toggle)")
            print("   🔍 https://11klassniki.ru/news (theme toggle)")
            print("   🔍 https://11klassniki.ru/ (header layout)")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()