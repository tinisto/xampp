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

# Upload the updated homepage with correct category ID
print("\nUploading index_content_posts_with_news_style.php with category = 21...")
with open('/Applications/XAMPP/xamppfiles/htdocs/index_content_posts_with_news_style.php', 'rb') as f:
    ftp.storbinary('STOR index_content_posts_with_news_style.php', f)
print("✓ Uploaded index_content_posts_with_news_style.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Homepage now uses correct category = 21 for 11-klassniki posts")
print("Check: https://11klassniki.ru/ and https://11klassniki.ru/category/11-klassniki")