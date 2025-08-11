#!/usr/bin/env python3
"""Deploy only the most critical updated files"""

import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# Only the most critical files that were updated
CRITICAL_FILES = [
    'posts_modern.php',
    'news_modern.php',
    'search_modern.php',
    'events.php',
    'privacy.php',
    'contact.php',
    'tests/automated-tests.php'
]

print("Deploying critical files...\n")

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    print("✅ Connected to server\n")
    
    # Upload each file
    for filepath in CRITICAL_FILES:
        if os.path.exists(filepath):
            try:
                # Handle directory
                if '/' in filepath:
                    directory = os.path.dirname(filepath)
                    try:
                        ftp.mkd(directory)
                    except:
                        pass
                
                # Upload
                with open(filepath, 'rb') as f:
                    ftp.storbinary(f'STOR {filepath}', f)
                print(f"✅ {filepath}")
                
            except Exception as e:
                print(f"❌ {filepath}: {e}")
        else:
            print(f"⚠️  {filepath} not found")
    
    # Close
    ftp.quit()
    print("\n✅ Deployment complete!")
    print("🌐 Check https://11klassniki.ru")
    
except Exception as e:
    print(f"❌ Error: {e}")