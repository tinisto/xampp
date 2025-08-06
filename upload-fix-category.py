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

# Upload the category assignment fix script
print("\nUploading fix-category-assignment.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/fix-category-assignment.php', 'rb') as f:
    ftp.storbinary('STOR fix-category-assignment.php', f)
print("✓ Uploaded fix-category-assignment.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Now run: https://11klassniki.ru/fix-category-assignment.php")
print("This will assign the posts to the proper 11-klassniki category")