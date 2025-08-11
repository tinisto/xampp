#!/usr/bin/env python3
"""
Deploy essential updated files to 11klassniki.ru
Uploads files in batches with proper error handling
"""

import ftplib
import os
import socket
import time

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# Essential files to upload (grouped by priority)
PRIORITY_FILES = [
    # Group 1: Core files
    [
        'index_modern.php',
        '.htaccess',
        'router.php',
        'database/db_modern.php',
        'database/db_modern_mysql.php'
    ],
    
    # Group 2: Main pages
    [
        'home_modern.php',
        'posts_modern.php',
        'news_modern.php',
        'schools_modern.php',
        'vpo_modern.php',
        'spo_modern.php',
        'events.php',
        'search_modern.php'
    ],
    
    # Group 3: Single pages
    [
        'post-single.php',
        'news-single.php',
        'school-single.php',
        'vpo-single.php',
        'spo-single.php',
        'event-single.php'
    ],
    
    # Group 4: User pages
    [
        'login_modern.php',
        'register_modern.php',
        'profile_modern.php',
        'logout_modern.php',
        'favorites_modern.php',
        'notifications.php',
        'reading-lists.php',
        'reading-list-single.php'
    ],
    
    # Group 5: Other pages
    [
        'privacy.php',
        'privacy_modern.php',
        'contact.php',
        'contacts.php',
        '404_modern.php',
        'recommendations.php',
        'content-showcase.php',
        'dashboard-overview.php'
    ],
    
    # Group 6: API and tests
    [
        'tests/automated-tests.php',
        'seo-optimizer.php',
        'health-check.php',
        'sitemap.php',
        'rss.php'
    ]
]

class FTPUploader:
    def __init__(self):
        self.ftp = None
        self.uploaded = 0
        self.failed = 0
        self.skipped = 0
        
    def connect(self):
        """Establish FTP connection"""
        socket.setdefaulttimeout(30)
        self.ftp = ftplib.FTP(FTP_HOST)
        self.ftp.login(FTP_USER, FTP_PASS)
        self.ftp.cwd(FTP_DIR)
        self.ftp.set_pasv(True)
        
    def ensure_dir(self, directory):
        """Create directory if it doesn't exist"""
        if not directory:
            return
            
        try:
            self.ftp.cwd(directory)
            self.ftp.cwd('/' + FTP_DIR)  # Go back to root
        except:
            try:
                self.ftp.mkd(directory)
                print(f"  📁 Created directory: {directory}")
            except:
                pass
                
    def upload_file(self, filepath):
        """Upload a single file"""
        if not os.path.exists(filepath):
            print(f"  ⚠️  Skipped: {filepath} (not found)")
            self.skipped += 1
            return False
            
        try:
            # Ensure directory exists
            directory = os.path.dirname(filepath)
            if directory:
                self.ensure_dir(directory)
                
            # Upload file
            with open(filepath, 'rb') as f:
                self.ftp.storbinary(f'STOR {filepath}', f)
                
            size = os.path.getsize(filepath)
            print(f"  ✅ {filepath} ({size:,} bytes)")
            self.uploaded += 1
            return True
            
        except Exception as e:
            print(f"  ❌ {filepath}: {str(e)}")
            self.failed += 1
            return False
            
    def upload_group(self, files, group_name):
        """Upload a group of files"""
        print(f"\n📦 {group_name}:")
        for filepath in files:
            self.upload_file(filepath)
            time.sleep(0.1)  # Small delay to avoid overwhelming server

def main():
    print("""
╔══════════════════════════════════════════════════════╗
║      Essential Files Deployment - 11klassniki.ru     ║
╚══════════════════════════════════════════════════════╝
    """)
    
    uploader = FTPUploader()
    
    try:
        # Connect to FTP
        print("\n🔌 Connecting to server...")
        uploader.connect()
        print("✅ Connected successfully!\n")
        
        # Upload files by priority groups
        group_names = [
            "Core System Files",
            "Main Pages",
            "Single Content Pages",
            "User Account Pages",
            "Other Pages",
            "API and Tests"
        ]
        
        for i, (files, name) in enumerate(zip(PRIORITY_FILES, group_names)):
            uploader.upload_group(files, name)
            
            # Reconnect every 2 groups to avoid timeout
            if i % 2 == 1 and i < len(PRIORITY_FILES) - 1:
                print("\n🔄 Reconnecting...")
                uploader.ftp.quit()
                uploader.connect()
                
        # Final summary
        print(f"""
╔══════════════════════════════════════════════════════╗
║                  Deployment Summary                   ║
╠══════════════════════════════════════════════════════╣
║  ✅ Uploaded: {uploader.uploaded:>3} files                              ║
║  ❌ Failed:   {uploader.failed:>3} files                              ║
║  ⚠️  Skipped:  {uploader.skipped:>3} files (not found)                ║
╚══════════════════════════════════════════════════════╝
        """)
        
        if uploader.failed == 0:
            print("\n🎉 All essential files deployed successfully!")
            print("\n📋 Next steps:")
            print("1. Visit https://11klassniki.ru to verify the site")
            print("2. Test the search functionality")
            print("3. Check privacy policy and contact pages")
            print("4. Run tests at /tests/automated-tests.php")
        else:
            print("\n⚠️  Some files failed to upload. Please check the errors above.")
            
    except Exception as e:
        print(f"\n❌ Deployment error: {e}")
        
    finally:
        if uploader.ftp:
            try:
                uploader.ftp.quit()
                print("\n🔌 Disconnected from server")
            except:
                pass

if __name__ == "__main__":
    main()