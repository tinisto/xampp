#!/usr/bin/env python3
"""
Final upload to fix test page and verify dashboard
"""

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🚀 Final fix for test page and dashboard...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Delete old test file
        try:
            ftp.delete("/test-comment-system.php")
            print("✅ Deleted old test file")
        except:
            pass
        
        # Upload new version with template
        with open("test-comment-system-with-template.php", 'rb') as file:
            ftp.storbinary("STOR /test-comment-system.php", file)
        print("✅ Uploaded test file with template (includes new favicon)")
        
        # Verify dashboard monitoring exists
        try:
            size = ftp.size("/dashboard-monitoring.php")
            print(f"✅ dashboard-monitoring.php exists ({size} bytes)")
        except:
            print("❌ dashboard-monitoring.php not found - uploading...")
            # Upload it
            with open("dashboard-monitoring.php", 'rb') as file:
                ftp.storbinary("STOR /dashboard-monitoring.php", file)
            print("✅ Uploaded dashboard-monitoring.php")
        
        # List files to verify
        print("\n📁 Verifying dashboard files:")
        files = ftp.nlst()
        dashboard_files = [f for f in files if 'dashboard' in f.lower()]
        for f in dashboard_files:
            print(f"  - {f}")
        
        ftp.quit()
        print("\n✅ Complete! Test at:")
        print("1. https://11klassniki.ru/test-comment-system.php (now with new favicon)")
        print("2. https://11klassniki.ru/dashboard-monitoring.php")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())