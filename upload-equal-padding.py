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

# Upload test layout with equal padding
print("\nUploading test layout with equal padding...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nPADDING CHANGES:")
print("Mobile (< 769px):")
print("- Content (red): 20px on all sides")
print("- Comments (blue): 20px on all sides")
print("\nDesktop (≥ 769px):")
print("- Content (red): 40px on all sides")
print("- Comments (blue): 40px on all sides")
print("\nEqual padding on top/bottom/left/right!")
print("\nTest: https://11klassniki.ru/test-real-layout.php")