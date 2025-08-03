#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

files = ['check_regions_columns.php', 'vpo-all-regions-fixed.php']

print("Uploading regions fix files...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    ftp.cwd(WEB_ROOT)
    
    for file in files:
        path = Path(file)
        if path.exists():
            with open(path, 'rb') as f:
                ftp.storbinary(f'STOR {file}', f)
            print(f'✅ Uploaded: {file}')
        else:
            print(f'❌ File not found: {file}')
    
    ftp.quit()
    print('\nDone!')
    print('Visit: https://11klassniki.ru/check_regions_columns.php')
    print('And: https://11klassniki.ru/vpo-all-regions-fixed.php')
    
except Exception as e:
    print(f'❌ Error: {e}')