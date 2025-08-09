#!/usr/bin/env python3
import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_migration_setup():
    """Upload the setup migration script"""
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server\n")
        
        # Upload the setup migration script
        local_file = "setup-comments-migration.php"
        remote_file = "setup-comments-migration.php"
        
        ftp.cwd(FTP_ROOT)
        
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        
        ftp.quit()
        
        print("\n✅ Migration setup script uploaded!")
        print("\n📌 To run the migration, visit:")
        print(f"   https://11klassniki.ru/setup-comments-migration.php?token=setup2025secure")
        print("\n⚠️  IMPORTANT: Delete the file after migration:")
        print("   https://11klassniki.ru/setup-comments-migration.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {str(e)}")

if __name__ == "__main__":
    upload_migration_setup()