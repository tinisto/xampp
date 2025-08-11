#!/usr/bin/env python3
import ftplib

ftp = ftplib.FTP('ftp.ipage.com', timeout=30)
ftp.login('franko', 'JyvR!HK2E!N55Zt')
ftp.cwd('11klassnikiru')

# Upload check script
with open('check-db-config.php', 'rb') as f:
    ftp.storbinary('STOR check-db-config.php', f)

# Upload prod config
try:
    with open('config/prod-config.php', 'rb') as f:
        ftp.storbinary('STOR config/prod-config.php', f)
    print('✅ Uploaded prod-config.php')
except:
    print('❌ Could not upload prod-config.php')

print('✅ Uploaded check-db-config.php')
print('🌐 Check: https://11klassniki.ru/check-db-config.php')

ftp.quit()