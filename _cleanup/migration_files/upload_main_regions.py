#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

print("Uploading main regions fix...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    # Upload main file
    file_path = 'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php'
    remote_dir = '/11klassnikiru/pages/common/educational-institutions-all-regions'
    
    ftp.cwd(remote_dir)
    
    with open(file_path, 'rb') as f:
        ftp.storbinary('STOR educational-institutions-all-regions.php', f)
    print('✅ Uploaded: educational-institutions-all-regions.php')
    
    ftp.quit()
    print('\nDone!')
    print('Check: https://11klassniki.ru/vpo-all-regions')
    
except Exception as e:
    print(f'❌ Error: {e}')