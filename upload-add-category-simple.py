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

# Upload the simple category addition script
print("\nUploading add-category-simple.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/add-category-simple.php', 'rb') as f:
    ftp.storbinary('STOR add-category-simple.php', f)
print("✓ Uploaded add-category-simple.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Now you can run: https://11klassniki.ru/add-category-simple.php")
print("This uses the existing database connection method")