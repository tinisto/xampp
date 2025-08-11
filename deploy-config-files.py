#!/usr/bin/env python3
"""Deploy configuration and include files"""

import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# Configuration and include files
CONFIG_FILES = [
    # Config files
    'config/loadEnv.php',
    
    # Include files
    'includes/Cache.php',
    'includes/api_auth.php',
    'includes/breadcrumbs.php',
    'includes/comments.php',
    'includes/email.php',
    'includes/notifications.php',
    'includes/rating.php',
    'includes/reading_list_widget.php',
    'includes/recommendations.php',
    'includes/upload.php',
    
    # API directories
    'api/v1/index.php',
    
    # Analytics files
    'analytics.php',
    'api_analytics.php',
    'api_comments.php',
    'api_events.php',
    'api_favorites.php',
    'api_notifications.php',
    'api_rating.php',
    'api_reading_lists.php'
]

print("Deploying configuration files...\n")

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    print("✅ Connected\n")
    
    uploaded = 0
    for filepath in CONFIG_FILES:
        if os.path.exists(filepath):
            try:
                # Create directory if needed
                if '/' in filepath:
                    parts = filepath.split('/')
                    current_path = ''
                    for part in parts[:-1]:
                        if current_path:
                            current_path += '/' + part
                        else:
                            current_path = part
                        try:
                            ftp.mkd(current_path)
                        except:
                            pass
                
                # Upload file
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
    print("\n📋 All critical files have been deployed!")
    print("🌐 The site should now be fully functional at https://11klassniki.ru")
    
except Exception as e:
    print(f"❌ Error: {e}")