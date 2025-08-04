#!/usr/bin/env python3
"""
Production Deployment Script for 11klassniki
Deploys all improvements to the live server
"""

import ftplib
import os
import sys
from pathlib import Path
import mimetypes
from datetime import datetime

# FTP Configuration
FTP_HOST = "185.46.8.204"
FTP_USER = "u2666700"
FTP_PASS = "19Dima08Dima08"
FTP_PATH = "/domains/11klassniki.ru/public_html/"

# Local paths
LOCAL_BASE = "/Applications/XAMPP/xamppfiles/htdocs"

# Files and directories to upload
UPLOAD_ITEMS = [
    # Core security and performance improvements
    "includes/security/",
    "includes/monitoring/", 
    "includes/performance/",
    "includes/cache/",
    "includes/utils/",
    "includes/database/migration_manager.php",
    "includes/comments/comment_enhancements.php",
    
    # Database migrations
    "database/migrations/",
    "database/migrate.php",
    
    # API endpoints
    "api/comments.php",
    
    # Admin tools
    "admin/cache-management.php",
    "admin/monitoring.php",
    
    # Assets
    "css/enhanced-comments.css",
    "js/enhanced-comments.js",
    "build/assets/",
    
    # Minified assets
    "css/*.min.css",
    "js/*.min.js",
    
    # Build and development tools
    "build/minify-assets.php",
    "scripts/setup-dev-environment.php",
    
    # Configuration updates
    "Makefile",
    
    # Tests
    "tests/",
    "phpunit.xml",
    
    # Documentation would go here if created
]

