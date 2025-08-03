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
    print("🚀 Deploying vanilla CSS/JS header system...")
    
    files_to_upload = [
        # New vanilla CSS/JS header
        ('common-components/header-unified-simple.php', 
         'common-components/header-unified-simple.php'),
        
        # Updated template engine to use vanilla header
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
        
        print("\n📤 Uploading vanilla CSS/JS header files...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 Vanilla CSS/JS header system deployed!")
            print("\n✅ What's new:")
            print("   • Pure CSS styling - NO Bootstrap dependencies")
            print("   • Vanilla JavaScript for all interactions")
            print("   • Mobile menu with pure JS toggle")
            print("   • Dropdowns without Bootstrap")
            print("   • Theme toggle working with vanilla JS")
            print("   • All pages now use this ONE vanilla header")
            print("\n🔍 Test these pages - they should all have working headers:")
            print("   • https://11klassniki.ru/tests")
            print("   • https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
            print("   • https://11klassniki.ru/news")
            print("   • https://11klassniki.ru/")
            print("   • https://11klassniki.ru/vpo-all-regions")
            print("\n📝 Note: As you requested, this uses vanilla CSS and JS, not Bootstrap!")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()