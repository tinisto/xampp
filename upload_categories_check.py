#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

file = 'check_categories.php'

print("Uploading categories check...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    ftp.cwd(WEB_ROOT)
    
    with open(file, 'rb') as f:
        ftp.storbinary(f'STOR {file}', f)
    print(f'✅ Uploaded: {file}')
    
    ftp.quit()
    print(f'\nVisit: https://11klassniki.ru/{file}')
    
except Exception as e:
    print(f'❌ Error: {e}')