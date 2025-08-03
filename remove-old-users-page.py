#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

try:
    print("🔧 Removing Old Users Dashboard Page")
    print("===================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    # Remove the old users-view.php file
    try:
        ftp.delete('pages/dashboard/users-dashboard/users-view/users-view.php')
        print("✅ Removed: pages/dashboard/users-dashboard/users-view/users-view.php")
    except Exception as e:
        print(f"⚠️  Could not remove old file: {str(e)}")
    
    ftp.quit()
    
    print("\n✅ Old users dashboard page removed successfully!")
    print("\n🔧 What was done:")
    print("   - Deleted the old users-view.php file from server")
    print("   - Now only the beautiful professional dashboard is accessible")
    
    print("\n🔗 Users dashboard is now only available via:")
    print("https://11klassniki.ru/dashboard/users (professional design)")
    
    print("\n📋 The old URL will now show 404:")
    print("https://11klassniki.ru/pages/dashboard/users-dashboard/users-view/users-view.php")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")