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

# Upload improved search design
print("\nUploading improved search bar design...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php', 'rb') as f:
    ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
print("✓ Uploaded template-engine-ultimate.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Improved search bar design:")
print("- ✕ No circle background - clean flat design")
print("- ✕ No red color - uses theme colors")
print("- ✓ Transparent background with subtle hover")
print("- ✓ Uses CSS variables for dark mode support")
print("- ✓ Smaller, more elegant 24px size")
print("- ✓ Smooth transitions")
print("- ✓ Focus border color matches site theme")
print("\nTest: https://11klassniki.ru/")
print("The X icon should look much cleaner and work in both light/dark modes")