class DeploymentManager:
    def __init__(self):
        self.ftp = None
        self.uploaded_files = []
        self.errors = []
        
    def connect(self):
        """Connect to FTP server"""
        try:
            print(f"🔌 Connecting to {FTP_HOST}...")
            self.ftp = ftplib.FTP(FTP_HOST)
            self.ftp.login(FTP_USER, FTP_PASS)
            self.ftp.cwd(FTP_PATH)
            print("✅ Connected successfully")
            return True
        except Exception as e:
            print(f"❌ Connection failed: {e}")
            return False
    
    def create_remote_dir(self, remote_path):
        """Create directory on remote server if it doesn't exist"""
        try:
            # Try to change to the directory
            self.ftp.cwd(FTP_PATH + remote_path)
            self.ftp.cwd(FTP_PATH)  # Go back to root
            return True
        except:
            # Directory doesn't exist, create it
            try:
                parts = remote_path.strip('/').split('/')
                current_path = ""
                
                for part in parts:
                    current_path += part + "/"
                    try:
                        self.ftp.cwd(FTP_PATH + current_path)
                    except:
                        self.ftp.mkd(FTP_PATH + current_path)
                        print(f"📁 Created directory: {current_path}")
                
                self.ftp.cwd(FTP_PATH)  # Go back to root
                return True
            except Exception as e:
                print(f"❌ Failed to create directory {remote_path}: {e}")
                return False
    
    def upload_file(self, local_path, remote_path):
        """Upload a single file"""
        try:
            # Create remote directory if needed
            remote_dir = os.path.dirname(remote_path)
            if remote_dir:
                self.create_remote_dir(remote_dir)
            
            # Determine transfer mode
            mime_type, _ = mimetypes.guess_type(local_path)
            is_binary = mime_type and (
                mime_type.startswith('image/') or 
                mime_type.startswith('video/') or
                mime_type.startswith('audio/') or
                mime_type in ['application/pdf', 'application/zip']
            )
            
            # Upload file
            with open(local_path, 'rb' if is_binary else 'r', encoding=None if is_binary else 'utf-8') as file:
                if is_binary:
                    self.ftp.storbinary(f'STOR {FTP_PATH}{remote_path}', file)
                else:
                    self.ftp.storlines(f'STOR {FTP_PATH}{remote_path}', file)
            
            self.uploaded_files.append(remote_path)
            print(f"✅ Uploaded: {remote_path}")
            return True
            
        except Exception as e:
            error_msg = f"Failed to upload {local_path}: {e}"
            print(f"❌ {error_msg}")
            self.errors.append(error_msg)
            return False
    
    def upload_directory(self, local_dir, remote_dir=""):
        """Upload entire directory recursively"""
        local_path = Path(LOCAL_BASE) / local_dir
        
        if not local_path.exists():
            print(f"⚠️  Directory not found: {local_path}")
            return
        
        print(f"📂 Uploading directory: {local_dir}")
        
        for item in local_path.rglob('*'):
            if item.is_file():
                # Skip certain files
                if item.name.startswith('.') or item.suffix in ['.tmp', '.log']:
                    continue
                
                relative_path = item.relative_to(Path(LOCAL_BASE))
                remote_file_path = str(relative_path).replace('\\', '/')
                
                self.upload_file(str(item), remote_file_path)
    
    def upload_pattern(self, pattern):
        """Upload files matching a pattern"""
        base_path = Path(LOCAL_BASE)
        matching_files = list(base_path.glob(pattern))
        
        if not matching_files:
            print(f"⚠️  No files found matching: {pattern}")
            return
        
        print(f"📄 Uploading files matching: {pattern}")
        
        for file_path in matching_files:
            if file_path.is_file():
                relative_path = file_path.relative_to(base_path)
                remote_file_path = str(relative_path).replace('\\', '/')
                
                self.upload_file(str(file_path), remote_file_path)
    
    def deploy(self):
        """Main deployment process"""
        if not self.connect():
            return False
        
        print(f"\n🚀 Starting deployment at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("=" * 60)
        
        try:
            for item in UPLOAD_ITEMS:
                local_path = Path(LOCAL_BASE) / item
                
                if '*' in item:
                    # Handle glob patterns
                    self.upload_pattern(item)
                elif local_path.is_dir():
                    # Upload directory
                    self.upload_directory(item)
                elif local_path.is_file():
                    # Upload single file
                    self.upload_file(str(local_path), item)
                else:
                    print(f"⚠️  Item not found: {item}")
            
            print("\n" + "=" * 60)
            print("📊 Deployment Summary")
            print("=" * 60)
            print(f"✅ Files uploaded: {len(self.uploaded_files)}")
            
            if self.errors:
                print(f"❌ Errors: {len(self.errors)}")
                print("\nError details:")
                for error in self.errors[:5]:  # Show first 5 errors
                    print(f"  • {error}")
                if len(self.errors) > 5:
                    print(f"  ... and {len(self.errors) - 5} more errors")
            
            print(f"\n🎉 Deployment completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
            
            # Show post-deployment instructions
            print("\n" + "=" * 60)
            print("📋 Post-Deployment Steps")
            print("=" * 60)
            print("1. Run database migrations on server:")
            print("   php database/migrate.php migrate")
            print("")
            print("2. Clear any existing cache:")
            print("   Visit: https://11klassniki.ru/admin/cache-management.php")
            print("")
            print("3. Test new features:")
            print("   • Enhanced comments with like/dislike")
            print("   • Admin cache management")
            print("   • Error monitoring dashboard")
            print("   • Minified assets loading")
            print("")
            print("4. Monitor performance:")
            print("   Visit: https://11klassniki.ru/admin/monitoring.php")
            
            return len(self.errors) == 0
            
        except Exception as e:
            print(f"❌ Deployment failed: {e}")
            return False
        
        finally:
            if self.ftp:
                self.ftp.quit()
                print("🔌 FTP connection closed")

def main():
    """Main function"""
    print("🏗️  11klassniki Production Deployment")
    print("=====================================")
    
    # Auto-confirm for script execution
    print("\n✅ Auto-confirming deployment for script execution...")
    print("🚀 Proceeding with production deployment...")
    
    deployer = DeploymentManager()
    success = deployer.deploy()
    
    if success:
        print("\n🎉 All improvements successfully deployed to production!")
        sys.exit(0)
    else:
        print("\n⚠️  Deployment completed with some errors. Check the details above.")
        sys.exit(1)

if __name__ == "__main__":
    main()