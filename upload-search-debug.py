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

# Upload debug search bar component
print("\n1. Uploading debug search-bar component...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar-debug.php', 'rb') as f:
    ftp.storbinary('STOR common-components/search-bar-debug.php', f)
print("✓ Uploaded search-bar-debug.php")

# Upload updated template engine
print("\n2. Uploading updated template engine...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php', 'rb') as f:
    ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
print("✓ Uploaded template-engine-ultimate.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Debug search bar deployed:")
print("- Simple, aggressive implementation")
print("- Red X button that's always visible")
print("- Detailed console logging")
print("- No CSS conflicts")
print("- Unique IDs and classes")
print("\nTest: https://11klassniki.ru/")
print("The X button should be clearly visible when typing")
print("Check browser console for detailed debug messages")