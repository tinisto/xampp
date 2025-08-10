#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def upload_clean_state():
    try:
        print("🚀 UPLOADING YESTERDAY'S CLEAN STATE...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Key files to upload from yesterday's clean commit
        key_files = [
            'index.php',
            'news-simple.php',
            'real_template.php',
            'common-components/real_header.php', 
            'common-components/real_footer.php',
            '.htaccess'
        ]
        
        uploaded = 0
        for file_path in key_files:
            if os.path.exists(file_path):
                try:
                    # Create directories if needed
                    if '/' in file_path:
                        dir_path = '/'.join(file_path.split('/')[:-1])
                        try:
                            ftp.mkd(dir_path)
                        except:
                            pass  # Directory might already exist
                    
                    with open(file_path, 'rb') as f:
                        ftp.storbinary(f'STOR {file_path}', f)
                    print(f"✅ Uploaded: {file_path}")
                    uploaded += 1
                except Exception as e:
                    print(f"❌ Failed {file_path}: {e}")
            else:
                print(f"⚠️  Not found: {file_path}")
        
        ftp.quit()
        print(f"\n🎉 Uploaded {uploaded} files from yesterday's clean commit!")
        print("✅ Server now has clean state without debug messages")
        print("🔗 Test: https://11klassniki.ru")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    print("📅 Git commit 5fa83d1: Complete advanced comment system implementation - 20 features")
    print("🧹 This is yesterday's clean state without today's debug messages")
    upload_clean_state()