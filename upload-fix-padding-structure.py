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

# Upload test layout with fixed padding
print("\nUploading test layout with fixed padding structure...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFIXED PADDING STRUCTURE:")
print("✅ Red/blue backgrounds extend full width")
print("✅ Padding moved to .container elements")
print("✅ Mobile: 20px padding inside containers")
print("✅ Desktop: 40px padding inside containers")
print("\nNow you'll see:")
print("- Red background touches edges")
print("- Blue background touches edges")
print("- White space (padding) inside each section")
print("\nTest: https://11klassniki.ru/test-real-layout.php")