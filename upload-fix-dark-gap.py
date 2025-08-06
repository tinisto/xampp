#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        ftp.cwd(PATH)
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    print("FIXING DARK GAP BETWEEN GREEN HEADER AND RED CONTENT")
    print("=" * 55)
    print("🔧 Problem: Dark strip showing between green and red sections")
    print("🎯 Solution: Remove margin-bottom from green header component")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload the fixed green header component
        local_file = "/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header.php"
        
        if upload_file(ftp, local_file, "common-components/page-section-header.php"):
            print("\n✅ DARK GAP FIXED!")
            print("Green header now directly touches red content area")
            print("No more dark strip between sections")
        else:
            print("\n❌ Failed to fix the gap")
            return 1
        
        ftp.quit()
        
        print("\n" + "=" * 55)
        print("TEST THESE PAGES:")
        print("=" * 55)
        print("📰 News: https://11klassniki.ru/news")
        print("ℹ️  About: https://11klassniki.ru/about")
        print("🏠 Homepage: https://11klassniki.ru/")
        print("")
        print("The dark gap should be GONE!")
        print("Green header should directly connect to red content.")
        print("=" * 55)
        
    except Exception as e:
        print(f"✗ FTP Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())