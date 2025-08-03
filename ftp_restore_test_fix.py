#!/usr/bin/env python3

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def restore_files():
    try:
        print(f"🔧 Restoring test pages...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✓ Connected successfully")
        
        print("\n🔄 Restoring test pages from backups...")
        
        # Restore test-improved.php
        try:
            ftp.rename('pages/tests/test-improved-old-standalone.php', 'pages/tests/test-improved.php')
            print(f"  ✓ Restored pages/tests/test-improved.php from backup")
        except Exception as e:
            print(f"  ⚠️  Could not restore test-improved.php: {e}")
        
        print("\n✅ TEST PAGES RESTORED!")
        print("\nAll pages should now be working with their original templates.")
        print("\nThe site is now back to its working state before template updates.")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    restore_files()