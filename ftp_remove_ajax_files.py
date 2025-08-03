#!/usr/bin/env python3

import ftplib

# FTP details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("Connecting to FTP server...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    print("✓ Connected")
    
    # Files to remove
    files_to_remove = [
        'common-components/news-category-navigation.php',
        'api/news-data.php',
        'test-api-direct.php'
    ]
    
    for file_path in files_to_remove:
        try:
            ftp.delete(file_path)
            print(f"✓ Removed: {file_path}")
        except Exception as e:
            print(f"⚠️  Could not remove {file_path}: {e}")
    
    # Also remove api directory if empty
    try:
        ftp.rmd('api')
        print("✓ Removed empty api directory")
    except Exception as e:
        print(f"⚠️  Could not remove api directory: {e}")
    
    print("\n✅ AJAX CLEANUP COMPLETE!")
    print("\n🗑️  Removed files:")
    print("1. 🚫 AJAX navigation component")
    print("2. 🚫 API endpoint for AJAX calls")
    print("3. 🚫 API diagnostic test file")
    print("\n✨ Codebase now uses simple, reliable navigation only!")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")