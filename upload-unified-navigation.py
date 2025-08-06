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

# Upload the reusable category navigation component
print("\nUploading reusable category-navigation.php component...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/category-navigation.php', 'rb') as f:
    ftp.storbinary('STOR common-components/category-navigation.php', f)
print("✓ Uploaded category-navigation.php")

# Upload updated news content with reusable navigation
print("\nUploading news-content.php with unified navigation...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/common/news/news-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/common/news/news-content.php', f)
print("✓ Uploaded news-content.php")

# Upload updated news main content
print("\nUploading news-main-content.php with unified navigation...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/news/news-main-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/news/news-main-content.php', f)
print("✓ Uploaded news-main-content.php")

# Upload updated tests content with unified navigation
print("\nUploading tests-main-content.php with unified navigation...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/tests-main-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/tests-main-content.php', f)
print("✓ Uploaded tests-main-content.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Both news and tests now use the same reusable navigation component with hover effects")
print("Test at:")
print("- https://11klassniki.ru/news")
print("- https://11klassniki.ru/tests")