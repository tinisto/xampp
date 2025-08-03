#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

files = ['vpo-all-regions-direct.php', 'vpo-all-regions-standalone.php']

print("Uploading final regions fixes...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    ftp.cwd(WEB_ROOT)
    
    for file in files:
        with open(file, 'rb') as f:
            ftp.storbinary(f'STOR {file}', f)
        print(f'✅ Uploaded: {file}')
    
    ftp.quit()
    print('\nDone! Check:')
    print('1. https://11klassniki.ru/vpo-all-regions-standalone.php (completely standalone)')
    print('2. https://11klassniki.ru/vpo-all-regions-direct.php (with fixed database)')
    
except Exception as e:
    print(f'❌ Error: {e}')