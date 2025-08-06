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

# Upload updated header that excludes current category
print("\nUploading header.php with current category exclusion...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/header.php', 'rb') as f:
    ftp.storbinary('STOR common-components/header.php', f)
print("✓ Uploaded header.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Header dropdown will now exclude the current category")
print("When on /category/11-klassniki, '11-классники' won't appear in dropdown")