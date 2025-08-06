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

# Upload all updated files
files = [
    ('test-real-layout.php', 'test-real-layout.php'),
    ('common-components/header.php', 'common-components/header.php'),
    ('common-components/page-section-header.php', 'common-components/page-section-header.php'),
    ('common-components/footer-unified.php', 'common-components/footer-unified.php')
]

for local_file, remote_file in files:
    print(f"\nUploading {local_file}...")
    with open(f'/Applications/XAMPP/xamppfiles/htdocs/{local_file}', 'rb') as f:
        ftp.storbinary(f'STOR {remote_file}', f)
    print(f"✓ Uploaded {remote_file}")

ftp.quit()
print("\n✓ All uploads completed!")
print("\nCHANGES MADE:")
print("\n1. REDUCED MOBILE SPACING:")
print("   ✅ All margins: 20px → 10px (left/right)")
print("   ✅ Content padding: 20px → 10px (left/right)")
print("   ✅ Top/bottom spacing unchanged")
print("\n2. REMOVED WIDTH LIMITS:")
print("   ✅ Header: max-width removed")
print("   ✅ Footer: max-width removed")
print("\nMOBILE NOW HAS:")
print("- Narrower yellow strips (10px)")
print("- More content space")
print("- Better mobile experience")
print("\nTest: https://11klassniki.ru/test-real-layout.php")