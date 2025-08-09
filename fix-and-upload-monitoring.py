#!/usr/bin/env python3
"""
Fix and upload monitoring dashboard and test files
"""

import ftplib
import os
from pathlib import Path

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def check_file_exists(ftp, remote_path):
    """Check if a file exists on FTP"""
    try:
        size = ftp.size(remote_path)
        return size is not None
    except:
        return False

def upload_file(ftp, local_path, remote_path):
    """Upload a single file"""
    try:
        # Upload file
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        
        # Verify upload
        size = ftp.size(remote_path)
        local_size = os.path.getsize(local_path)
        
        if size == local_size:
            print(f"✅ Uploaded successfully: {local_path} -> {remote_path} ({size} bytes)")
            return True
        else:
            print(f"⚠️  Size mismatch: local={local_size}, remote={size}")
            return False
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Fixing monitoring dashboard and test file issues...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # First, check what's in the root directory
        print("\n📁 Checking existing files...")
        files = ftp.nlst()
        
        dashboard_exists = False
        test_exists = False
        
        for file in files:
            if 'dashboard-monitoring' in file:
                print(f"Found: {file}")
                dashboard_exists = True
            if 'test-comment-system' in file:
                print(f"Found: {file}")
                test_exists = True
        
        # Fix test file issue - delete and re-upload
        print("\n🔧 Fixing test file...")
        if test_exists:
            try:
                ftp.delete("/test-comment-system.php")
                print("✅ Deleted old test file")
            except Exception as e:
                print(f"ℹ️  Could not delete test file: {e}")
        
        # Upload fixed test file
        upload_file(ftp, "test-comment-system-fixed.php", "/test-comment-system.php")
        
        # Upload monitoring dashboard
        print("\n🔧 Uploading monitoring dashboard...")
        upload_file(ftp, "dashboard-monitoring.php", "/dashboard-monitoring.php")
        
        # Also upload the monitoring API endpoint
        print("\n🔧 Checking monitoring API...")
        try:
            ftp.cwd("/api/comments")
            upload_file(ftp, "api/comments/monitor.php", "monitor.php")
            ftp.cwd(FTP_ROOT)
        except Exception as e:
            print(f"⚠️  API directory issue: {e}")
            # Try creating the structure
            try:
                ftp.mkd("/api")
                ftp.mkd("/api/comments")
                ftp.cwd("/api/comments")
                upload_file(ftp, "api/comments/monitor.php", "monitor.php")
                ftp.cwd(FTP_ROOT)
            except:
                pass
        
        print("\n📋 Final verification...")
        
        # Check if files exist
        if check_file_exists(ftp, "/dashboard-monitoring.php"):
            print("✅ dashboard-monitoring.php exists")
        else:
            print("❌ dashboard-monitoring.php NOT found")
            
        if check_file_exists(ftp, "/test-comment-system.php"):
            print("✅ test-comment-system.php exists")
        else:
            print("❌ test-comment-system.php NOT found")
        
        # Close connection
        ftp.quit()
        
        print("\n✅ Upload complete!")
        print("\n📋 URLs to test:")
        print("1. https://11klassniki.ru/test-comment-system.php")
        print("2. https://11klassniki.ru/dashboard-monitoring.php")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())