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

# Upload header with improved dropdown close functionality
print("\nUploading header.php with improved dropdown close behavior...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/header.php', 'rb') as f:
    ftp.storbinary('STOR common-components/header.php', f)
print("✓ Uploaded header.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Dropdown will now close when clicking outside or pressing Escape key")
print("Test on: https://11klassniki.ru/category/a-naposledok-ya-skazhu")