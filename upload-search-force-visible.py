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

# Upload search bar with forced visibility
print("\nUploading search-bar.php with forced X icon visibility...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php', 'rb') as f:
    ftp.storbinary('STOR common-components/search-bar.php', f)
print("✓ Uploaded search-bar.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Enhanced X icon visibility:")
print("- Added !important to all CSS rules")
print("- Increased z-index to 999999")
print("- Made X button larger (32px)")
print("- Added stronger background opacity")
print("- Force styles via JavaScript")
print("- Added console logging for debugging")
print("\nTest: https://11klassniki.ru/ - Type in search to see X icon")
print("Check browser console for debug messages")