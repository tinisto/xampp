#!/usr/bin/env python3
import ftplib

ftp = ftplib.FTP('ftp.ipage.com', timeout=30)
ftp.login('franko', 'JyvR!HK2E!N55Zt')
ftp.cwd('11klassnikiru')

with open('test-connection.php', 'rb') as f:
    ftp.storbinary('STOR test-connection.php', f)

print('✅ Uploaded test-connection.php')
print('🌐 Check: https://11klassniki.ru/test-connection.php')

ftp.quit()