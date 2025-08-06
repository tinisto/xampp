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

# Upload the debug script
print("\nUploading debug-category-posts.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/debug-category-posts.php', 'rb') as f:
    ftp.storbinary('STOR debug-category-posts.php', f)
print("✓ Uploaded debug-category-posts.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Run: https://11klassniki.ru/debug-category-posts.php")
print("This will show all categories and fix the assignment automatically")