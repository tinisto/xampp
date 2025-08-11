#!/usr/bin/env python3
"""Deploy remaining updated files"""

import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# Remaining important files
REMAINING_FILES = [
    # Updated grid layouts
    'home_modern.php',
    'schools_modern.php',
    'vpo_modern.php',
    'spo_modern.php',
    
    # Single pages with fixes
    'post-single.php',
    'news-single.php',
    'school-single.php',
    'vpo-single.php',
    'spo-single.php',
    'event-single.php',
    
    # Other important pages
    'privacy_modern.php',
    'contacts.php',
    '404_modern.php',
    
    # SEO and health
    'seo-optimizer.php',
    'health-check.php',
    'sitemap.php'
]

print("Deploying remaining updates...\n")

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    print("✅ Connected\n")
    
    uploaded = 0
    for filepath in REMAINING_FILES:
        if os.path.exists(filepath):
            try:
                with open(filepath, 'rb') as f:
                    ftp.storbinary(f'STOR {filepath}', f)
                print(f"✅ {filepath}")
                uploaded += 1
            except Exception as e:
                print(f"❌ {filepath}: {e}")
        else:
            print(f"⚠️  {filepath} not found")
    
    ftp.quit()
    print(f"\n✅ Uploaded {uploaded} files!")
    
except Exception as e:
    print(f"❌ Error: {e}")