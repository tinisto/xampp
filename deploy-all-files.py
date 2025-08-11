#!/usr/bin/env python3
"""
Deploy ALL files to 11klassnikiru folder on production server
This uploads everything except local-only files
"""

import ftplib
import os
import time

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'  # THIS IS THE TARGET FOLDER!

# Files to exclude
EXCLUDE_PATTERNS = [
    '.git',
    '.DS_Store',
    '*.py',
    '*.sh',
    '*.sql',
    'CLAUDE_SESSION.md',
    'config/local-config.php',
    'config/database.local.php',
    '.env',
    'database/local.sqlite',
    'test-*.php',
    'import_*.php',
    'fill_*.php',
    'populate_*.php',
    'clean_*.php',
    'real_template_local.php',
    'index_local.php',
    'news_local.php',
    '*.html'
]

class FTPUploader:
    def __init__(self):
        self.ftp = None
        self.uploaded = 0
        self.failed = 0
        
    def connect(self):
        """Connect to FTP server"""
        print(f"🔌 Connecting to {FTP_HOST}...")
        self.ftp = ftplib.FTP(FTP_HOST, timeout=30)
        self.ftp.login(FTP_USER, FTP_PASS)
        self.ftp.cwd(FTP_DIR)  # Change to 11klassnikiru folder
        print(f"✅ Connected and changed to /{FTP_DIR} folder\n")
        
    def should_exclude(self, filepath):
        """Check if file should be excluded"""
        for pattern in EXCLUDE_PATTERNS:
            if pattern.startswith('*'):
                if filepath.endswith(pattern[1:]):
                    return True
            elif pattern in filepath:
                return True
        return False
        
    def create_dirs(self, path):
        """Create directory structure"""
        dirs = path.split('/')
        current = ''
        for d in dirs[:-1]:
            if d:
                current = current + '/' + d if current else d
                try:
                    self.ftp.mkd(current)
                except:
                    pass
                    
    def upload_file(self, local_path):
        """Upload a single file"""
        if self.should_exclude(local_path):
            return False
            
        try:
            # Create directory structure
            if '/' in local_path:
                self.create_dirs(local_path)
                
            # Upload file
            with open(local_path, 'rb') as f:
                self.ftp.storbinary(f'STOR {local_path}', f)
            
            self.uploaded += 1
            if self.uploaded % 10 == 0:
                print(f"  Uploaded {self.uploaded} files...")
            return True
            
        except Exception as e:
            print(f"  ❌ Failed: {local_path} - {str(e)}")
            self.failed += 1
            return False
            
    def upload_directory(self, start_path='.'):
        """Upload entire directory"""
        for root, dirs, files in os.walk(start_path):
            # Skip excluded directories
            dirs[:] = [d for d in dirs if not self.should_exclude(d)]
            
            for filename in files:
                if filename.startswith('.'):
                    continue
                    
                local_path = os.path.join(root, filename)
                if start_path == '.':
                    remote_path = local_path[2:] if local_path.startswith('./') else local_path
                else:
                    remote_path = os.path.relpath(local_path, start_path)
                    
                remote_path = remote_path.replace('\\', '/')
                
                if os.path.isfile(local_path):
                    self.upload_file(remote_path)
                    
            # Reconnect every 50 files to avoid timeout
            if self.uploaded % 50 == 0 and self.uploaded > 0:
                print(f"\n🔄 Reconnecting after {self.uploaded} files...")
                self.ftp.quit()
                time.sleep(1)
                self.connect()

def main():
    print("""
╔══════════════════════════════════════════════════════╗
║      FULL Site Deployment to /11klassnikiru          ║
╚══════════════════════════════════════════════════════╝
    """)
    
    # Change to htdocs directory
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    
    uploader = FTPUploader()
    
    try:
        # Connect
        uploader.connect()
        
        print("📤 Uploading ALL files to /11klassnikiru folder...\n")
        print("This will take several minutes. Progress shown every 10 files.\n")
        
        # Upload everything
        uploader.upload_directory('.')
        
        # Summary
        print(f"""
╔══════════════════════════════════════════════════════╗
║                 Deployment Complete!                  ║
╠══════════════════════════════════════════════════════╣
║  ✅ Uploaded: {uploader.uploaded:>4} files                            ║
║  ❌ Failed:   {uploader.failed:>4} files                            ║
║  📁 Target:   /{FTP_DIR}                      ║
╚══════════════════════════════════════════════════════╝
        """)
        
        if uploader.failed == 0:
            print("\n🎉 ALL files deployed successfully!")
            print(f"🌐 Your site is live at: https://11klassniki.ru")
            print("\n✅ The site should now display correctly!")
        else:
            print("\n⚠️  Some files failed. The site may still work.")
            
    except Exception as e:
        print(f"\n❌ Error: {e}")
        
    finally:
        if uploader.ftp:
            try:
                uploader.ftp.quit()
                print("\n🔌 Disconnected")
            except:
                pass

if __name__ == "__main__":
    main()