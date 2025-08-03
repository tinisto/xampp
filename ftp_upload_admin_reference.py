#!/usr/bin/env python3
"""
Upload comprehensive theme reference to admin area
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
            print(f"✅ Uploaded: {local_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Uploading admin theme reference...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Admin theme reference page
        ('admin/theme-reference.php', 'admin/theme-reference.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Create admin directory if it doesn't exist
        try:
            ftp.mkd('admin')
        except:
            pass  # Directory might already exist
        
        # Upload the file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                upload_file(ftp, local_path, remote_path)
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n📚 Admin Reference Page Features:")
        print("1. Complete color system reference")
        print("2. Typography examples")
        print("3. Post/News cards (desktop & mobile)")
        print("4. Test cards with selection view")
        print("5. Question cards with answers (correct/incorrect states)")
        print("6. Mobile-specific variants")
        print("7. Form elements")
        print("8. Navigation patterns")
        print("\n🔒 Admin Reference URL:")
        print("https://11klassniki.ru/admin/theme-reference.php")
        print("\n💡 This page will be kept in your admin area for future reference!")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()