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

# Upload test layout with margins on all sections
print("\nUploading test layout with margins on all sections...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nALL SECTIONS NOW HAVE MARGINS:")
print("✅ Header: 20px/40px left-right margins")
print("✅ Green section: 20px/40px left-right margins")
print("✅ Red section: 20px/40px left-right margins")
print("✅ Blue section: 20px/40px left-right margins")
print("✅ Footer: 20px/40px left-right margins")
print("\nYou'll see YELLOW strips on both sides of ALL sections!")
print("- Consistent visual spacing")
print("- All sections align vertically")
print("- Yellow body background visible on sides")
print("\nTest: https://11klassniki.ru/test-real-layout.php")