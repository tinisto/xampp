#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

print("Uploading write page fixes...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    # Upload standalone write page
    ftp.cwd(WEB_ROOT)
    with open('write-standalone.php', 'rb') as f:
        ftp.storbinary('STOR write-standalone.php', f)
    print('✅ Uploaded: write-standalone.php')
    
    # Upload write-simple.php
    ftp.cwd(WEB_ROOT + '/pages/write')
    with open('pages/write/write-simple.php', 'rb') as f:
        ftp.storbinary('STOR write-simple.php', f)
    print('✅ Uploaded: write-simple.php')
    
    # Upload fixed write.php
    with open('pages/write/write.php', 'rb') as f:
        ftp.storbinary('STOR write.php', f)
    print('✅ Uploaded: write.php')
    
    # Upload .htaccess
    ftp.cwd(WEB_ROOT)
    with open('.htaccess', 'rb') as f:
        ftp.storbinary('STOR .htaccess', f)
    print('✅ Uploaded: .htaccess')
    
    ftp.quit()
    print('\n✅ Write page fixed!')
    print('\nThe /write page now uses a simple version that works without the missing template engine.')
    print('\nCheck:')
    print('- https://11klassniki.ru/write (main page)')
    print('- https://11klassniki.ru/write-standalone.php (backup standalone version)')
    
except Exception as e:
    print(f'❌ Error: {e}')