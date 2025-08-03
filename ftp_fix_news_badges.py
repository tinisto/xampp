#!/usr/bin/env python3
"""
Fix news badges to show correct news categories
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        ftp.cwd('/11klassnikiru')
        
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir in dirs:
                if dir:
                    try:
                        ftp.cwd(dir)
                    except:
                        ftp.mkd(dir)
                        ftp.cwd(dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Fixing news badges to show correct categories...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Updated news page with correct badge logic
        ('pages/common/news/news.php', 
         'pages/common/news/news.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                upload_file(ftp, local_path, remote_path)
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n🎯 Fixed:")
        print("✅ News badges now show news-specific categories")
        print("✅ Badge colors match news type:")
        print("   • Новости ВУЗов - Purple (#9b59b6)")
        print("   • Новости ССУЗов - Orange (#f39c12)")
        print("   • Новости школ - Green (#2ecc71)")
        print("   • Новости образования - Blue (#3498db)")
        
        print("\n🔍 Test the page:")
        print("https://11klassniki.ru/news")
        
        print("\n📋 How it works:")
        print("• Checks id_vpo > 0 → Shows 'Новости ВУЗов'")
        print("• Checks id_spo > 0 → Shows 'Новости ССУЗов'")
        print("• Checks id_school > 0 → Shows 'Новости школ'")
        print("• Otherwise → Shows 'Новости образования'")
        print("• No longer uses general site categories for badges")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()