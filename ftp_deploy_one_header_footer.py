#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as file:
            ftp.storbinary(f'STOR {remote_file}', file)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

def rename_file(ftp, old_name, new_name):
    """Rename a file on FTP server"""
    try:
        ftp.rename(old_name, new_name)
        print(f"✅ Renamed: {old_name} -> {new_name}")
        return True
    except Exception as e:
        print(f"❌ Failed to rename {old_name}: {str(e)}")
        return False

def main():
    print("🚀 Deploying ONE HEADER, ONE FOOTER, ONE TEMPLATE solution...")
    
    files_to_upload = [
        # ONE unified header
        ('common-components/header-unified.php', 
         'common-components/header-unified.php'),
        
        # ONE unified footer
        ('common-components/footer-unified.php', 
         'common-components/footer-unified.php'),
        
        # Updated template engine to use only unified components
        ('common-components/template-engine-ultimate.php', 
         'common-components/template-engine-ultimate.php'),
    ]
    
    files_to_rename = [
        ('common-components/header.php', 'common-components/header.php.old'),
        ('common-components/header-modern.php', 'common-components/header-modern.php.old'),
        ('common-components/header-no-bootstrap.php', 'common-components/header-no-bootstrap.php.old'),
        ('common-components/header-old.php', 'common-components/header-old.php.old'),
        ('common-components/footer.php', 'common-components/footer.php.old'),
        ('common-components/footer-modern.php', 'common-components/footer-modern.php.old'),
        ('common-components/footer-old.php', 'common-components/footer-old.php.old'),
    ]
    
    try:
        # Connect to FTP
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully")
        
        # Change to 11klassnikiru directory
        try:
            ftp.cwd('11klassnikiru')
            print("✅ Changed to 11klassnikiru directory")
        except Exception as e:
            print(f"❌ Could not change to 11klassnikiru: {e}")
            return
        
        # Upload new files
        success_count = 0
        print("\n📤 Uploading unified components...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        # Rename old files
        print("\n📝 Renaming old headers and footers...")
        for old_name, new_name in files_to_rename:
            rename_file(ftp, old_name, new_name)
        
        if success_count == len(files_to_upload):
            print("\n🎉 ONE HEADER, ONE FOOTER, ONE TEMPLATE deployed!")
            print("\n✅ What's achieved:")
            print("   • ONE unified header for ALL pages")
            print("   • ONE unified footer for ALL pages")
            print("   • Template engine uses ONLY unified components")
            print("   • Old headers/footers renamed to prevent usage")
            print("   • Theme toggle will work consistently everywhere")
            print("   • No more header type confusion")
            print("\n🔍 Test all pages - they should all use the same header/footer:")
            print("   • https://11klassniki.ru/")
            print("   • https://11klassniki.ru/tests")
            print("   • https://11klassniki.ru/news")
            print("   • https://11klassniki.ru/vpo-all-regions")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()