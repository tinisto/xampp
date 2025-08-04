#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_critical_files():
    print("🚀 Fast deployment of critical improvements")
    
    # Only the most essential files
    critical_files = [
        # Security
        ("includes/security/csrf.php", "includes/security/csrf.php"),
        ("includes/security/rate_limiter.php", "includes/security/rate_limiter.php"),
        ("includes/security/security_headers.php", "includes/security/security_headers.php"),
        
        # Enhanced Comments
        ("includes/comments/comment_enhancements.php", "includes/comments/comment_enhancements.php"),
        ("api/comments.php", "api/comments.php"),
        ("css/enhanced-comments.css", "css/enhanced-comments.css"),
        ("js/enhanced-comments.js", "js/enhanced-comments.js"),
        
        # Database Migrations
        ("includes/database/migration_manager.php", "includes/database/migration_manager.php"),
        ("database/migrate.php", "database/migrate.php"),
        ("database/migrations/2025_08_04_210000_enhance_comments_system.php", "database/migrations/2025_08_04_210000_enhance_comments_system.php"),
        
        # Performance
        ("includes/cache/page_cache.php", "includes/cache/page_cache.php"),
        ("includes/performance/query_cache.php", "includes/performance/query_cache.php"),
        
        # Admin Tools
        ("admin/cache-management.php", "admin/cache-management.php"),
        
        # Minified Assets
        ("build/assets/bundle.min.css", "build/assets/bundle.min.css"),
        ("build/assets/bundle.min.js", "build/assets/bundle.min.js"),
    ]
    
    try:
        print("🔌 Connecting to ftp.ipage.com...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected successfully")
        
        uploaded = 0
        base_path = "/Applications/XAMPP/xamppfiles/htdocs"
        
        for local_rel, remote_rel in critical_files:
            local_path = os.path.join(base_path, local_rel)
            
            if os.path.exists(local_path):
                try:
                    # Create directory if needed
                    remote_dir = "/".join(remote_rel.split("/")[:-1])
                    if remote_dir:
                        try:
                            ftp.mkd(remote_dir)
                        except:
                            pass
                    
                    # Upload file
                    with open(local_path, 'rb') as f:
                        ftp.storbinary(f'STOR {remote_rel}', f)
                    
                    print(f"✅ {remote_rel}")
                    uploaded += 1
                    
                except Exception as e:
                    print(f"❌ {remote_rel}: {e}")
            else:
                print(f"⚠️  Not found: {local_rel}")
        
        ftp.quit()
        
        print(f"\n🎉 Uploaded {uploaded} critical files!")
        print("\n📋 Next steps:")
        print("1. Run: php database/migrate.php migrate")
        print("2. Visit: /admin/cache-management.php")
        print("3. Test enhanced comments")
        
        return uploaded > 0
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

if __name__ == "__main__":
    upload_critical_files()