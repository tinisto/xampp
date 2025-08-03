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

def main():
    print("🔧 Fixing pages to use ONE TEMPLATE system...")
    
    files_to_upload = [
        # Fixed VPO/SPO all-regions to use template engine
        ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions-proper.php', 
         'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-proper.php'),
        ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-proper.php', 
         'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-proper.php'),
        
        # Updated .htaccess
        ('.htaccess', '.htaccess'),
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
        
        # Upload files
        success_count = 0
        
        print("\n📤 Uploading fixes...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                # Create directory structure if needed
                remote_dir = os.path.dirname(remote_path)
                if remote_dir:
                    dirs = remote_dir.split('/')
                    current_path = ''
                    for dir_name in dirs:
                        if dir_name:
                            current_path = current_path + '/' + dir_name if current_path else dir_name
                            try:
                                ftp.mkd(current_path)
                            except:
                                pass  # Directory might already exist
                
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 All pages now use ONE TEMPLATE!")
            print("\n✅ What's fixed:")
            print("   • VPO/SPO/Schools all-regions pages now use template engine")
            print("   • All pages will have unified header and footer")
            print("   • Theme toggle will work consistently")
            print("   • No more missing headers")
            print("\n🔍 Test these pages - they should all have headers now:")
            print("   • https://11klassniki.ru/vpo-all-regions")
            print("   • https://11klassniki.ru/spo-all-regions")
            print("   • https://11klassniki.ru/schools-all-regions")
            print("   • https://11klassniki.ru/tests")
            print("   • https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()