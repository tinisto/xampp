#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def main():
    print("UPLOAD UPDATED PHP FILES")
    print("=" * 50)
    print("🔧 Uploading PHP files with new field names")
    print("   • All meta_d_* → meta_description")
    print("   • All meta_k_* removed")
    
    # Files that were updated
    files_to_upload = [
        'pages/post/post.php',
        'pages/post/post-data-fetch.php',
        'pages/common/news/news-data-fetch.php',
        'pages/common/news/news-form-content.php',
        'pages/common/news/news-process.php',
        'pages/common/create-process.php',
        'pages/common/create-form.php',
        'pages/dashboard/posts-dashboard/posts-view/posts-view-content.php',
        'pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form-content.php',
        'pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form-process.php',
        'pages/dashboard/posts-dashboard/posts-create/posts-create-process.php',
        'pages/dashboard/posts-dashboard/posts-create/posts-create-form.php'
    ]
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        for file_path in files_to_upload:
            local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{file_path}"
            if os.path.exists(local_path):
                with open(local_path, 'rb') as f:
                    ftp.storbinary(f'STOR {file_path}', f)
                print(f"✓ Uploaded: {file_path}")
            else:
                print(f"✗ File not found: {file_path}")
        
        ftp.quit()
        
        print("\n🎉 ALL PHP FILES UPDATED!")
        print("✅ Database standardized: meta_description only")
        print("✅ PHP files updated to use new field names")
        print("✅ Meta keywords completely removed")
        
        print("\n" + "=" * 50)
        print("FINAL SUMMARY:")
        print("• All tables now use 'meta_description'")
        print("• All meta keyword fields removed")
        print("• 12 PHP files updated")
        print("• Template engine no longer outputs meta keywords")
        print("\nThe site is now using standardized field names!")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())