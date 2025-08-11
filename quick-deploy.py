#!/usr/bin/env python3
"""
Quick deployment of essential updated files to 11klassniki.ru
"""

import ftplib
import os
import time

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# Essential files to upload (only the most critical updates)
ESSENTIAL_FILES = [
    # Core routing and configuration
    'index_modern.php',
    '.htaccess',
    'router.php',
    
    # Main pages with updates
    'home_modern.php',
    'posts_modern.php',
    'news_modern.php',
    'schools_modern.php',
    'vpo_modern.php',
    'spo_modern.php',
    'events.php',
    'search_modern.php',
    
    # Privacy and contact
    'privacy.php',
    'privacy_modern.php',
    'contact.php',
    'contacts.php',
    
    # Single pages
    'post-single.php',
    'news-single.php',
    'school-single.php',
    'vpo-single.php',
    'spo-single.php',
    'event-single.php',
    
    # Database connection
    'database/db_modern.php',
    'database/db_modern_mysql.php',
    
    # Template
    'real_template_local.php',
    
    # Tests
    'tests/automated-tests.php',
    
    # Other essential pages
    'login_modern.php',
    'register_modern.php',
    'profile_modern.php',
    'logout_modern.php',
    'favorites_modern.php',
    'notifications.php',
    'reading-lists.php',
    '404_modern.php'
]

def upload_file(ftp, local_path, remote_path):
    """Upload a single file"""
    try:
        # Create directory if needed
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            try:
                ftp.mkd(remote_dir)
            except:
                pass  # Directory might already exist
        
        # Upload file
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        
        size = os.path.getsize(local_path)
        print(f"✅ {remote_path} ({size:,} bytes)")
        return True
    except Exception as e:
        print(f"❌ {remote_path}: {str(e)}")
        return False

def main():
    print("""
╔══════════════════════════════════════════════════════╗
║        Quick Deploy - 11klassniki.ru                 ║
╚══════════════════════════════════════════════════════╝
    """)
    
    # Connect to FTP
    print(f"\n🔌 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    print("✅ Connected!\n")
    
    # Upload essential files
    uploaded = 0
    failed = 0
    
    for filepath in ESSENTIAL_FILES:
        if os.path.exists(filepath):
            if upload_file(ftp, filepath, filepath):
                uploaded += 1
            else:
                failed += 1
        else:
            print(f"⚠️  {filepath} not found locally")
    
    # Summary
    print(f"""
╔══════════════════════════════════════════════════════╗
║                    Summary                           ║
╠══════════════════════════════════════════════════════╣
║  ✅ Uploaded: {uploaded:>3} files                              ║
║  ❌ Failed:   {failed:>3} files                              ║
╚══════════════════════════════════════════════════════╝
    """)
    
    # Close connection
    ftp.quit()
    print("\n🔌 Disconnected")
    
    if failed == 0:
        print("\n🎉 Deployment successful!")
        print("🌐 Visit https://11klassniki.ru to verify")
    else:
        print("\n⚠️  Some files failed to upload")

if __name__ == "__main__":
    main()