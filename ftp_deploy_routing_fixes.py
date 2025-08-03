#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def deploy_routing_fixes():
    """Deploy routing and database fixes"""
    
    print("📤 Deploying routing fixes...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files_to_upload = [
            '.htaccess',
            'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php',
            'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php'
        ]
        
        for file_path in files_to_upload:
            local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
            
            # Create directories if needed
            remote_dir = os.path.dirname(file_path)
            if remote_dir and remote_dir != '.':
                dirs = remote_dir.split('/')
                for i in range(len(dirs)):
                    dir_path = '/'.join(dirs[:i+1])
                    try:
                        ftp.cwd(f'/11klassnikiru/{dir_path}')
                    except:
                        try:
                            ftp.mkd(f'/11klassnikiru/{dir_path}')
                        except:
                            pass
                ftp.cwd('/11klassnikiru')
            
            # Upload file
            try:
                if os.path.exists(local_file):
                    with open(local_file, 'rb') as f:
                        ftp.storbinary(f'STOR {file_path}', f)
                    print(f"✅ {file_path}")
                else:
                    print(f"❌ {file_path} - File not found")
            except Exception as e:
                print(f"❌ {file_path} - Failed: {e}")
        
        ftp.quit()
        
        print("\n🎯 Routing fixes deployed!")
        print("\n🧪 Test these URLs now:")
        print("1. https://11klassniki.ru/educational-institutions-all-regions?type=vpo")
        print("2. https://11klassniki.ru/educational-institutions-all-regions?type=spo")
        print("3. https://11klassniki.ru/schools-all-regions")
        print("\nChanges made:")
        print("✅ Added .htaccess rule for educational-institutions-all-regions")
        print("✅ Fixed region column mapping for schools (id_region vs region_id)")
        print("✅ Updated count queries to handle different table structures")
        
    except Exception as e:
        print(f"❌ Deploy failed: {e}")

if __name__ == "__main__":
    deploy_routing_fixes()