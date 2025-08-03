#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_claude_db_check():
    """Upload claude database check script"""
    
    print("📤 Uploading claude database check script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload check_claude_db_status.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/check_claude_db_status.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR check_claude_db_status.php', f)
            print("✅ check_claude_db_status.php uploaded!")
        else:
            print("❌ check_claude_db_status.php not found locally")
        
        ftp.quit()
        print("\n🔗 Check status at: https://11klassniki.ru/check_claude_db_status.php")
        print("\nThis will show:")
        print("- Tables in 11klassniki_claude database")
        print("- Record counts for each table")
        print("- Verification that clean structure is used")
        print("- Overall migration status")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_claude_db_check()