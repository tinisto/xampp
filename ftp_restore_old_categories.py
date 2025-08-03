#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as file:
            ftp.storbinary(f'STOR {remote_file}', file)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

def main():
    print("🔄 RESTORING OLD CATEGORIES LIST...")
    
    files_to_upload = [
        # Fixed header with correct database query
        ('common-components/header-unified-simple-safe-v2.php', 
         'common-components/header-unified-simple-safe-v2.php'),
    ]
    
    try:
        # Connect to FTP
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully")
        
        # Change to 11klassnikiru directory
        try:
            ftp.cwd('11klassnikiru')
            print("✅ Changed to 11klassnikiru directory")
        except Exception as e:
            print(f"❌ Could not change to 11klassnikiru: {e}")
            return
        
        # Upload files
        success_count = 0
        
        print("\n📤 Uploading corrected categories...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n✅ OLD CATEGORIES LIST RESTORED!")
            print("\n🔧 What was fixed:")
            print("   • Wrong table name: 'category' → 'categories'")
            print("   • Wrong column names: 'id', 'category' → 'id_category', 'title_category'")
            print("   • Removed incorrect 'status = 1' condition")
            print("   • Fixed ORDER BY to use 'title_category ASC'")
            print("\n📋 Database query now:")
            print("   SELECT id_category, url_category, title_category")
            print("   FROM categories") 
            print("   WHERE id_category IN (2,3,5,7,8,9,10,11,12,13,14,15,18)")
            print("   ORDER BY title_category ASC")
            print("\n🎯 Category IDs restored: [2,3,5,7,8,9,10,11,12,13,14,15,18]")
            print("\n🌐 Test now: https://11klassniki.ru")
            print("   Click 'Категории' - should show the full original categories list!")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()