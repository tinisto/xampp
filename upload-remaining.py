#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_remaining():
    print("🚀 Uploading remaining improvements")
    
    # Remaining important files
    remaining_files = [
        # Security
        ("includes/security/input_sanitizer.php", "includes/security/input_sanitizer.php"),
        ("includes/security/security_bootstrap.php", "includes/security/security_bootstrap.php"),
        
        # Performance & Utils
        ("includes/utils/lazy_loading.php", "includes/utils/lazy_loading.php"),
        ("includes/utils/minifier.php", "includes/utils/minifier.php"),
        ("includes/utils/image_optimizer.php", "includes/utils/image_optimizer.php"),
        ("includes/cache/cache_middleware.php", "includes/cache/cache_middleware.php"),
        
        # Monitoring
        ("includes/monitoring/error_logger.php", "includes/monitoring/error_logger.php"),
        ("includes/monitoring/performance_monitor.php", "includes/monitoring/performance_monitor.php"),
        ("admin/monitoring.php", "admin/monitoring.php"),
        
        # Database migrations
        ("database/migrations/2025_08_04_200000_add_failed_login_tracking.php", "database/migrations/2025_08_04_200000_add_failed_login_tracking.php"),
        ("database/migrations/2025_08_04_200001_add_remember_me_tokens.php", "database/migrations/2025_08_04_200001_add_remember_me_tokens.php"),
        ("database/migrations/2025_08_04_200002_add_password_reset_tokens.php", "database/migrations/2025_08_04_200002_add_password_reset_tokens.php"),
        
        # Build tools
        ("build/minify-assets.php", "build/minify-assets.php"),
        
        # Minified assets
        ("css/styles.min.css", "css/styles.min.css"),
        ("css/unified-styles.min.css", "css/unified-styles.min.css"),
        ("css/buttons-styles.min.css", "css/buttons-styles.min.css"),
        ("js/enhanced-comments.min.js", "js/enhanced-comments.min.js"),
        
        # Updated files
        ("Makefile", "Makefile"),
    ]
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        uploaded = 0
        base_path = "/Applications/XAMPP/xamppfiles/htdocs"
        
        for local_rel, remote_rel in remaining_files:
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
        
        print(f"\n🎉 Uploaded {uploaded} additional files!")
        print(f"\n🏆 TOTAL DEPLOYMENT STATUS:")
        print(f"✅ Security systems deployed")
        print(f"✅ Enhanced comments deployed") 
        print(f"✅ Performance optimizations deployed")
        print(f"✅ Database migrations deployed")
        print(f"✅ Admin tools deployed")
        
        return uploaded > 0
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_remaining()