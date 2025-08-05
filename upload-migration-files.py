#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_migration_files():
    print("🚀 Uploading migration files")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Create migrations directory
        try:
            ftp.mkd('migrations')
        except:
            pass  # Directory might already exist
        
        ftp.cwd('migrations')
        
        # Upload migration files
        files_to_upload = [
            ('standardize_field_names.sql', '/Applications/XAMPP/xamppfiles/htdocs/migrations/standardize_field_names.sql'),
            ('update_php_field_names.php', '/Applications/XAMPP/xamppfiles/htdocs/migrations/update_php_field_names.php'),
            ('MIGRATION_PLAN.md', '/Applications/XAMPP/xamppfiles/htdocs/migrations/MIGRATION_PLAN.md')
        ]
        
        for remote_name, local_path in files_to_upload:
            if os.path.exists(local_path):
                with open(local_path, 'rb') as f:
                    ftp.storbinary(f'STOR {remote_name}', f)
                print(f"✅ Uploaded {remote_name}")
            else:
                print(f"⚠️  File not found: {local_path}")
        
        ftp.quit()
        
        print("\n🎉 Migration files uploaded!")
        print("📋 Migration plan: https://11klassniki.ru/migrations/MIGRATION_PLAN.md")
        print("🔧 PHP updater: https://11klassniki.ru/migrations/update_php_field_names.php")
        print("🗃️  SQL migration: https://11klassniki.ru/migrations/standardize_field_names.sql")
        
        print("\n⚠️  IMPORTANT: This is a major database change!")
        print("1. Read the migration plan carefully")
        print("2. Backup your database first")
        print("3. Test on staging environment if possible")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_migration_files()