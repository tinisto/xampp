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

# Upload test-card.php with both buttons outlined
print("\nUploading test-card.php with both buttons outlined...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/test-card.php', 'rb') as f:
    ftp.storbinary('STOR common-components/test-card.php', f)
print("✓ Uploaded test-card.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Fixed issues:")
print("1. ✓ Both test buttons are now outlined style (not filled)")
print("2. ✓ X icon in search bar is more visible with background/border")
print("\nTest at:")
print("- https://11klassniki.ru/tests (both buttons outlined)")
print("- https://11klassniki.ru/ (search X icon)")