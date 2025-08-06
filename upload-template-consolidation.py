#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

# Local base directory
LOCAL_BASE = "/Applications/XAMPP/xamppfiles/htdocs"

# Files updated during template consolidation
files_to_upload = [
    # Dashboard template migrations
    "pages/dashboard/admin-index/dashboard.php",
    "dashboard-force-new-template.php",
    
    # Modern template migrations
    "pages/login/login-modern.php",
    "pages/post/post.php",
    "scripts/migrate-to-css-variables.php",
    "dashboard/admin-tools/theme-reference.php",
    
    # Authorization template migrations
    "pages/login/login-old.php"
]

# Files to delete (unused template engines)
files_to_delete = [
    "common-components/template-engine-authorization.php",
    "common-components/template-engine-dashboard-minimal.php", 
    "common-components/template-engine-diagnostic.php",
    "common-components/template-engine-modern.php"
]

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        ftp.cwd(PATH)
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir_name in dirs:
                if dir_name:
                    try:
                        ftp.cwd(dir_name)
                    except:
                        try:
                            ftp.mkd(dir_name)
                            ftp.cwd(dir_name)
                        except:
                            pass
            ftp.cwd(PATH)
        
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def delete_file(ftp, remote_path):
    """Delete a file from FTP server"""
    try:
        ftp.cwd(PATH)
        ftp.delete(remote_path)
        print(f"✓ Deleted: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to delete {remote_path}: {str(e)}")
        return False

def main():
    print("=" * 70)
    print("TEMPLATE ENGINE CONSOLIDATION DEPLOYMENT")
    print("=" * 70)
    print("🎯 CONSOLIDATING TO ONE TEMPLATE ENGINE")
    print("")
    print("Before: 5 different template engines")
    print("After:  1 unified template engine (template-engine-ultimate.php)")
    print("")
    print("Migrating 9 files to use the ultimate template engine:")
    print("- 2 dashboard files")
    print("- 5 modern template files") 
    print("- 2 authorization files")
    print("")
    print("Deleting 4 unused template engines")
    print("=" * 70)
    
    uploaded = 0
    failed = 0
    deleted = 0
    delete_failed = 0
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(HOST)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP server")
        print()
        
        # Upload migrated files
        print("📤 UPLOADING MIGRATED FILES...")
        for file_path in files_to_upload:
            local_file = os.path.join(LOCAL_BASE, file_path)
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, file_path):
                    uploaded += 1
                else:
                    failed += 1
            else:
                print(f"✗ File not found: {local_file}")
                failed += 1
        
        print()
        print("🗑️  DELETING UNUSED TEMPLATE ENGINES...")
        # Delete old template engines
        for file_path in files_to_delete:
            if delete_file(ftp, file_path):
                deleted += 1
            else:
                delete_failed += 1
        
        ftp.quit()
        
        print("\n" + "=" * 70)
        print("CONSOLIDATION SUMMARY")
        print("=" * 70)
        print(f"✅ Files migrated: {uploaded}")
        print(f"❌ Migration failures: {failed}")
        print(f"🗑️  Template engines deleted: {deleted}")
        print(f"❌ Deletion failures: {delete_failed}")
        
        if failed == 0 and delete_failed == 0:
            print("\n🎉 TEMPLATE CONSOLIDATION COMPLETE!")
            print("✅ All pages now use ONE unified template engine")
            print("✅ Green headers work consistently across all pages")
            print("✅ Reduced code complexity and maintenance overhead")
        
        print("\n" + "=" * 70)
        print("PAGES TO TEST:")
        print("=" * 70)
        print("🔐 Dashboard: /dashboard (admin only)")
        print("🔑 Login Modern: /login")  
        print("📝 Posts: /post/[any-post-slug]")
        print("🔑 Login Old: /login-old")
        print("🎨 Theme Reference: /admin-tools/theme-reference (admin only)")
        print("")
        print("All should display:")
        print("- Consistent green headers")
        print("- Proper dark mode support")
        print("- Mobile responsive design")
        print("- Red content areas (for testing)")
        print("=" * 70)
        
    except Exception as e:
        print(f"✗ FTP Connection Error: {str(e)}")
        return 1
    
    return 0 if (failed == 0 and delete_failed == 0) else 1

if __name__ == "__main__":
    sys.exit(main())