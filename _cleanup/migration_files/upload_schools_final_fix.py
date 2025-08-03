#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

print("Uploading schools final fixes...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    # Upload fixed standalone page
    ftp.cwd(WEB_ROOT)
    with open('schools-all-regions-fixed.php', 'rb') as f:
        ftp.storbinary('STOR schools-all-regions-fixed.php', f)
    print('✅ Uploaded: schools-all-regions-fixed.php')
    
    # Upload main page fix
    file_path = 'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php'
    remote_dir = '/11klassnikiru/pages/common/educational-institutions-all-regions'
    
    ftp.cwd(remote_dir)
    
    with open(file_path, 'rb') as f:
        ftp.storbinary('STOR educational-institutions-all-regions.php', f)
    print('✅ Uploaded: educational-institutions-all-regions.php')
    
    ftp.quit()
    print('\n✅ Schools pages fixed!')
    print('\nCheck:')
    print('1. https://11klassniki.ru/schools-all-regions-fixed.php (diagnostic version)')
    print('2. https://11klassniki.ru/schools-all-regions (main page)')
    print('\nThe fix handles the different column names in schools table:')
    print('- Uses id_school instead of id')
    print('- Uses id_region instead of region_id')
    
except Exception as e:
    print(f'❌ Error: {e}')