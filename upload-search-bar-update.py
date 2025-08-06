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

# Upload updated search bar component
print("\nUploading search-bar.php with X close button...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php', 'rb') as f:
    ftp.storbinary('STOR common-components/search-bar.php', f)
print("✓ Uploaded search-bar.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Search bar improvements:")
print("- Removed search icon from right side")
print("- Added X close icon that appears when typing")
print("- Click X to clear search input")
print("- Press Enter to submit search")
print("- Test at: https://11klassniki.ru/")