#!/usr/bin/env python3

import ftplib
import os

# Correct FTP credentials
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

# Connect to FTP
ftp = ftplib.FTP()
ftp.connect(HOST, 21)
ftp.login(USER, PASS)
ftp.cwd(PATH)

# Upload test-card.php
print("Uploading test-card.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/test-card.php', 'rb') as f:
    ftp.storbinary('STOR common-components/test-card.php', f)
print("✓ Uploaded test-card.php")

# Upload tests-main-content.php
print("Uploading tests-main-content.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/tests-main-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/tests-main-content.php', f)
print("✓ Uploaded tests-main-content.php")

ftp.quit()
print("\n✓ All files uploaded successfully!")
print("Check https://11klassniki.ru/tests")