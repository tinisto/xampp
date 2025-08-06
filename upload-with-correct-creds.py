#!/usr/bin/env python3

import ftplib
import os

# Correct FTP credentials
ftp = ftplib.FTP()
ftp.connect('77.232.131.89', 21)
ftp.login('franko', 'jU9%mHr1')  # Using correct username
ftp.cwd('/domains/11klassniki.ru/public_html')

# Upload test-card.php
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/test-card.php', 'rb') as f:
    ftp.storbinary('STOR common-components/test-card.php', f)
print("✓ Uploaded test-card.php")

# Upload tests-main-content.php
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/tests-main-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/tests-main-content.php', f)
print("✓ Uploaded tests-main-content.php")

ftp.quit()
print("\n✓ All files uploaded successfully!")