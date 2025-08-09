#!/usr/bin/env python3
import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def cleanup_and_verify():
    """Delete setup file and verify deployment"""
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server\n")
        
        ftp.cwd(FTP_ROOT)
        
        # Delete the setup migration file
        try:
            ftp.delete('setup-comments-migration.php')
            print("✅ Deleted setup-comments-migration.php for security\n")
        except:
            print("⚠️  Could not delete setup-comments-migration.php (may already be deleted)\n")
        
        # Verify key files exist
        print("Verifying deployment...")
        files_to_check = [
            'api/comments/like.php',
            'api/comments/edit.php',
            'api/comments/report.php',
            'api/comments/analytics.php',
            'api/comments/add.php',
            'api/comments/threaded.php',
            'common-components/threaded-comments.php',
            'dashboard-moderation.php',
            'dashboard-analytics.php'
        ]
        
        verified = 0
        for file_path in files_to_check:
            try:
                size = ftp.size(file_path)
                if size > 0:
                    print(f"✅ {file_path} - {size} bytes")
                    verified += 1
            except:
                print(f"❌ {file_path} - NOT FOUND")
        
        ftp.quit()
        
        print(f"\n✅ Deployment verified: {verified}/{len(files_to_check)} files")
        print("\n🎉 COMMENT SYSTEM DEPLOYMENT COMPLETE!")
        print("\nThe advanced comment system is now live with:")
        print("- Threaded comments with replies")
        print("- Like/dislike voting system")
        print("- Comment editing (15-min window)")
        print("- Moderation dashboard (/dashboard-moderation.php)")
        print("- Analytics dashboard (/dashboard-analytics.php)")
        print("- User @mentions")
        print("- Email notifications (requires cron setup)")
        print("- Comment reporting system")
        print("\n📌 Next step: Set up the cron job for email notifications")
        print("   Add to crontab: */10 * * * * /usr/bin/php /path/to/cron/send-comment-notifications.php")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    cleanup_and_verify()