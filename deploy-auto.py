#!/usr/bin/env python3
"""
Automated deployment script for 11klassniki.ru
Uploads all necessary files without user interaction
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
    'migration_output.html',
    'fill_content_output.html',
    'test_homepage.html',
    'homepage_test.html',
    'run-comments-migration.php',
    'setup-migration.php'
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
                current_dir = ''
                for dir_part in dirs:
                    if dir_part:
                        current_dir = current_dir + '/' + dir_part if current_dir else dir_part
                        try:
                            self.ftp.cwd('/' + FTP_DIR + '/' + current_dir)
                            self.ftp.cwd('/' + FTP_DIR)
                        except:
                            self.create_remote_dir(current_dir)
            
            # Upload file
            with open(local_path, 'rb') as f:
                self.ftp.storbinary(f'STOR {remote_path}', f)
            
            file_size = os.path.getsize(local_path)
            print(f"✅ Uploaded: {remote_path} ({file_size:,} bytes)")
            self.uploaded_count += 1
            return True
            
        except Exception as e:
            print(f"❌ Failed to upload {remote_path}: {e}")
            self.failed_count += 1
            return False
            
    def upload_all_php_files(self):
        """Upload all PHP files and essential directories"""
        print("\n📤 Starting automated deployment...")
        
        # Get all PHP files in root directory
        for filename in os.listdir('.'):
            if filename.endswith('.php') and not self.should_exclude(filename):
                if os.path.isfile(filename):
                    self.upload_file(filename, filename)
        
        # Upload essential directories
        essential_dirs = ['api', 'admin', 'includes', 'database', 'tests', 'assets', 'css', 'js', 'images']
        
        for dir_name in essential_dirs:
            if os.path.isdir(dir_name):
                print(f"\n📁 Uploading directory: {dir_name}/")
                self.upload_directory(dir_name)
                
        # Upload .htaccess
        if os.path.isfile('.htaccess'):
            self.upload_file('.htaccess', '.htaccess')
            
    def upload_directory(self, local_dir, remote_base=''):
        """Recursively upload directory"""
        for root, dirs, files in os.walk(local_dir):
            # Skip excluded directories
            dirs[:] = [d for d in dirs if not self.should_exclude(os.path.join(root, d))]
            
            for filename in files:
                local_path = os.path.join(root, filename)
                
                # Skip excluded files
                if self.should_exclude(local_path):
                    self.skipped_count += 1
                    continue
                    
                # Calculate remote path
                rel_path = os.path.relpath(local_path, '.')
                remote_path = rel_path.replace('\\', '/')
                
                # Upload file
                self.upload_file(local_path, remote_path)
                
                # Small delay to avoid overwhelming server
                time.sleep(0.1)
                
    def verify_critical_files(self):
        """Verify critical files exist on server"""
        print("\n🔍 Verifying critical files...")
        
        critical_files = [
            'index_modern.php',
            'database/db_modern.php',
            '.htaccess',
            'router.php',
            'home_modern.php',
            'posts_modern.php',
            'news_modern.php'
        ]
        
        verified = 0
        for filepath in critical_files:
            try:
                size = self.ftp.size(filepath)
                if size > 0:
                    print(f"✅ Verified: {filepath} ({size:,} bytes)")
                    verified += 1
                else:
                    print(f"❌ Empty file: {filepath}")
            except:
                print(f"❌ Missing: {filepath}")
                
        return verified == len(critical_files)
        
    def close(self):
        """Close FTP connection"""
        if self.ftp:
            self.ftp.quit()
            print("\n🔌 Disconnected from server")

def main():
    print("""
╔══════════════════════════════════════════════════════╗
║  11klassniki.ru Automated Production Deployment       ║
╚══════════════════════════════════════════════════════╝
    """)
    
    # Change to script directory
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    
    # Start deployment
    start_time = time.time()
    uploader = FTPUploader()
    
    try:
        # Connect to server
        uploader.connect()
        
        # Upload all PHP files and directories
        uploader.upload_all_php_files()
        
        # Verify critical files
        success = uploader.verify_critical_files()
        
        # Show summary
        elapsed = time.time() - start_time
        print(f"""
╔══════════════════════════════════════════════════════╗
║                 Deployment Summary                    ║
╠══════════════════════════════════════════════════════╣
║  ✅ Uploaded:  {uploader.uploaded_count:>5} files                         ║
║  ⏭️  Skipped:  {uploader.skipped_count:>5} files (excluded)             ║
║  ❌ Failed:    {uploader.failed_count:>5} files                         ║
║  ⏱️  Time:     {elapsed:>5.1f} seconds                      ║
╚══════════════════════════════════════════════════════╝
        """)
        
        if success and uploader.failed_count == 0:
            print("🎉 Deployment completed successfully!")
            print("\n🌐 Your site is now live at: https://11klassniki.ru")
            print("\n📊 Next steps:")
            print("   1. Visit https://11klassniki.ru to verify deployment")
            print("   2. Run tests at https://11klassniki.ru/tests/automated-tests.php")
            print("   3. Check all major pages are working correctly")
        else:
            print("⚠️  Deployment completed with issues. Please check the output above.")
            
    except Exception as e:
        print(f"\n❌ Deployment failed: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
        
    finally:
        uploader.close()

if __name__ == "__main__":
    main()