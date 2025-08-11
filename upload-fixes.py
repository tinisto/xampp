#!/usr/bin/env python3
import ftplib
import os

def upload_fixed_files():
    # FTP credentials
    ftp_host = "ftp.ipage.com"
    ftp_user = "franko"
    ftp_pass = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        ("home_modern.php", "/11klassnikiru/home_modern.php"),
        ("real_template_local.php", "/11klassnikiru/real_template_local.php"),
    ]
    
    try:
        print("🚀 Uploading fixed files to production...")
        ftp = ftplib.FTP(ftp_host)
        ftp.login(ftp_user, ftp_pass)
        print("✅ Connected successfully!")
        
        for local_file, remote_file in files_to_upload:
            if os.path.exists(local_file):
                print(f"📤 Uploading {local_file}...")
                with open(local_file, 'rb') as file:
                    ftp.storbinary(f'STOR {remote_file}', file)
                print(f"✅ Uploaded: {local_file}")
            else:
                print(f"⚠️  File not found: {local_file}")
        
        ftp.quit()
        print("\n🎉 Upload complete!")
        print("\n🌐 Test at: https://11klassniki.ru/home_modern.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_fixed_files()