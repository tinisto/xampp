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

# Upload updated test-card.php
print("Uploading updated test-card.php with buttons...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/test-card.php', 'rb') as f:
    ftp.storbinary('STOR common-components/test-card.php', f)
print("✓ Uploaded test-card.php")

# Create Spanish test directory
try:
    ftp.mkd('pages/tests/spanish-test')
    print("✓ Created spanish-test directory")
except:
    print("- Spanish test directory already exists")

# Upload Spanish test questions
print("Uploading Spanish test questions...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/spanish-test/questions.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/spanish-test/questions.php', f)
print("✓ Uploaded Spanish test questions")

ftp.quit()
print("\n✓ All files uploaded successfully!")
print("Check https://11klassniki.ru/tests")