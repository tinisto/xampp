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

# Upload improved search bar with visible X icon
print("\nUploading search-bar.php with more visible X icon...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php', 'rb') as f:
    ftp.storbinary('STOR common-components/search-bar.php', f)
print("✓ Uploaded search-bar.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Search bar X icon improvements:")
print("- Added gray background for better visibility")
print("- Added border for contrast")
print("- Red background on hover")
print("- Better dark mode support")
print("- Debug console logs added")
print("- Error handling for missing elements")
print("\nTest: https://11klassniki.ru/ - Type in search to see X icon")
print("Check browser console for debug messages")