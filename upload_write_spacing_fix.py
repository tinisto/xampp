#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

print("Uploading write page spacing and dark mode fixes...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    # Upload write-simple.php
    ftp.cwd(WEB_ROOT + '/pages/write')
    with open('pages/write/write-simple.php', 'rb') as f:
        ftp.storbinary('STOR write-simple.php', f)
    print('✅ Uploaded: write-simple.php')
    
    ftp.quit()
    print('\n✅ Write page fixed!')
    print('\nChanges made:')
    print('1. Added non-breaking space (&nbsp;) after comma to ensure proper spacing')
    print('2. Added CSS for dark mode button visibility')
    print('3. Made button text always white with !important')
    print('\nThe button "Отправить сообщение" should now be visible in dark mode')
    print('\nCheck: https://11klassniki.ru/write')
    
except Exception as e:
    print(f'❌ Error: {e}')