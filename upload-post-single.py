#!/usr/bin/env python3
import ftplib

ftp = ftplib.FTP('ftp.ipage.com', timeout=30)
ftp.login('franko', 'JyvR!HK2E!N55Zt')
ftp.cwd('11klassnikiru')

with open('post-single.php', 'rb') as f:
    ftp.storbinary('STOR post-single.php', f)

print('✅ Updated post-single.php (removed WhatsApp)')
print('🌐 WhatsApp sharing button has been removed from posts')

ftp.quit()