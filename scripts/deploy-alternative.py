#!/usr/bin/env python3
"""
Alternative deployment using different FTP settings
"""

import ftplib
import os
import sys
from pathlib import Path
import socket

# Try different FTP configurations
FTP_CONFIGS = [
    {
        "host": "185.46.8.204",
        "user": "u2666700", 
        "pass": "19Dima08Dima08",
        "path": "/domains/11klassniki.ru/public_html/",
        "port": 21
    },
    {
        "host": "ftp.11klassniki.ru",
        "user": "u2666700",
        "pass": "19Dima08Dima08", 
        "path": "/public_html/",
        "port": 21
    },
    {
        "host": "185.46.8.204",
        "user": "u2666700",
        "pass": "19Dima08Dima08",
        "path": "/domains/11klassniki.ru/public_html/",
        "port": 2121  # Alternative port
    }
]

def test_connection(config):
    """Test FTP connection with given configuration"""
    try:
        print(f"🔌 Testing connection to {config['host']}:{config['port']}...")
        
        # Set socket timeout
        socket.setdefaulttimeout(10)
        
        ftp = ftplib.FTP()
        ftp.connect(config['host'], config['port'])
        ftp.login(config['user'], config['pass'])
        
        # Test changing to the target directory
        try:
            ftp.cwd(config['path'])
            print(f"✅ Connection successful - can access {config['path']}")
            ftp.quit()
            return True
        except:
            print(f"⚠️  Connected but cannot access {config['path']}")
            # Try to list current directory
            try:
                files = ftp.nlst()
                print(f"📁 Current directory contains: {len(files)} items")
                if files:
                    print(f"   Sample items: {files[:3]}")
            except:
                pass
            ftp.quit()
            return False
            
    except Exception as e:
        print(f"❌ Connection failed: {e}")
        return False

def upload_critical_files(ftp, base_path):
    """Upload only the most critical files"""
    critical_files = [
        # Security enhancements
        "includes/security/csrf.php",
        "includes/security/rate_limiter.php", 
        "includes/security/security_headers.php",
        "includes/security/input_sanitizer.php",
        
        # Performance improvements
        "includes/cache/page_cache.php",
        "includes/performance/query_cache.php",
        "includes/utils/lazy_loading.php",
        "includes/utils/minifier.php",
        
        # Enhanced comments
        "includes/comments/comment_enhancements.php",
        "api/comments.php",
        "css/enhanced-comments.css",
        "js/enhanced-comments.js",
        
        # Database migrations
        "includes/database/migration_manager.php",
        "database/migrate.php",
        "database/migrations/2025_08_04_200000_add_failed_login_tracking.php",
        "database/migrations/2025_08_04_200001_add_remember_me_tokens.php", 
        "database/migrations/2025_08_04_200002_add_password_reset_tokens.php",
        "database/migrations/2025_08_04_210000_enhance_comments_system.php",
        
        # Admin tools
        "admin/cache-management.php",
        
        # Minified assets
        "build/assets/bundle.min.css",
        "build/assets/bundle.min.js",
        
        # Updated Makefile
        "Makefile"
    ]
    
    uploaded = 0
    errors = 0
    
    for file_path in critical_files:
        local_file = Path(base_path) / file_path
        
        if not local_file.exists():
            print(f"⚠️  File not found: {file_path}")
            continue
            
        try:
            # Create remote directory if needed
            remote_dir = os.path.dirname(file_path)
            if remote_dir:
                create_remote_dirs(ftp, remote_dir)
            
            # Upload file
            with open(local_file, 'rb') as f:
                ftp.storbinary(f'STOR {file_path}', f)
            
            print(f"✅ Uploaded: {file_path}")
            uploaded += 1
            
        except Exception as e:
            print(f"❌ Failed to upload {file_path}: {e}")
            errors += 1
    
    return uploaded, errors

def create_remote_dirs(ftp, path):
    """Create remote directory structure"""
    dirs = path.split('/')
    current = ""
    
    for dir_name in dirs:
        if not dir_name:
            continue
            
        current += dir_name + "/"
        try:
            ftp.mkd(current)
        except:
            pass  # Directory might already exist

def main():
    """Main deployment function"""
    print("🏗️  11klassniki Alternative Deployment")
    print("=======================================")
    
    base_path = "/Applications/XAMPP/xamppfiles/htdocs"
    
    # Test all configurations
    working_config = None
    for i, config in enumerate(FTP_CONFIGS):
        print(f"\n📡 Testing configuration {i+1}/{len(FTP_CONFIGS)}")
        if test_connection(config):
            working_config = config
            break
    
    if not working_config:
        print("\n❌ All FTP configurations failed")
        print("\n📋 Manual Upload Instructions:")
        print("=" * 50)
        print("1. Download all files from the GitHub repository")
        print("2. Use cPanel File Manager or FTP client to upload:")
        print("   - All files in /includes/ directory")
        print("   - All files in /database/ directory") 
        print("   - All files in /api/ directory")
        print("   - All files in /admin/ directory")
        print("   - Updated CSS and JS files")
        print("   - New build/ directory")
        print("3. Run database migrations: php database/migrate.php migrate")
        return False
    
    # Upload files using working configuration
    print(f"\n🚀 Starting upload using {working_config['host']}")
    
    try:
        socket.setdefaulttimeout(30)
        ftp = ftplib.FTP()
        ftp.connect(working_config['host'], working_config['port'])
        ftp.login(working_config['user'], working_config['pass'])
        
        # Change to target directory or use root
        try:
            ftp.cwd(working_config['path'])
        except:
            print("⚠️  Using root directory")
        
        uploaded, errors = upload_critical_files(ftp, base_path)
        
        ftp.quit()
        
        print(f"\n📊 Upload Summary:")
        print(f"✅ Files uploaded: {uploaded}")
        print(f"❌ Errors: {errors}")
        
        if uploaded > 0:
            print(f"\n🎉 Successfully uploaded {uploaded} critical files!")
            print("\n📋 Next Steps:")
            print("1. Run database migrations: php database/migrate.php migrate")
            print("2. Clear cache: Visit /admin/cache-management.php")
            print("3. Test enhanced comment system")
            return True
        else:
            return False
            
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)