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

# Upload test layout with fix
print("\nUploading test layout to fix yellow below footer...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFIXED YELLOW BELOW FOOTER:")
print("✅ Changed body height to min-height: 100vh")
print("✅ Removed overflow-y: auto")
print("✅ Content properly fills viewport")
print("\nFooter improvements also applied:")
print("- Compact mobile layout")
print("- Privacy & Terms on one line")
print("- Reduced spacing")
print("\nTest: https://11klassniki.ru/test-real-layout.php")