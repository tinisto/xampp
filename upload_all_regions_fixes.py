#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

print("Uploading all regions fixes...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    # Upload direct version
    ftp.cwd(WEB_ROOT)
    with open('vpo-all-regions-direct.php', 'rb') as f:
        ftp.storbinary('STOR vpo-all-regions-direct.php', f)
    print('✅ Uploaded: vpo-all-regions-direct.php')
    
    # Upload main file
    file_path = 'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php'
    remote_dir = '/11klassnikiru/pages/common/educational-institutions-all-regions'
    
    ftp.cwd(remote_dir)
    
    with open(file_path, 'rb') as f:
        ftp.storbinary('STOR educational-institutions-all-regions.php', f)
    print('✅ Uploaded: educational-institutions-all-regions.php')
    
    ftp.quit()
    print('\nDone! Check:')
    print('1. https://11klassniki.ru/vpo-all-regions-direct.php (direct connection)')
    print('2. https://11klassniki.ru/vpo-all-regions (standard page)')
    
except Exception as e:
    print(f'❌ Error: {e}')