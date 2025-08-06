#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP credentials
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
        print("✓ Connected to FTP server")
        
        # Files to upload for CSS fixes
        files_to_upload = [
            {
                'local': 'css/dashboard/dashboard.css',
                'remote': 'css/dashboard/dashboard.css',
                'desc': 'Fixed dashboard CSS - removed black background'
            },
            {
                'local': 'css/dashboard/dashboard.min.css', 
                'remote': 'css/dashboard/dashboard.min.css',
                'desc': 'Fixed minified dashboard CSS - removed black background'
            },
            {
                'local': 'common-components/template-engine-ultimate.php',
                'remote': 'common-components/template-engine-ultimate.php',
                'desc': 'Fixed template engine - removed debug colors (orange/red)'
            }
        ]
        
        print("\n🎨 Uploading CSS fixes to remove conflicting backgrounds...")
        print("=" * 65)
        
        success_count = 0
        for file_info in files_to_upload:
            print(f"\n📁 {file_info['desc']}")
            
            if upload_file(ftp, file_info['local'], file_info['remote']):
                success_count += 1
        
        print(f"\n✅ Upload completed: {success_count}/{len(files_to_upload)} files uploaded successfully")
        
        if success_count == len(files_to_upload):
            print("\n🎯 CHANGES MADE:")
            print("• Dashboard CSS: Black background → CSS variable background")
            print("• Template Engine: Removed orange body background (debug)")
            print("• Template Engine: Removed red main content background (debug)")
            print("\n📋 RESULT:")
            print("• Dashboard pages now use proper themed backgrounds")
            print("• No more conflicting black backgrounds")
            print("• Debug colors removed from all pages")
            print("\n🚀 All background conflicts should now be resolved!")
            
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()