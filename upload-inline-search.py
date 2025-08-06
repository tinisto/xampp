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

# Upload template with inline search bar
print("\nUploading template with inline search bar (no external JS)...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php', 'rb') as f:
    ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
print("✓ Uploaded template-engine-ultimate.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Inline search bar solution:")
print("- No external JavaScript files")
print("- Simple inline oninput/onclick handlers")
print("- No CSS conflicts")
print("- Red X button with ✕ character")
print("- Avoids all JavaScript syntax errors")
print("\nTest: https://11klassniki.ru/")
print("The search bar should work without any JS errors")