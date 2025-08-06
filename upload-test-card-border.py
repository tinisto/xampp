#!/usr/bin/env python3

import ftplib

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

# Upload updated test-card.php with borders
print("Uploading test-card.php with borders...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/test-card.php', 'rb') as f:
    ftp.storbinary('STOR common-components/test-card.php', f)
print("✓ Uploaded test-card.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Test cards now have borders matching news cards")