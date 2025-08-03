#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def deploy_database_analysis():
    """Deploy database analysis script to live server"""
    
    print("🚀 Deploying Database Analysis Script...")
    
    local_path = '/Applications/XAMPP/xamppfiles/htdocs/database-analysis.php'
    
    if not os.path.exists(local_path):
        print(f"❌ File not found: {local_path}")
        return False
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload file
        print("📤 Uploading database-analysis.php...")
        with open(local_path, 'rb') as f:
            ftp.storbinary('STOR database-analysis.php', f)
        
        print("✅ Database analysis script deployed!")
        print("\n🎯 Run the analysis:")
        print("https://11klassniki.ru/database-analysis.php")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

if __name__ == "__main__":
    deploy_database_analysis()