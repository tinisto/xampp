#!/usr/bin/env python3

import ftplib
import os

def try_upload():
    """Try different FTP configurations"""
    
    # Try different FTP configs
    configs = [
        {'host': 'ftp.ipage.com', 'user': 'franko', 'pass': 'JyvR!HK2E!N55Zt'},
        {'host': 'ftp.ipage.com', 'user': 'admin_claude', 'pass': 'W4eZ!#9uwLmrMay'},
        {'host': '11klassnikiru67871.ipagemysql.com', 'user': 'franko', 'pass': 'JyvR!HK2E!N55Zt'},
    ]
    
    files_to_upload = [
        'final_vpo_spo_fix.php',
        'test_db_connection.php'
    ]
    
    for config in configs:
        print(f"🔄 Trying {config['user']}@{config['host']}...")
        
        try:
            ftp = ftplib.FTP(config['host'])
            ftp.login(config['user'], config['pass'])
            
            # Try to find web directory
            try:
                ftp.cwd('/11klassnikiru')
                print(f"✅ Found /11klassnikiru directory")
            except:
                try:
                    ftp.cwd('/public_html')
                    print(f"✅ Found /public_html directory")
                except:
                    try:
                        ftp.cwd('/www')
                        print(f"✅ Found /www directory")
                    except:
                        print(f"📁 Listing root directory:")
                        ftp.dir()
                        continue
            
            # Upload files
            uploaded_count = 0
            for file_name in files_to_upload:
                local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{file_name}'
                
                if os.path.exists(local_file):
                    try:
                        with open(local_file, 'rb') as f:
                            ftp.storbinary(f'STOR {file_name}', f)
                        print(f"✅ Uploaded {file_name}")
                        uploaded_count += 1
                    except Exception as e:
                        print(f"❌ Failed to upload {file_name}: {e}")
                else:
                    print(f"❌ File not found: {file_name}")
            
            ftp.quit()
            
            if uploaded_count > 0:
                print(f"\n🎯 Successfully uploaded {uploaded_count} files!")
                print("Next steps:")
                print("1. Visit https://11klassniki.ru/final_vpo_spo_fix.php")
                print("2. Visit https://11klassniki.ru/test_db_connection.php")
                return True
            
        except Exception as e:
            print(f"❌ Failed with {config['user']}: {e}")
            continue
    
    print("❌ All FTP attempts failed")
    return False

if __name__ == "__main__":
    try_upload()