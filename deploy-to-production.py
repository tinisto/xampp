#!/usr/bin/env python3
"""
Deploy 11klassniki.ru site to production server
This script uploads all necessary files while excluding local-only configurations
"""

import ftplib
import os
import sys
from pathlib import Path
import time

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

# Files and directories to exclude from upload
EXCLUDE_PATTERNS = [
    '.git',
    '.gitignore',
    '*.pyc',
    '__pycache__',
    '.DS_Store',
    'config/local-config.php',
    'config/database.local.php',
    '.env',
    '*.sh',
    '*.py',
    'CLAUDE_SESSION.md',
    'README.md',
    '*.sql',
    'test-*.php',
    'import_*.php',
    'fill_*.php',
    'populate_*.php',
    'clean_*.php',
    'database/local.sqlite',
    'database/db_connections_local.php',
    'index_local.php',
    'real_template_local.php',
    'news_local.php',
    'auto-save-session.sh',
    'cleanup-mysql.sh',
    'import-no-fk.sh',
    'run-comments-migration.php',
    'setup-migration.php',
    'DEPLOYMENT.md',
    'IMPORT_YOUR_DATA.md',
    'delete-all-server-files.py',
    'download-database.py',
    'emergency-restore.py',
    'test-upload.py',
    'upload-*.py',
    'migration_output.html',
    'fill_content_output.html',
    'test_homepage.html',
    'homepage_test.html'
]

# Files that should be uploaded (whitelist approach for critical files)
REQUIRED_FILES = [
    'index_modern.php',
    'home_modern.php',
    'posts_modern.php',
    'news_modern.php',
    'schools_modern.php',
    'vpo_modern.php',
    'spo_modern.php',
    'events.php',
    'search_modern.php',
    'privacy_modern.php',
    'privacy.php',
    'contact.php',
    'contacts.php',
    'login_modern.php',
    'register_modern.php',
    'profile_modern.php',
    'settings_modern.php',
    'favorites_modern.php',
    'logout_modern.php',
    'welcome_modern.php',
    'post-single.php',
    'news-single.php',
    'school-single.php',
    'vpo-single.php',
    'spo-single.php',
    'event-single.php',
    'reading-lists.php',
    'reading-list-single.php',
    'recommendations.php',
    'notifications.php',
    'search_advanced.php',
    'database/db_modern.php',
    'database/db_modern_mysql.php',
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
    'api/v1/',
    'admin/',
    'router.php',
    '.htaccess',
    'sitemap.php',
    'rss.php',
    'health-check.php',
    'seo-optimizer.php',
    'analytics.php',
    'api_analytics.php',
    'api_comments.php',
    'api_events.php',
    'api_favorites.php',
    'api_notifications.php',
    'api_rating.php',
    'api_reading_lists.php',
    'tests/automated-tests.php',
    '404_modern.php',
    'dashboard-overview.php',
    'content-showcase.php'
]

