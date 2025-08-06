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

# Upload test layout with yellow background and margins
print("\nUploading test layout with yellow background and margins...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nCHANGES MADE:")
print("✅ Body background = YELLOW")
print("✅ Header: 20px margins (mobile) / 40px (desktop)")
print("✅ Green section: 20px margins (mobile) / 40px (desktop)")
print("✅ Footer: 20px margins (mobile) / 40px (desktop)")
print("\nYou'll now see:")
print("- Yellow strips on left/right of header, green section, and footer")
print("- Red and blue sections still full width")
print("- Consistent margins creating visual separation")
print("\nTest: https://11klassniki.ru/test-real-layout.php")