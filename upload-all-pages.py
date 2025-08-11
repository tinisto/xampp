#!/usr/bin/env python3
"""Upload all the page files that index_modern.php needs"""

import ftplib
import os
import glob

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# All the PHP files needed by index_modern.php
NEEDED_FILES = [
    'home_modern.php',
    'news_modern.php',
    'news-single.php',
    'posts_modern.php',
    'post-single.php',
    'vpo_modern.php',
    'vpo-single.php',
    'spo_modern.php',
    'spo-single.php',
    'schools_modern.php',
    'school-single.php',
    'search_modern.php',
    'login_modern.php',
    'register_modern.php',
    'welcome_modern.php',
    'logout_modern.php',
    'profile_modern.php',
    'favorites_modern.php',
    'settings_modern.php',
    'reading-lists.php',
    'reading-list-single.php',
    'notifications.php',
    'recommendations.php',
    'privacy_modern.php',
    'events.php',
    'event-single.php',
    '404_modern.php',
    'analytics.php',
    'sitemap.php',
    'rss.php',
    # API files
    'api_favorites.php',
    'api_comments.php',
    'api_rating.php',
    'api_reading_lists.php',
    'api_notifications.php',
    'api_events.php',
    'api_analytics.php',
    # Include files
    'includes/Cache.php',
    'includes/notifications.php',
    'includes/upload.php',
    'includes/recommendations.php',
    'includes/reading_list_widget.php',
    'includes/rating.php',
    'includes/comments.php',
    'includes/breadcrumbs.php',
    'includes/email.php',
    'includes/api_auth.php'
]

print("Uploading all required page files...\n")

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    print(f"✅ Connected to /{FTP_DIR}\n")
    
    uploaded = 0
    failed = 0
    
    # Upload files in batches
    for i, filepath in enumerate(NEEDED_FILES):
        if os.path.exists(filepath):
            try:
                # Create directory if needed
                if '/' in filepath:
                    directory = os.path.dirname(filepath)
                    try:
                        ftp.mkd(directory)
                    except:
                        pass
                
                # Upload file
                with open(filepath, 'rb') as f:
                    ftp.storbinary(f'STOR {filepath}', f)
                uploaded += 1
                
                # Show progress
                if uploaded % 5 == 0:
                    print(f"  ✅ Uploaded {uploaded} files...")
                    
                # Reconnect after every 20 files
                if uploaded % 20 == 0:
                    ftp.quit()
                    ftp = ftplib.FTP(FTP_HOST, timeout=30)
                    ftp.login(FTP_USER, FTP_PASS)
                    ftp.cwd(FTP_DIR)
                    
            except Exception as e:
                print(f"  ❌ {filepath}: {e}")
                failed += 1
        else:
            print(f"  ⚠️  {filepath} not found locally")
    
    # Upload the template file if it exists
    if os.path.exists('real_template_local.php'):
        try:
            with open('real_template_local.php', 'rb') as f:
                ftp.storbinary('STOR real_template_local.php', f)
            print("  ✅ Template file uploaded")
            uploaded += 1
        except Exception as e:
            print(f"  ❌ Template: {e}")
    
    ftp.quit()
    
    print(f"""
╔══════════════════════════════════════════════════════╗
║                 Upload Complete!                      ║
╠══════════════════════════════════════════════════════╣
║  ✅ Uploaded: {uploaded:>3} files                              ║
║  ❌ Failed:   {failed:>3} files                              ║
╚══════════════════════════════════════════════════════╝

🎉 Site files deployed!
🌐 Your site should now work at: https://11klassniki.ru
    """)
    
except Exception as e:
    print(f"❌ Error: {e}")