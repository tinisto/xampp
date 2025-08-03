#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration - exactly as before
FTP_HOST = "11klassniki.ru"
FTP_USER = "u2709849"
FTP_PASS = "Qazwsxedc123"

def upload_critical_files():
    """Upload only the most critical template files"""
    
    print("🚀 Uploading ONE TEMPLATE System...")
    
    # Just the most critical file
    critical_file = 'common-components/template-engine-ultimate.php'
    local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{critical_file}'
    
    if not os.path.exists(local_path):
        print(f"❌ Critical file not found: {critical_file}")
        return
    
    try:
        # Connect with exact same method as before
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        
        # Enable debug to see what's happening
        # ftp.set_debuglevel(2)
        
        # Login
        print(f"🔐 Logging in as {FTP_USER}...")
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Login successful!")
        
        # Set passive mode
        ftp.set_pasv(True)
        
        # Try to upload the ultimate template
        print(f"\n📤 Uploading {critical_file}...")
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {critical_file}', file)
        print(f"✅ Successfully uploaded: {critical_file}")
        
        # Upload a few more critical files
        other_files = [
            'pages/search/search-process.php',
            'pages/dashboard/admin-index/dashboard.php',
        ]
        
        for file_path in other_files:
            local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
            if os.path.exists(local_file):
                try:
                    with open(local_file, 'rb') as f:
                        ftp.storbinary(f'STOR {file_path}', f)
                    print(f"✅ Uploaded: {file_path}")
                except Exception as e:
                    print(f"⚠️  Failed to upload {file_path}: {e}")
        
        print("\n🎉 ONE TEMPLATE system deployed!")
        print("   The unified template is now active on production!")
        
        # Close connection
        ftp.quit()
        
    except ftplib.error_perm as e:
        if "530" in str(e):
            print(f"\n❌ Login failed: {e}")
            print("\n🔧 Troubleshooting:")
            print("1. The credentials might have changed")
            print("2. The FTP account might be temporarily locked")
            print("3. Try using an FTP client like FileZilla to verify")
        else:
            print(f"❌ Permission error: {e}")
    except Exception as e:
        print(f"❌ Error: {type(e).__name__}: {e}")

if __name__ == "__main__":
    upload_critical_files()