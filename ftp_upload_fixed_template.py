#!/usr/bin/env python3
"""
Upload the fixed template engine with proper theme script
"""

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("🚀 Uploading fixed template engine...")
    print(f"📡 Connecting to {FTP_HOST}...")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru/common-components')
    
    print("✅ Connected to FTP server")
    
    # Upload the fixed template engine
    with open('common-components/template-engine-ultimate.php', 'rb') as file:
        ftp.storbinary('STOR template-engine-ultimate.php', file)
        print("✅ Uploaded: template-engine-ultimate.php (with fixed theme script)")
    
    ftp.quit()
    print("\n🎉 Fixed template engine deployed!")
    
except Exception as e:
    print(f"❌ Upload failed: {str(e)}")