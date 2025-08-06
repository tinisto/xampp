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

# Upload test layout with final fix
print("\nUploading test layout with overscroll fix...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFIXED OVERSCROLL ISSUES:")
print("✅ Dark background (#212529) for overscroll areas")
print("✅ White header background")
print("✅ Light footer background (#f8f9fa)")
print("✅ Removed scrollbars with overflow: hidden")
print("\nNo more white showing under footer!")
print("\nTest: https://11klassniki.ru/test-real-layout.php")