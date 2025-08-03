#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def deploy_migration():
    """Deploy migration script to live server"""
    
    print("🚀 Deploying Database Migration Script...")
    
    local_files = [
        '/Applications/XAMPP/xamppfiles/htdocs/migrate-database-data.php',
        '/Applications/XAMPP/xamppfiles/htdocs/migrate-simple.php'
    ]
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload migration script
        for local_path in local_files:
            if os.path.exists(local_path):
                filename = os.path.basename(local_path)
                print(f"📤 Uploading {filename}...")
                with open(local_path, 'rb') as f:
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {filename} uploaded!")
            else:
                print(f"❌ File not found: {local_path}")
        
        print("\n🎯 Run the migration:")
        print("https://11klassniki.ru/migrate-database-data.php")
        print("\n⚠️  IMPORTANT:")
        print("1. Make sure your .env file uses the new database credentials:")
        print("   DB_NAME=11klassniki_new")
        print("   DB_USER=admin_claude") 
        print("   DB_PASS=Secure9#Klass")
        print("2. Click 'CONFIRM MIGRATION' when ready")
        print("3. Wait for all tables to migrate")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

if __name__ == "__main__":
    deploy_migration()