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

# Upload fixed search-process.php
print("\nUploading fixed search-process.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/search/search-process.php', 'rb') as f:
    ftp.storbinary('STOR pages/search/search-process.php', f)
print("✓ Uploaded pages/search/search-process.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Fixed search process features:")
print("- Proper error handling for database connection")
print("- Clean, modern design with dark mode support")
print("- Searches schools, posts, and news")
print("- Uses CSS variables for theming")
print("- No external dependencies causing errors")
print("- Fallback database connection if needed")
print("\nTest: https://11klassniki.ru/search-process?query=test")
print("Should now show proper search results page")