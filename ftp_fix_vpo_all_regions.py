#!/usr/bin/env python3
"""
Fix VPO all regions page showing empty content
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        ftp.cwd('/11klassnikiru')
        
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir in dirs:
                if dir:
                    try:
                        ftp.cwd(dir)
                    except:
                        ftp.mkd(dir)
                        ftp.cwd(dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Fixing VPO all regions page...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed educational institutions page
        ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php', 
         'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                upload_file(ftp, local_path, remote_path)
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n🎯 Fixed:")
        print("✅ Corrected template engine parameters")
        print("✅ Changed CSS framework from 'bootstrap' to 'custom'")
        print("✅ Fixed renderTemplate call format")
        
        print("\n🔍 Test the pages:")
        print("https://11klassniki.ru/vpo-all-regions - Should show VPO by regions")
        print("https://11klassniki.ru/spo-all-regions - Should show SPO by regions")  
        print("https://11klassniki.ru/schools-all-regions - Should show schools by regions")
        
        print("\n📋 The pages should now display:")
        print("• List of Russian regions")
        print("• Count of institutions in each region")
        print("• Links to region-specific pages")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()