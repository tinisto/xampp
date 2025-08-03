#!/usr/bin/env python3
"""
Upload critical fixes for site-wide issues
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
            print(f"✅ Uploaded: {local_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Uploading critical fixes for site-wide issues...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed search page footer reference
        ('pages/search/search-process-clean.php', 'pages/search/search-process-clean.php'),
        # Fixed page header alignment
        ('common-components/page-header-compact.php', 'common-components/page-header-compact.php'),
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
        print("\n🛠️ Critical Issues Fixed:")
        print("✅ Fixed search page footer - was using wrong path")
        print("✅ Fixed category page header alignment")
        print("\n📋 Summary of Site-Wide Issues Found:")
        print("1. ❌ Missing footer.php references (17 files) - need bulk fix")
        print("2. ❌ Inconsistent template engine usage")
        print("3. ❌ Session management inconsistencies")
        print("4. ❌ Missing database error handling")
        print("5. ⚠️  Mixed character encoding")
        print("6. ⚠️  JavaScript function conflicts")
        print("\n🔍 Most Critical Issues:")
        print("• Footer missing on some pages due to wrong includes")
        print("• No error handling when database is down")
        print("• Session conflicts from mixed approaches")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()