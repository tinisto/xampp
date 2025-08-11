#!/usr/bin/env python3
import ftplib
import os
import sys

def upload_branding_files():
    # FTP credentials for 11klassniki.ru
    ftp_host = "ftp.ipage.com"
    ftp_user = "franko"
    ftp_pass = "JyvR!HK2E!N55Zt"
    remote_dir = "/11klassnikiru"
    
    # Files to upload with their local and remote paths
    files_to_upload = [
        # Favicon and logo files
        ("favicon.svg", "/11klassnikiru/favicon.svg"),
        ("logo-final-swoosh.svg", "/11klassnikiru/logo-final-swoosh.svg"),
        
        # Header and footer includes
        ("includes/header_modern.php", "/11klassnikiru/includes/header_modern.php"),
        ("includes/footer_modern.php", "/11klassnikiru/includes/footer_modern.php"),
        
        # Test pages (optional)
        ("test-simple-header.php", "/11klassnikiru/test-simple-header.php"),
    ]
    
    try:
        print("🚀 Connecting to 11klassniki.ru FTP server...")
        ftp = ftplib.FTP(ftp_host)
        ftp.login(ftp_user, ftp_pass)
        print("✅ Connected successfully!")
        
        # Upload each file
        for local_file, remote_file in files_to_upload:
            if os.path.exists(local_file):
                print(f"📤 Uploading {local_file}...")
                
                try:
                    with open(local_file, 'rb') as file:
                        ftp.storbinary(f'STOR {remote_file}', file)
                    print(f"✅ Uploaded: {local_file}")
                except Exception as e:
                    print(f"❌ Failed to upload {local_file}: {e}")
            else:
                print(f"⚠️  File not found: {local_file}")
        
        ftp.quit()
        print("\n🎉 Branding files upload complete!")
        print("\n🌐 Test the new design at:")
        print("https://11klassniki.ru/test-simple-header.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False
    
    return True

if __name__ == "__main__":
    upload_branding_files()