#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 RESTORING ORIGINAL WORKING CONNECTION")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Go to database directory
        ftp.cwd('database')
        
        # First, backup the current broken one
        print("\n1️⃣ Backing up current connection file...")
        try:
            ftp.rename('db_connections.php', 'db_connections_my_broken.php')
            print("   ✅ Backed up my broken version")
        except:
            print("   ⚠️  Could not backup current file")
        
        # Restore the original
        print("\n2️⃣ Restoring original working connection...")
        try:
            ftp.rename('db_connections_broken.php', 'db_connections.php')
            print("   ✅ RESTORED ORIGINAL WORKING CONNECTION!")
        except Exception as e:
            print(f"   ❌ Could not restore: {e}")
            
            # Try copying instead
            print("   Trying to copy instead...")
            
            # Download the working file
            with tempfile.NamedTemporaryFile(delete=False) as tmp:
                tmp_path = tmp.name
            
            ftp.retrbinary('RETR db_connections_broken.php', open(tmp_path, 'wb').write)
            
            # Upload as db_connections.php
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR db_connections.php', file)
            
            os.unlink(tmp_path)
            print("   ✅ Copied working connection to db_connections.php")
        
        ftp.cwd('..')
        ftp.quit()
        
        print("\n✅ ORIGINAL CONNECTION RESTORED!")
        print("\n🎯 Your site should be working again now!")
        print("\nCheck:")
        print("• Homepage: https://11klassniki.ru/")
        print("• News: https://11klassniki.ru/news")
        print("• VPO: https://11klassniki.ru/vpo-all-regions")
        
        print("\n📝 What I did:")
        print("1. Found your original working db_connections.php (I had renamed it to db_connections_broken.php)")
        print("2. Restored it as the main db_connections.php")
        print("3. Your database connection should be working like before!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()