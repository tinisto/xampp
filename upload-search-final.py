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

# Upload final search bar
print("\n1. Uploading final search-bar.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php', 'rb') as f:
    ftp.storbinary('STOR common-components/search-bar.php', f)
print("✓ Uploaded search-bar.php")

# Upload template back to use main search bar
print("\n2. Uploading template engine...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php', 'rb') as f:
    ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
print("✓ Uploaded template-engine-ultimate.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Final search bar deployed:")
print("- Clean, production-ready code")
print("- Red X button (like the working debug version)")
print("- No unnecessary !important rules")
print("- Simplified JavaScript")
print("- Uses ✕ character for better visibility")
print("\nTest: https://11klassniki.ru/")
print("The X button should work exactly like the debug version")