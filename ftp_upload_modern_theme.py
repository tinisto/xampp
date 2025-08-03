#!/usr/bin/env python3
"""
Upload modern theme system with CSS variables
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
    print("🚀 Uploading modern theme system...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # CSS Variables
        ('css/theme-variables.css', 'css/theme-variables.css'),
        
        # Modern template engine
        ('common-components/template-engine-modern.php', 'common-components/template-engine-modern.php'),
        
        # Modern header
        ('common-components/header-modern-vars.php', 'common-components/header-modern-vars.php'),
        
        # Migration script
        ('scripts/migrate-to-css-variables.php', 'scripts/migrate-to-css-variables.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Create directories if needed
        try:
            ftp.mkd('css')
        except:
            pass
        
        try:
            ftp.mkd('scripts')
        except:
            pass
        
        # Upload each file
        success_count = 0
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        print(f"\n📊 Uploaded {success_count}/{len(files_to_upload)} files")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n🎨 Modern Theme System Features:")
        print("1. CSS Variables for all colors (YouTube-style)")
        print("2. Semantic color naming")
        print("3. Smooth theme transitions")
        print("4. No hardcoded colors")
        print("5. Consistent opacity values")
        print("\n🔍 Test pages:")
        print("- Run migration: https://11klassniki.ru/scripts/migrate-to-css-variables.php")
        print("- Test theme: https://11klassniki.ru/test-theme.php")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()