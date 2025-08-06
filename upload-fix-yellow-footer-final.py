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
print("\nUploading test layout with final yellow fix...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFINAL FIX FOR YELLOW UNDER FOOTER:")
print("✅ Changed body background to match footer (#f8f9fa)")
print("✅ Created yellow wrapper for middle sections only")
print("✅ Header and footer outside yellow wrapper")
print("\nStructure now:")
print("- Body (gray background)")
print("  - Header (full width)")
print("  - Yellow wrapper")
print("    - Green section")
print("    - Red section")
print("    - Blue section")
print("  - Footer (full width, gray background)")
print("\nNo more yellow below footer!")
print("\nTest: https://11klassniki.ru/test-real-layout.php")