#!/usr/bin/env python3

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

# Files to restore
restore_files = [
    ('pages/tests/test-improved-old-standalone.php', 'pages/tests/test-improved.php'),
]

def restore_files():
    try:
        print(f"🔧 Restoring test pages...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✓ Connected successfully")
        
        print("\n🔄 Restoring test pages from backups...")
        for backup_file, original_file in restore_files:
            try:
                ftp.rename(backup_file, original_file)
                print(f"  ✓ Restored {original_file} from backup")
            except Exception as e:
                print(f"  ⚠️  Could not restore {original_file}: {e}")
        
        print("\n✅ TEST PAGES RESTORED!")
        print("\nAll pages should now be working with their original templates.")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    restore_files()