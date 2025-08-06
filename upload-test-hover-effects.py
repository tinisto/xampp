#!/usr/bin/env python3

import ftplib
import os

# Correct FTP credentials
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

print("Connecting to FTP server...")
ftp = ftplib.FTP()
ftp.connect(HOST, 21)
ftp.login(USER, PASS)
ftp.cwd(PATH)

# Upload tests-main-content.php with hover effects
print("\nUploading tests-main-content.php with hover effects...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/tests-main-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/tests-main-content.php', f)
print("✓ Uploaded tests-main-content.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Test navigation buttons now have hover effects matching the news page")