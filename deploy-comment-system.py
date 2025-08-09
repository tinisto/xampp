#!/usr/bin/env python3
import ftplib
import os
from datetime import datetime

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Ensure directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            try:
                ftp.cwd(FTP_ROOT + remote_dir)
            except:
                # Create directory if it doesn't exist
                dirs = remote_dir.strip('/').split('/')
                current_dir = FTP_ROOT
                for dir in dirs:
                    current_dir += '/' + dir
                    try:
                        ftp.cwd(current_dir)
                    except:
                        ftp.mkd(current_dir)
                        ftp.cwd(current_dir)
        
        # Upload file
        ftp.cwd(FTP_ROOT)
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✅ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_path}: {str(e)}")
        return False

def deploy_comment_system():
    """Deploy all comment system files"""
    
    # Files to upload
    files_to_upload = [
        # API endpoints
        'api/comments/like.php',
        'api/comments/edit.php',
        'api/comments/report.php',
        'api/comments/analytics.php',
        'api/comments/add.php',  # Updated
        'api/comments/threaded.php',  # Updated
        
        # Components
        'common-components/threaded-comments.php',  # Updated
        
        # Fixed regional pages
        'vpo-in-region-new.php',
        'schools-in-region-real.php',
        
        # Dashboard pages
        'dashboard-moderation.php',
        'dashboard-analytics.php',
        
        # Database and utilities
        'database/migrations/update_comments_threaded_system.sql',
        'run-comments-migration.php',
        
        # Cron job
        'cron/send-comment-notifications.php',
        
        # Session documentation
        'CLAUDE_SESSION.md'
    ]
    
    try:
        print(f"\n🚀 Deploying Comment System to {FTP_HOST}")
        print(f"📅 Deployment started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to FTP server\n")
        
        # Upload each file
        success_count = 0
        for file_path in files_to_upload:
            local_file = os.path.join(os.path.dirname(__file__), file_path)
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, file_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {file_path}")
        
        # Close FTP connection
        ftp.quit()
        
        print(f"\n✅ Deployment completed!")
        print(f"📊 Uploaded {success_count}/{len(files_to_upload)} files")
        print(f"📅 Finished at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        
        print("\n⚠️  IMPORTANT NEXT STEPS:")
        print("1. Run the database migration:")
        print("   - Access: https://11klassniki.ru/run-comments-migration.php")
        print("   - Or via SSH: php /path/to/run-comments-migration.php")
        print("\n2. Set up the cron job for email notifications:")
        print("   - Add to crontab: */10 * * * * /usr/bin/php /path/to/cron/send-comment-notifications.php")
        print("\n3. Update admin menu to include new dashboards:")
        print("   - /dashboard-moderation.php")
        print("   - /dashboard-analytics.php")
        
    except Exception as e:
        print(f"❌ Deployment failed: {str(e)}")

if __name__ == "__main__":
    deploy_comment_system()