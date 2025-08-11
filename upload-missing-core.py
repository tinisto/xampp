#!/usr/bin/env python3
"""Upload the missing core files that are preventing the site from loading"""

import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# CRITICAL files that MUST be uploaded
CORE_FILES = [
    'index_modern.php',      # Main entry point
    'index.php',            # Fallback entry
    'router.php',           # URL routing
    '.htaccess',            # Apache config
    'home_modern.php',      # Homepage
    'database/db_modern.php', # Database connection
    'database/db_modern_mysql.php',
    'config/loadEnv.php'    # Configuration loader
]

print("Uploading MISSING CORE files...\n")

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    print(f"✅ Connected to /{FTP_DIR}\n")
    
    # Upload each core file
    for filepath in CORE_FILES:
        if os.path.exists(filepath):
            try:
                # Upload the file
                with open(filepath, 'rb') as f:
                    ftp.storbinary(f'STOR {filepath}', f)
                
                # Verify it was uploaded
                size = ftp.size(filepath)
                print(f"✅ {filepath} - uploaded ({size} bytes)")
                
            except Exception as e:
                print(f"❌ {filepath} - {e}")
        else:
            # Try alternate names
            if filepath == 'index.php' and os.path.exists('index_modern.php'):
                # Create index.php as copy of index_modern.php
                with open('index_modern.php', 'rb') as f:
                    ftp.storbinary('STOR index.php', f)
                print(f"✅ index.php - created from index_modern.php")
            else:
                print(f"⚠️  {filepath} - not found locally")
    
    # Close
    ftp.quit()
    
    print("""
✅ Core files uploaded!
🌐 The site should now load at: https://11klassniki.ru

If still empty, the issue might be:
- Database connection settings
- PHP version compatibility
- File permissions
    """)
    
except Exception as e:
    print(f"❌ Error: {e}")