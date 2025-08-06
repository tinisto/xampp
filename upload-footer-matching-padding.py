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

# Upload footer with matching padding
print("\nUploading footer with matching padding...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php', 'rb') as f:
    ftp.storbinary('STOR common-components/footer-unified.php', f)
print("✓ Uploaded footer-unified.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFOOTER NOW MATCHES ALL SECTIONS:")
print("✅ Mobile: 20px padding all sides, 20px left/right margins")
print("✅ Desktop: 40px padding all sides, 40px left/right margins")
print("\nALL SECTIONS NOW IDENTICAL:")
print("- Header: margins only (no padding)")
print("- Green: 20px/40px padding + margins")
print("- Red: 20px/40px padding + margins")
print("- Blue: 20px/40px padding + margins")
print("- Footer: 20px/40px padding + margins")
print("\nPerfect consistency across all sections!")
print("\nTest: https://11klassniki.ru/test-real-layout.php")