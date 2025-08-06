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

# 1. Upload footer with "Связаться с нами" restored
print("\n1. Uploading footer-unified.php with 'Связаться с нами' restored...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php', 'rb') as f:
    ftp.storbinary('STOR common-components/footer-unified.php', f)
print("✓ Uploaded footer-unified.php")

# 2. Upload news page with unified navigation (hover effects)
print("\n2. Uploading news.php with unified navigation hover effects...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/common/news/news.php', 'rb') as f:
    ftp.storbinary('STOR pages/common/news/news.php', f)
print("✓ Uploaded news.php")

# 3. Upload improved search bar with better X icon
print("\n3. Uploading search-bar.php with improved X icon...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php', 'rb') as f:
    ftp.storbinary('STOR common-components/search-bar.php', f)
print("✓ Uploaded search-bar.php")

ftp.quit()
print("\n✓ All fixes uploaded!")
print("\nFixed issues:")
print("1. ✓ Restored 'Связаться с нами' to footer")
print("2. ✓ Fixed news navigation hover effects (now using reusable component)")
print("3. ✓ Improved X icon visibility in search bar")
print("\nTest at:")
print("- https://11klassniki.ru/ (search X icon)")
print("- https://11klassniki.ru/news (hover effects)")
print("- Footer (Связаться с нами link)")