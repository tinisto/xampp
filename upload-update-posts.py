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

# Upload the update posts script
print("\nUploading update-posts-to-category-21.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/update-posts-to-category-21.php', 'rb') as f:
    ftp.storbinary('STOR update-posts-to-category-21.php', f)
print("✓ Uploaded update-posts-to-category-21.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Run: https://11klassniki.ru/update-posts-to-category-21.php")
print("This will move all posts from category 1 to category 21 (11-klassniki)")