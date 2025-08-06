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

# Upload seo-head.php with FOUC fix
print("Uploading seo-head.php with flash prevention...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/seo-head.php', 'rb') as f:
    ftp.storbinary('STOR common-components/seo-head.php', f)
print("✓ Uploaded seo-head.php")

ftp.quit()
print("\n✓ Upload completed!")
print("White flash on page load should be eliminated")