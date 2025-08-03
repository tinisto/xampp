#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

file = 'fix_duplicate_categories.php'

print("Uploading duplicate categories fix...")

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
    print('\nThis will:')
    print('1. Find duplicate categories')
    print('2. Delete the "for-applicants" version')
    print('3. Keep the "abiturientam" version')
    print('4. Show all categories with test links')
    
except Exception as e:
    print(f'❌ Error: {e}')