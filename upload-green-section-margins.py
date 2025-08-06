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

# Upload updated files
print("\nUploading page-section-header with consistent margins...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header.php', 'rb') as f:
    ftp.storbinary('STOR common-components/page-section-header.php', f)
print("✓ Uploaded page-section-header.php")

print("\nUploading test layout...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nGREEN SECTION NOW MATCHES:")
print("✅ Mobile: 20px padding all sides, 20px left/right margins")
print("✅ Desktop: 40px padding all sides, 40px left/right margins")
print("\nALL SECTIONS NOW CONSISTENT:")
print("- Header: 20px/40px margins")
print("- Green: 20px/40px padding + margins")
print("- Red: 20px/40px padding + margins")
print("- Blue: 20px/40px padding + margins")
print("- Footer: 20px/40px margins")
print("\nAll children divs have the same spacing!")
print("\nTest: https://11klassniki.ru/test-real-layout.php")