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

def delete_remote_file(ftp, remote_file):
    """Delete a file from the remote server"""
    try:
        ftp.delete(remote_file)
        print(f"🗑️ Deleted: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Could not delete {remote_file}: {e}")
        return False

def main():
    """Main upload function"""
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Files to upload for CSS consolidation
        files_to_upload = [
            {
                'local': 'css/unified-styles.css',
                'remote': 'css/unified-styles.css',
                'desc': 'Updated unified styles - includes merged test.css styles'
            }
        ]
        
        # Files to remove from server (consolidated files)
        files_to_remove = [
            'css/test.css'
        ]
        
        print("\n🧹 Uploading CSS consolidation changes...")
        print("=" * 50)
        
        success_count = 0
        for file_info in files_to_upload:
            print(f"\n📁 {file_info['desc']}")
            
            if upload_file(ftp, file_info['local'], file_info['remote']):
                success_count += 1
        
        print(f"\n🗑️ Removing consolidated files from server...")
        removed_count = 0
        for file_path in files_to_remove:
            if delete_remote_file(ftp, file_path):
                removed_count += 1
        
        print(f"\n✅ CSS Consolidation completed!")
        print(f"📊 Files uploaded: {success_count}/{len(files_to_upload)}")
        print(f"🗑️ Files removed: {removed_count}/{len(files_to_remove)}")
        
        if success_count == len(files_to_upload):
            print("\n🎯 CONSOLIDATION RESULTS:")
            print("• Merged test.css → unified-styles.css")
            print("• Removed redundant test.css file")
            print("• Template engine uses 3 focused CSS files:")
            print("  - unified-styles.css (main styles)")
            print("  - authorization.css (auth page gradient)")
            print("  - dashboard/dashboard.css (dashboard specifics)")
            print("\n🚀 CSS structure is now more organized and efficient!")
            
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()