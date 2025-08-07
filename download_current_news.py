#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Downloading template files from server...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Change to root directory
    ftp.cwd('/11klassnikiru/')
    
    files_to_download = [
        ('real_template.php', 'real_template.php'),
        ('real_components.php', 'real_components.php'),
        ('template-debug-colors.php', 'template-debug-colors.php'),
        ('common-components/real_header.php', 'common-components/real_header.php'),
        ('common-components/real_footer.php', 'common-components/real_footer.php'),
        ('common-components/real_title.php', 'common-components/real_title.php')
    ]
    
    downloaded = 0
    for server_file, local_file in files_to_download:
        try:
            # Ensure local directory exists
            local_dir = os.path.dirname(local_file)
            if local_dir and not os.path.exists(local_dir):
                os.makedirs(local_dir)
            
            with open(local_file, 'wb') as f:
                ftp.retrbinary(f'RETR {server_file}', f.write)
            print(f"✓ Downloaded {server_file} -> {local_file}")
            downloaded += 1
        except Exception as e:
            print(f"❌ Error downloading {server_file}: {e}")
    
    print(f"\n✅ Downloaded {downloaded}/{len(files_to_download)} files")
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")