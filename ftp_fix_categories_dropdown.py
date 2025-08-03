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
    print("🔧 Fixing Categories dropdown functionality...")
    
    files_to_upload = [
        # Fixed header with working Categories dropdown
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
        
        print("\n📤 Uploading Categories dropdown fix...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 Categories dropdown should now work!")
            print("\n✅ Fixes applied:")
            print("   • Added console.log debugging to toggleDropdown()")
            print("   • Added event listeners via DOMContentLoaded")
            print("   • Dual approach: onclick attribute + event listeners")
            print("   • Better error handling in dropdown function")
            print("\n🔍 Test the Categories dropdown:")
            print("   • Click 'Категории' - should open dropdown")
            print("   • Check browser console for debug messages")
            print("   • Should show old categories from database")
            print("   • Click outside to close dropdown")
            print("\n📋 Debug steps if still not working:")
            print("   • Open browser console (F12)")
            print("   • Click Categories and check for console messages")
            print("   • Look for any JavaScript errors")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()