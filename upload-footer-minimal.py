#!/usr/bin/env python3

import ftplib

# FTP credentials
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

print("Connecting to FTP server...")
ftp = ftplib.FTP()
ftp.connect(HOST, 21)
ftp.login(USER, PASS)
ftp.cwd(PATH)

# Upload minimal footer with only "О проекте"
print("\nUploading footer-unified.php with minimal navigation...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php', 'rb') as f:
    ftp.storbinary('STOR common-components/footer-unified.php', f)
print("✓ Uploaded footer-unified.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Footer now only shows 'О проекте' link")