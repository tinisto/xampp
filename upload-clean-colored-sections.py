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

# Upload test layout with clean colored sections
print("\nUploading test layout with clean colored sections...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nCLEAN COLORED SECTIONS:")
print("✅ Removed white boxes and content")
print("✅ Red section: empty with min-height 200px")
print("✅ Blue section: empty with min-height 150px")
print("✅ Both have padding and margins")
print("\nYou'll now see:")
print("- Pure RED section (no white box)")
print("- Pure BLUE section (no white box)")
print("- Yellow strips on sides (margins)")
print("- Clean visual layout structure")
print("\nTest: https://11klassniki.ru/test-real-layout.php")