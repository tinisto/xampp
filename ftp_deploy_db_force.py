#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def deploy_db_force():
    """Deploy database force fix"""
    
    print("📤 Deploying database force fix...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload updated db_connections.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/database/db_connections.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR database/db_connections.php', f)
            print("✅ database/db_connections.php updated!")
        
        # Upload override file
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/override_db_connection.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR override_db_connection.php', f)
            print("✅ override_db_connection.php uploaded!")
        
        ftp.quit()
        
        print("\n🎯 Force Fix Deployed!")
        print("This bypasses the cached .env values and forces the new database.")
        print("\n🧪 Test immediately:")
        print("1. https://11klassniki.ru/test_new_structure.php")
        print("2. Should now show: Current database: 11klassniki_claude")
        print("3. Universities and colleges tables should be found")
        
        print("\n📝 Note:")
        print("This is a temporary fix. Once PHP cache clears naturally,")
        print("you can revert by setting $force_new_db = false in db_connections.php")
        
    except Exception as e:
        print(f"❌ Deploy failed: {e}")

if __name__ == "__main__":
    deploy_db_force()