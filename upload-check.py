#!/usr/bin/env python3
import ftplib

ftp = ftplib.FTP('ftp.ipage.com', timeout=30)
ftp.login('franko', 'JyvR!HK2E!N55Zt')
ftp.cwd('11klassnikiru')

with open('check-index.php', 'rb') as f:
    ftp.storbinary('STOR check-index.php', f)

print('✅ Uploaded check-index.php')
print('🌐 Check: https://11klassniki.ru/check-index.php')

ftp.quit()