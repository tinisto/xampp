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

# Upload test layout with fixed footer margins
print("\nUploading test layout with fixed footer margins...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFIXED FOOTER DOUBLE MARGIN ISSUE:")
print("✅ Removed duplicate margins from .main-footer")
print("✅ Footer margins now only come from .unified-footer component")
print("✅ No more double margins!")
print("\nFooter now has same spacing as other sections:")
print("- Mobile: 20px padding + 20px margins")
print("- Desktop: 40px padding + 40px margins")
print("\nTest: https://11klassniki.ru/test-real-layout.php")