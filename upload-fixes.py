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

# Upload fixed files
files = [
    'common-components/content-wrapper.php',
    'pages/tests/tests-main-content.php'
]

for file_path in files:
    print(f"Uploading {file_path}...")
    with open(f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}', 'rb') as f:
        ftp.storbinary(f'STOR {file_path}', f)
    print(f"✓ Uploaded {file_path}")

ftp.quit()
print("\n✓ All files uploaded successfully!")