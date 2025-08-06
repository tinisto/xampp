#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP credentials for franko user
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

def upload_file(ftp, local_file, remote_file):
    """Upload a single file"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✓ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"✗ Error uploading {remote_file}: {e}")
        return False

def main():
    """Main upload function"""
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server (franko@ipage.com)")
        
        # Upload updated homepage content
        file_to_upload = {
            'local': 'index_content_posts_with_news_style.php',
            'remote': 'index_content_posts_with_news_style.php',
            'desc': 'Homepage cards moved into red content area'
        }
        
        print(f"\n🚀 Uploading homepage cards fix to 11klassniki.ru...")
        print("=" * 60)
        print(f"\n📁 {file_to_upload['desc']}")
        
        if upload_file(ftp, file_to_upload['local'], file_to_upload['remote']):
            print(f"\n✅ Upload completed successfully")
            print("\n🎯 Cards are now inside the red content area:")
            print("- Removed container wrapper with padding/margins")
            print("- Cards now display directly in red background")
            print("- Statistics section also moved to red area")
            print("\n📱 View updated homepage:")
            print("https://11klassniki.ru/")
        else:
            print(f"\n❌ Upload failed")
            
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()