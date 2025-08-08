#!/usr/bin/env python3
"""Upload debug files"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Upload debug files
    files = ['index-debug.php', 'index-minimal.php']
    
    for filename in files:
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR ' + filename, f)
        print(f"✓ Uploaded {filename}")
    
    # Also replace index.php with minimal version for now
    print("\nReplacing index.php with minimal version...")
    with open('index-minimal.php', 'rb') as f:
        ftp.storbinary('STOR index.php', f)
    print("✓ index.php replaced with minimal version")
    
    ftp.quit()
    
    print("\n" + "="*50)
    print("Test these URLs:")
    print("1. https://11klassniki.ru/index-debug.php (debug info)")
    print("2. https://11klassniki.ru/index-minimal.php (minimal homepage)")
    print("3. https://11klassniki.ru/ (should now show minimal homepage)")
    print("="*50)
    
except Exception as e:
    print(f"Error: {e}")