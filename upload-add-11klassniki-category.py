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

# Upload the script to add the category
print("\nUploading add-category-direct.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/add-category-direct.php', 'rb') as f:
    ftp.storbinary('STOR add-category-direct.php', f)
print("✓ Uploaded add-category-direct.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Now you can run: https://11klassniki.ru/add-category-direct.php")
print("This will add the missing '11-классники' category to the database")