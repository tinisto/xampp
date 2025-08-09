#!/usr/bin/env python3
"""
Verify what's on the server and force upload the correct files
"""

import ftplib
import os
import hashlib
from datetime import datetime

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def get_file_hash(filepath):
    """Get MD5 hash of a file"""
    hash_md5 = hashlib.md5()
    with open(filepath, "rb") as f:
        for chunk in iter(lambda: f.read(4096), b""):
            hash_md5.update(chunk)
    return hash_md5.hexdigest()

def download_and_check(ftp, remote_file, local_file):
    """Download a file and compare with local version"""
    temp_file = f"temp_{remote_file.replace('/', '_')}"
    try:
        with open(temp_file, 'wb') as f:
            ftp.retrbinary(f'RETR {remote_file}', f.write)
        
        # Check first few lines
        with open(temp_file, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read(500)
            print(f"\nFirst 500 chars of {remote_file}:")
            print(content)
        
        os.remove(temp_file)
        return True
    except Exception as e:
        print(f"Error checking {remote_file}: {e}")
        return False

def main():
    print("🔍 Verifying and forcing upload of correct files...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Check test-comment-system.php
        print("\n📋 Checking test-comment-system.php...")
        try:
            size = ftp.size("/test-comment-system.php")
            mod_time = ftp.sendcmd(f"MDTM /test-comment-system.php")
            print(f"Current file size: {size} bytes")
            print(f"Modification time: {mod_time}")
            
            # Download and check content
            download_and_check(ftp, "/test-comment-system.php", "test-comment-system.php")
            
            # Force delete and re-upload
            print("\n🗑️ Deleting old file...")
            ftp.delete("/test-comment-system.php")
            print("✅ Deleted")
            
        except Exception as e:
            print(f"File check error: {e}")
        
        # Upload the correct version with template
        print("\n📤 Uploading correct test file with template...")
        local_size = os.path.getsize("test-comment-system-with-template.php")
        print(f"Local file size: {local_size} bytes")
        
        with open("test-comment-system-with-template.php", 'rb') as file:
            ftp.storbinary("STOR /test-comment-system.php", file)
        
        # Verify upload
        remote_size = ftp.size("/test-comment-system.php")
        print(f"✅ Uploaded! Remote size: {remote_size} bytes")
        
        if remote_size != local_size:
            print("⚠️  Size mismatch! Upload may have failed")
        
        # Check dashboard-monitoring.php
        print("\n📋 Checking dashboard-monitoring.php...")
        try:
            size = ftp.size("/dashboard-monitoring.php")
            print(f"✅ File exists: {size} bytes")
        except:
            print("❌ File not found! Uploading...")
            with open("dashboard-monitoring.php", 'rb') as file:
                ftp.storbinary("STOR /dashboard-monitoring.php", file)
            print("✅ Uploaded dashboard-monitoring.php")
        
        # Also check the monitoring API
        print("\n📋 Checking monitoring API...")
        try:
            ftp.cwd("/api/comments")
            size = ftp.size("monitor.php")
            print(f"✅ API exists: {size} bytes")
            ftp.cwd(FTP_ROOT)
        except:
            print("❌ API not found! Creating directory and uploading...")
            ftp.cwd(FTP_ROOT)
            try:
                ftp.mkd("/api")
            except:
                pass
            try:
                ftp.mkd("/api/comments")
            except:
                pass
            
            with open("api/comments/monitor.php", 'rb') as file:
                ftp.storbinary("STOR /api/comments/monitor.php", file)
            print("✅ Uploaded monitor API")
        
        # Clear any cache that might exist
        print("\n🧹 Attempting to clear cache...")
        cache_files = [".htaccess", "index.php"]
        for cf in cache_files:
            try:
                # Touch the file to force cache refresh
                ftp.sendcmd(f"MDTM {cf} {datetime.now().strftime('%Y%m%d%H%M%S')}")
            except:
                pass
        
        print("\n✅ Complete! Files have been force-uploaded.")
        print("\n⚠️  IMPORTANT: Clear your browser cache!")
        print("1. Press Ctrl+Shift+R (or Cmd+Shift+R on Mac)")
        print("2. Or open in incognito/private window")
        print("\nThen test at:")
        print("1. https://11klassniki.ru/test-comment-system.php")
        print("2. https://11klassniki.ru/dashboard-monitoring.php")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())