class FTPUploader:
    def __init__(self):
        self.ftp = None
        self.uploaded_count = 0
        self.failed_count = 0
        self.skipped_count = 0
        
    def connect(self):
        """Connect to FTP server"""
        print(f"🔌 Connecting to {FTP_HOST}...")
        self.ftp = ftplib.FTP(FTP_HOST)
        self.ftp.login(FTP_USER, FTP_PASS)
        self.ftp.cwd(FTP_DIR)
        print(f"✅ Connected successfully!")
        
    def should_exclude(self, filepath):
        """Check if file should be excluded"""
        filename = os.path.basename(filepath)
        
        # Check exclude patterns
        for pattern in EXCLUDE_PATTERNS:
            if pattern.startswith('*'):
                if filename.endswith(pattern[1:]):
                    return True
            elif pattern in filepath:
                return True
                
        return False
        
    def create_remote_dir(self, dirname):
        """Create directory on remote server if it doesn't exist"""
        try:
            self.ftp.mkd(dirname)
            print(f"📁 Created directory: {dirname}")
        except ftplib.error_perm as e:
            if "550" not in str(e):  # 550 = directory already exists
                print(f"❌ Error creating directory {dirname}: {e}")
                
    def upload_file(self, local_path, remote_path):
        """Upload a single file"""
        try:
            # Ensure remote directory exists
            remote_dir = os.path.dirname(remote_path)
            if remote_dir:
                # Create nested directories
                dirs = remote_dir.split('/')
                for i in range(len(dirs)):
                    if dirs[i]:
                        partial_dir = '/'.join(dirs[:i+1])
                        self.create_remote_dir(partial_dir)
            
            # Upload file
            with open(local_path, 'rb') as f:
                self.ftp.storbinary(f'STOR {remote_path}', f)
            
            print(f"✅ Uploaded: {remote_path}")
            self.uploaded_count += 1
            return True
            
        except Exception as e:
            print(f"❌ Failed to upload {remote_path}: {e}")
            self.failed_count += 1
            return False
            
    def upload_directory(self, local_dir, remote_dir=''):
        """Recursively upload directory"""
        for root, dirs, files in os.walk(local_dir):
            # Skip excluded directories
            dirs[:] = [d for d in dirs if not self.should_exclude(d)]
            
            for filename in files:
                local_path = os.path.join(root, filename)
                
                # Skip excluded files
                if self.should_exclude(local_path):
                    self.skipped_count += 1
                    continue
                    
                # Calculate remote path
                rel_path = os.path.relpath(local_path, local_dir)
                remote_path = os.path.join(remote_dir, rel_path).replace('\\', '/')
                
                # Upload file
                self.upload_file(local_path, remote_path)
                
    def upload_required_files(self):
        """Upload only required files"""
        print("\n📤 Uploading required files...")
        
        for file_pattern in REQUIRED_FILES:
            if file_pattern.endswith('/'):
                # It's a directory
                if os.path.isdir(file_pattern):
                    print(f"\n📁 Uploading directory: {file_pattern}")
                    self.upload_directory(file_pattern, file_pattern)
            else:
                # It's a file or pattern
                if '*' in file_pattern:
                    # Handle wildcards
                    base_dir = os.path.dirname(file_pattern)
                    pattern = os.path.basename(file_pattern)
                    if os.path.isdir(base_dir):
                        for filename in os.listdir(base_dir):
                            if filename.startswith(pattern.replace('*', '')):
                                local_path = os.path.join(base_dir, filename)
                                if os.path.isfile(local_path):
                                    self.upload_file(local_path, local_path)
                else:
                    # Single file
                    if os.path.isfile(file_pattern):
                        self.upload_file(file_pattern, file_pattern)
                    else:
                        print(f"⚠️  File not found: {file_pattern}")
                        
    def verify_critical_files(self):
        """Verify critical files exist on server"""
        print("\n🔍 Verifying critical files...")
        
        critical_files = [
            'index_modern.php',
            'database/db_modern.php',
            '.htaccess',
            'router.php'
        ]
        
        verified = 0
        for filepath in critical_files:
            try:
                size = self.ftp.size(filepath)
                if size > 0:
                    print(f"✅ Verified: {filepath} ({size} bytes)")
                    verified += 1
                else:
                    print(f"❌ Empty file: {filepath}")
            except:
                print(f"❌ Missing: {filepath}")
                
        print(f"\n✅ Verified {verified}/{len(critical_files)} critical files")
        
    def close(self):
        """Close FTP connection"""
        if self.ftp:
            self.ftp.quit()
            print("\n🔌 Disconnected from server")

def main():
    print("""
╔══════════════════════════════════════════════════════╗
║     11klassniki.ru Production Deployment Script       ║
╚══════════════════════════════════════════════════════╝
    """)
    
    # Change to script directory
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    
    # Confirm deployment
    print("⚠️  This will upload files to the PRODUCTION server!")
    response = input("\nContinue with deployment? (yes/no): ")
    
    if response.lower() != 'yes':
        print("❌ Deployment cancelled")
        return
        
    # Start deployment
    start_time = time.time()
    uploader = FTPUploader()
    
    try:
        # Connect to server
        uploader.connect()
        
        # Upload files
        uploader.upload_required_files()
        
        # Verify critical files
        uploader.verify_critical_files()
        
        # Show summary
        elapsed = time.time() - start_time
        print(f"""
╔══════════════════════════════════════════════════════╗
║                 Deployment Summary                    ║
╠══════════════════════════════════════════════════════╣
║  ✅ Uploaded:  {uploader.uploaded_count:>5} files                         ║
║  ⏭️  Skipped:  {uploader.skipped_count:>5} files (local-only)           ║
║  ❌ Failed:    {uploader.failed_count:>5} files                         ║
║  ⏱️  Time:     {elapsed:>5.1f} seconds                      ║
╚══════════════════════════════════════════════════════╝
        """)
        
        if uploader.failed_count == 0:
            print("🎉 Deployment completed successfully!")
            print("\n🌐 Your site is now live at: https://11klassniki.ru")
        else:
            print("⚠️  Deployment completed with errors. Please check failed files.")
            
    except Exception as e:
        print(f"\n❌ Deployment failed: {e}")
        sys.exit(1)
        
    finally:
        uploader.close()

if __name__ == "__main__":
    main()