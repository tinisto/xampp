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

# Upload content wrapper with red background
print("\nUploading content wrapper with red background...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/content-wrapper.php', 'rb') as f:
    ftp.storbinary('STOR common-components/content-wrapper.php', f)
print("✓ Uploaded content-wrapper.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nDEBUG: Main content area is now RED")
print("This helps visualize where all the content goes")
print("The red area is the main content wrapper that:")
print("- Expands to fill space between header and footer")
print("- Contains all page content")
print("- Uses flex: 1 to push footer down")
print("\nTest: https://11klassniki.ru/")