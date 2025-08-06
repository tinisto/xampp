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
files = [
    ('test-real-layout.php', 'test-real-layout.php'),
    ('common-components/footer-unified.php', 'common-components/footer-unified.php')
]

for local_file, remote_file in files:
    print(f"\nUploading {local_file}...")
    with open(f'/Applications/XAMPP/xamppfiles/htdocs/{local_file}', 'rb') as f:
        ftp.storbinary(f'STOR {remote_file}', f)
    print(f"✓ Uploaded {remote_file}")

ftp.quit()
print("\n✓ All uploads completed!")
print("\nHEADER/FOOTER SPACING REMOVED:")
print("✅ Header: NO margins, NO padding")
print("✅ Footer: NO margins, minimal padding for content")
print("\nRESULT:")
print("- Header touches screen edges (no yellow)")
print("- Footer touches screen edges (no yellow)")
print("- Green, Red, Blue sections still have margins")
print("\nLayout structure:")
print("- Header (full width)")
print("- Green (with margins)")
print("- Red (with margins)")
print("- Blue (with margins)")
print("- Footer (full width)")
print("\nTest: https://11klassniki.ru/test-real-layout.php")