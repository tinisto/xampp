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

# Upload footer with mobile improvements
print("\nUploading footer with mobile improvements...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php', 'rb') as f:
    ftp.storbinary('STOR common-components/footer-unified.php', f)
print("✓ Uploaded footer-unified.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFOOTER MOBILE IMPROVEMENTS:")
print("✅ Reduced padding: 10px vertical, 15px horizontal")
print("✅ Tighter spacing between elements (8px gap)")
print("✅ All nav links on one line (no wrap)")
print("✅ Smaller font size (13px)")
print("✅ Hidden 'О проекте' to save space")
print("\nFooter is now more compact on mobile!")
print("\nTest: https://11klassniki.ru/test-real-layout.php")