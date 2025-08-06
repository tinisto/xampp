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

# Upload optimized template engine (disabled loading spinner)
print("\n1. Uploading optimized template engine...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php', 'rb') as f:
    ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
print("✓ Uploaded template-engine-ultimate.php")

# Upload optimized search bar (removed console logging)
print("\n2. Uploading optimized search bar...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php', 'rb') as f:
    ftp.storbinary('STOR common-components/search-bar.php', f)
print("✓ Uploaded search-bar.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Performance optimizations:")
print("- Disabled YouTube-style loading placeholders")
print("- Removed console.log statements")  
print("- Removed unnecessary error logging")
print("- Streamlined JavaScript execution")
print("\nThe site should load much faster now!")
print("Test: https://11klassniki.ru/")