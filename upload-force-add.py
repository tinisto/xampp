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

# Upload the force add script
print("\nUploading force-add-category.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/force-add-category.php', 'rb') as f:
    ftp.storbinary('STOR force-add-category.php', f)
print("✓ Uploaded force-add-category.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Run: https://11klassniki.ru/force-add-category.php")
print("This will show the table structure and force add the category")