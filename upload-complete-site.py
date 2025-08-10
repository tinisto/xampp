#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def upload_complete_site():
    try:
        print("🚀 UPLOADING COMPLETE WORKING SITE...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # All essential files for complete site
        essential_files = [
            # Core pages
            'index.php',
            'news-new.php',
            'category-new.php',
            
            # Template system
            'real_template.php',
            'common-components/real_header.php',
            'common-components/real_footer.php',
            'common-components/site-icon.php',
            'common-components/content-wrapper.php',
            
            # Database and config
            'database/db_connections.php',
            'config/loadEnv.php',
            '.env',
            
            # Routing
            '.htaccess',
            
            # CSS and assets
            'css/unified-styles.css',
            'favicon.svg',
            
            # Essential includes
            'includes/SessionManager.php',
            'includes/config/environment.php'
        ]
        
        uploaded = 0
        for file_path in essential_files:
            if os.path.exists(file_path):
                try:
                    # Create directories if needed
                    if '/' in file_path:
                        dirs = file_path.split('/')[:-1]
                        current_path = ''
                        for dir_name in dirs:
                            current_path = f"{current_path}/{dir_name}" if current_path else dir_name
                            try:
                                ftp.mkd(current_path)
                            except:
                                pass  # Directory might exist
                    
                    with open(file_path, 'rb') as f:
                        ftp.storbinary(f'STOR {file_path}', f)
                    print(f"✅ Uploaded: {file_path}")
                    uploaded += 1
                except Exception as e:
                    print(f"❌ Failed {file_path}: {e}")
            else:
                print(f"⚠️  Missing: {file_path}")
        
        ftp.quit()
        print(f"\n🎉 Complete site uploaded! {uploaded} files deployed")
        print("🔗 Test: https://11klassniki.ru")
        print("✅ Site should now display full content")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    upload_complete_site()