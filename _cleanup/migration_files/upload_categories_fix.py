#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

files = ['check_categories.php', 'fix_categories.php']

print("Uploading categories fixes...")

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
    print('\nFirst visit: https://11klassniki.ru/check_categories.php')
    print('To see the current state of categories')
    print('\nThen visit: https://11klassniki.ru/fix_categories.php')
    print('To fix the categories table and add default categories')
    
except Exception as e:
    print(f'❌ Error: {e}')