#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

print("Uploading cleaned up write page...")

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
    print('\n✅ Write page cleaned up!')
    print('\nChanges made:')
    print('1. Removed "или зарегистрируйтесь" - now only shows "войдите"')
    print('2. Fixed spacing after comma')
    print('3. Removed icon before "Чтобы отправить сообщение"')
    print('4. Removed entire "Контактная информация" section')
    print('\nCheck: https://11klassniki.ru/write')
    
except Exception as e:
    print(f'❌ Error: {e}')