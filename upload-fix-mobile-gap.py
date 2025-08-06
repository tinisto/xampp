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

# Upload page header without mobile gap
print("\nUploading page header without mobile gap...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header.php', 'rb') as f:
    ftp.storbinary('STOR common-components/page-section-header.php', f)
print("✓ Uploaded page-section-header.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFIXED MOBILE GAP:")
print("✅ Removed margin-bottom: 20px on mobile (768px)")
print("✅ Removed margin-bottom: 15px on small mobile (480px)")
print("✅ Now green header touches red content directly")
print("\nNo more gap between green and red on mobile!")
print("\nTest: https://11klassniki.ru/test-real-layout.php")