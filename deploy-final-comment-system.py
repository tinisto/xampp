#!/usr/bin/env python3
"""
Deploy final comment system files to production server
"""

import ftplib
import os
from pathlib import Path

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

# Files to upload
FILES_TO_UPLOAD = [
    # API endpoints
    ("api/comments/auto-tune.php", "/api/comments/auto-tune.php"),
    ("api/comments/monitor.php", "/api/comments/monitor.php"),
    ("api/comments/upload-image.php", "/api/comments/upload-image.php"),
    ("api/profile/update.php", "/api/profile/update.php"),
    
    # Dashboard pages
    ("dashboard-monitoring.php", "/dashboard-monitoring.php"),
    
    # Components
    ("common-components/rich-text-editor.php", "/common-components/rich-text-editor.php"),
    
    # User profile
    ("user-profile.php", "/user-profile.php"),
    
    # Configuration
    ("includes/comment-config.php", "/includes/comment-config.php"),
    
    # Scripts and documentation
    ("setup-cron-job.sh", "/setup-cron-job.sh"),
    ("test-comment-system.php", "/test-comment-system.php"),
    ("CRON_JOB_SETUP.md", "/CRON_JOB_SETUP.md"),
    ("MODERATOR_TRAINING.md", "/MODERATOR_TRAINING.md"),
    ("API_DOCUMENTATION.md", "/API_DOCUMENTATION.md"),
    
    # Updated pages with rich text editor integration
    ("pages/post/post.php", "/pages/post/post.php"),
    ("spo-single-new.php", "/spo-single-new.php"),
    ("vpo-single-new.php", "/vpo-single-new.php"),
    ("school-single-new.php", "/school-single-new.php"),
    
    # Updated htaccess for user profiles
    (".htaccess", "/.htaccess"),
    
    # Updated components
    ("common-components/threaded-comments.php", "/common-components/threaded-comments.php"),
    ("common-components/favicon.php", "/common-components/favicon.php")
]

def ensure_ftp_directory(ftp, path):
    """Create directory if it doesn't exist"""
    dirs = path.split('/')
    current = ""
    for dir in dirs:
        if dir:
            current += "/" + dir
            try:
                ftp.cwd(current)
            except:
                try:
                    ftp.mkd(current)
                    print(f"Created directory: {current}")
                except:
                    pass

def upload_file(ftp, local_path, remote_path):
    """Upload a single file"""
    try:
        # Ensure directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir and remote_dir != '/':
            ensure_ftp_directory(ftp, remote_dir)
        
        # Change to root directory
        ftp.cwd(FTP_ROOT)
        
        # Upload file
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✅ Uploaded: {local_path} -> {remote_path}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Starting deployment of final comment system files...")
    print(f"📡 Connecting to {FTP_HOST}...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        print(f"📁 Changed to directory: {FTP_ROOT}")
        
        # Create config directory if needed
        try:
            ftp.mkd("/config")
            print("Created /config directory")
        except:
            pass
        
        # Create includes directory if needed
        try:
            ftp.mkd("/includes")
            print("Created /includes directory")
        except:
            pass
        
        # Upload files
        success_count = 0
        fail_count = 0
        
        for local_file, remote_file in FILES_TO_UPLOAD:
            local_path = Path(local_file)
            if local_path.exists():
                if upload_file(ftp, local_file, remote_file):
                    success_count += 1
                else:
                    fail_count += 1
            else:
                print(f"⚠️  Local file not found: {local_file}")
                fail_count += 1
        
        # Create empty config file for comment limits
        print("\n📝 Creating default comment limits config...")
        config_content = '''{
    "rate_limits": {
        "comments_per_minute": 3,
        "comments_per_hour": 20,
        "comments_per_day": 100
    },
    "spam_keywords": [
        "spam", "viagra", "casino", "xxx", "porn"
    ],
    "auto_approve_threshold": 5,
    "min_comment_length": 3,
    "max_comment_length": 2000
}'''
        
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.json') as tmp:
            tmp.write(config_content)
            tmp_path = tmp.name
        
        try:
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /config/comment-limits.json', file)
            print("✅ Created default comment-limits.json")
            os.unlink(tmp_path)
        except Exception as e:
            print(f"❌ Failed to create config file: {str(e)}")
        
        # Summary
        print(f"\n📊 Deployment Summary:")
        print(f"✅ Successfully uploaded: {success_count} files")
        print(f"❌ Failed: {fail_count} files")
        
        # Close connection
        ftp.quit()
        print("\n✅ Deployment complete!")
        
        # Reminders
        print("\n📋 Post-deployment checklist:")
        print("1. Run setup-cron-job.sh on the server to enable email notifications")
        print("2. Test the comment system at https://11klassniki.ru/test-comment-system.php")
        print("3. Check monitoring dashboard at https://11klassniki.ru/dashboard-monitoring.php")
        print("4. Review moderator guide at https://11klassniki.ru/MODERATOR_TRAINING.md")
        print("5. Share API docs with mobile developers: https://11klassniki.ru/API_DOCUMENTATION.md")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())