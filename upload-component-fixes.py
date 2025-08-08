#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    """Upload component fixes"""
    files_to_upload = [
        ("common-components/breadcrumb.php", "common-components/breadcrumb.php"),
        ("common-components/category-navigation.php", "common-components/category-navigation.php")
    ]
    
    try:
        # Connect to FTP
        print("Connecting to FTP server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Upload files
        success_count = 0
        for local_file, remote_file in files_to_upload:
            local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{local_file}"
            if upload_file(ftp, local_path, remote_file):
                success_count += 1
        
        print(f"\n✓ Successfully uploaded {success_count}/{len(files_to_upload)} files")
        
        # Close connection
        ftp.quit()
        print("✓ FTP connection closed")
        
        print("\n🎯 FIXES APPLIED:")
        print("1. ✅ Fixed empty breadcrumb items (won't render if text is empty)")
        print("2. ✅ Fixed news category navigation active states")
        print("3. ✅ Added proper hover effects for news navigation")
        
        print("\n🧪 TEST URLS:")
        print("- https://11klassniki.ru/news (should have 'Все новости' active)")
        print("- https://11klassniki.ru/news/novosti-vuzov (should have 'Новости ВПО' active)")
        print("- Both should have proper hover effects and no empty breadcrumbs")
        
    except Exception as e:
        print(f"\n✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())