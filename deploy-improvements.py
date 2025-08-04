#!/usr/bin/env python3

import ftplib
import os
from pathlib import Path

# FTP settings (same as previous successful deployments)
ftp_host = "185.46.8.204"
ftp_user = "u2666700"
ftp_pass = "19Dima08Dima08"
ftp_path = "/domains/11klassniki.ru/public_html/"

def upload_file(ftp, local_file, remote_file):
    """Upload a single file"""
    try:
        # Create remote directory if needed
        remote_dir = os.path.dirname(remote_file)
        if remote_dir:
            parts = remote_dir.split('/')
            current = ""
            for part in parts:
                if part:
                    current += part + "/"
                    try:
                        ftp.mkd(current)
                    except:
                        pass  # Directory might exist
        
        # Upload file
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ {remote_file}")
        return True
    except Exception as e:
        print(f"❌ {remote_file}: {e}")
        return False

def main():
    print("🚀 Deploying all improvements to 11klassniki.ru")
    print("=" * 50)
    
    # Key files to upload (most important improvements)
    files_to_upload = [
        # Security
        ("includes/security/csrf.php", "includes/security/csrf.php"),
        ("includes/security/rate_limiter.php", "includes/security/rate_limiter.php"),
        ("includes/security/security_headers.php", "includes/security/security_headers.php"),
        ("includes/security/input_sanitizer.php", "includes/security/input_sanitizer.php"),
        ("includes/security/security_bootstrap.php", "includes/security/security_bootstrap.php"),
        
        # Performance  
        ("includes/cache/page_cache.php", "includes/cache/page_cache.php"),
        ("includes/cache/cache_middleware.php", "includes/cache/cache_middleware.php"),
        ("includes/performance/query_cache.php", "includes/performance/query_cache.php"),
        ("includes/utils/lazy_loading.php", "includes/utils/lazy_loading.php"),
        ("includes/utils/minifier.php", "includes/utils/minifier.php"),
        
        # Enhanced Comments
        ("includes/comments/comment_enhancements.php", "includes/comments/comment_enhancements.php"),
        ("api/comments.php", "api/comments.php"),
        ("css/enhanced-comments.css", "css/enhanced-comments.css"),
        ("js/enhanced-comments.js", "js/enhanced-comments.js"),
        
        # Database
        ("includes/database/migration_manager.php", "includes/database/migration_manager.php"),
        ("database/migrate.php", "database/migrate.php"),
        ("database/migrations/2025_08_04_200000_add_failed_login_tracking.php", "database/migrations/2025_08_04_200000_add_failed_login_tracking.php"),
        ("database/migrations/2025_08_04_200001_add_remember_me_tokens.php", "database/migrations/2025_08_04_200001_add_remember_me_tokens.php"),
        ("database/migrations/2025_08_04_200002_add_password_reset_tokens.php", "database/migrations/2025_08_04_200002_add_password_reset_tokens.php"),
        ("database/migrations/2025_08_04_210000_enhance_comments_system.php", "database/migrations/2025_08_04_210000_enhance_comments_system.php"),
        
        # Admin Tools
        ("admin/cache-management.php", "admin/cache-management.php"),
        ("includes/monitoring/error_logger.php", "includes/monitoring/error_logger.php"),
        ("includes/monitoring/performance_monitor.php", "includes/monitoring/performance_monitor.php"),
        ("admin/monitoring.php", "admin/monitoring.php"),
        
        # Assets
        ("build/assets/bundle.min.css", "build/assets/bundle.min.css"),
        ("build/assets/bundle.min.js", "build/assets/bundle.min.js"),
        ("build/minify-assets.php", "build/minify-assets.php"),
        
        # Updated core
        ("Makefile", "Makefile"),
    ]
    
    try:
        # Connect to FTP
        print("🔌 Connecting to FTP server...")
        ftp = ftplib.FTP()
        ftp.connect(ftp_host, 21)
        ftp.login(ftp_user, ftp_pass)
        ftp.cwd(ftp_path)
        print("✅ Connected successfully")
        
        # Upload files
        base_path = "/Applications/XAMPP/xamppfiles/htdocs"
        uploaded = 0
        failed = 0
        
        for local_rel, remote_rel in files_to_upload:
            local_full = os.path.join(base_path, local_rel)
            
            if os.path.exists(local_full):
                if upload_file(ftp, local_full, remote_rel):
                    uploaded += 1
                else:
                    failed += 1
            else:
                print(f"⚠️  File not found: {local_rel}")
                failed += 1
        
        ftp.quit()
        
        print("\n" + "=" * 50)
        print(f"📊 Upload Summary:")
        print(f"✅ Uploaded: {uploaded} files")
        print(f"❌ Failed: {failed} files")
        
        if uploaded > 0:
            print(f"\n🎉 Successfully deployed improvements!")
            print("\n📋 Next steps:")
            print("1. Run migrations: php database/migrate.php migrate")
            print("2. Clear cache: Visit /admin/cache-management.php")
            print("3. Test enhanced comments and admin tools")
        
        return uploaded > 0
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

if __name__ == "__main__":
    success = main()
    if success:
        print("\n🚀 All improvements deployed to production!")
    else:
        print("\n⚠️  Deployment had issues - check FileZilla connection")