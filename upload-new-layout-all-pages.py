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
        
        # Files to upload for new layout
        files_to_upload = [
            {
                'local': 'test-real-layout.php',
                'remote': 'test-real-layout.php',
                'desc': 'Updated test layout (removed white border)'
            },
            {
                'local': 'common-components/content-wrapper.php',
                'remote': 'common-components/content-wrapper.php',
                'desc': 'Updated content wrapper with new layout structure'
            },
            {
                'local': 'common-components/template-engine-ultimate.php',
                'remote': 'common-components/template-engine-ultimate.php',
                'desc': 'Template engine with new yellow background layout'
            }
        ]
        
        print("\n🚀 Uploading new layout structure to 11klassniki.ru...")
        print("=" * 60)
        
        success_count = 0
        for file_info in files_to_upload:
            print(f"\n📁 {file_info['desc']}")
            
            if upload_file(ftp, file_info['local'], file_info['remote']):
                success_count += 1
        
        print(f"\n✅ Upload completed: {success_count}/{len(files_to_upload)} files uploaded successfully")
        
        if success_count == len(files_to_upload):
            print("\n🎯 NEW LAYOUT IS NOW LIVE:")
            print("All pages using template-engine-ultimate.php now have the new layout!")
            print("\nTest URLs:")
            print("- https://11klassniki.ru/ (homepage with new layout)")
            print("- https://11klassniki.ru/test-real-layout.php (test page)")
            print("\nNew layout features:")
            print("✓ Yellow background wrapper")  
            print("✓ Green page header section")
            print("✓ Red main content area")
            print("✓ Blue comments section")
            print("✓ Proper mobile/desktop spacing")
            print("✓ No overscroll issues")
            
            print("\nExcludes login/registration/forgot password pages (as requested)")
            
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()