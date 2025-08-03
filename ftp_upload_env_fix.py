#!/usr/bin/env python3
"""
Fix environment and config issues
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
    print("🚀 Fixing environment configuration...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Config files
        ('config/minimal_env.php', 'config/minimal_env.php'),
        ('config/loadEnv_simple.php', 'config/loadEnv_simple.php'),
        ('config/loadEnv_patched.php', 'config/loadEnv.php'),  # Replace the original
        
        # Database connection
        ('database/db_connections.php', 'database/db_connections.php'),
        ('common-components/check_under_construction.php', 'common-components/check_under_construction.php'),
        
        # Test file
        ('simple_category_test.php', 'simple_category_test.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        success_count = 0
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        print(f"\n📊 Uploaded {success_count}/{len(files_to_upload)} files")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("🔍 Test: https://11klassniki.ru/simple_category_test.php")
        print("🔍 Category: https://11klassniki.ru/category/mir-uvlecheniy")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()