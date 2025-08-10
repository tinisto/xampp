#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def restore_critical_files():
    try:
        print("🚨 EMERGENCY RESTORE - Uploading critical working files...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Critical files needed for site to work
        critical_files = [
            'database/db_connections.php',
            'config/loadEnv.php', 
            'news-simple.php',
            'index.php',
            'real_template.php'
        ]
        
        uploaded = 0
        for file_path in critical_files:
            if os.path.exists(file_path):
                try:
                    # Create directories if needed
                    if '/' in file_path:
                        dirs = file_path.split('/')[:-1]
                        for i, dir_name in enumerate(dirs):
                            dir_path = '/'.join(dirs[:i+1])
                            try:
                                ftp.mkd(dir_path)
                            except:
                                pass
                    
                    with open(file_path, 'rb') as f:
                        ftp.storbinary(f'STOR {file_path}', f)
                    print(f"✅ Restored: {file_path}")
                    uploaded += 1
                except Exception as e:
                    print(f"❌ Failed {file_path}: {e}")
            else:
                print(f"⚠️  Missing: {file_path}")
        
        ftp.quit()
        print(f"\n✅ Emergency restore complete! {uploaded} critical files uploaded")
        print("🔧 Site should be working now")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    restore_critical_files()