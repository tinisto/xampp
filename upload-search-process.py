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

# Upload simple search-process.php
print("\nUploading simple search-process.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/search-process.php', 'rb') as f:
    ftp.storbinary('STOR search-process.php', f)
print("✓ Uploaded search-process.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Simple search process deployed:")
print("- Basic validation and sanitization")
print("- Redirects to /search page with query parameter")
print("- No complex dependencies")
print("- Handles empty/invalid queries")
print("\nTest: https://11klassniki.ru/search-process?query=test")
print("Should redirect to: https://11klassniki.ru/search?query=test")