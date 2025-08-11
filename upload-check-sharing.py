#!/usr/bin/env python3
import ftplib

ftp = ftplib.FTP('ftp.ipage.com', timeout=30)
ftp.login('franko', 'JyvR!HK2E!N55Zt')
ftp.cwd('11klassnikiru')

with open('check-sharing.php', 'rb') as f:
    ftp.storbinary('STOR check-sharing.php', f)

print('✅ Uploaded check-sharing.php')
print('🌐 Check: https://11klassniki.ru/check-sharing.php')

ftp.quit()