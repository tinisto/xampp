#!/usr/bin/env python3
import ftplib
import os
from io import BytesIO

# FTP credentials - Using the same as in the working upload script
ftp = ftplib.FTP('ftp.ipage.com')
ftp.login('franko', 'JyvR!HK2E!N55Zt')
ftp.encoding = 'utf-8'

print("Connected to FTP server")

# Navigate to web root
try:
    ftp.cwd('/public_html')
except:
    try:
        ftp.cwd('/domains/11klassniki.ru/public_html')
    except:
        print("Current directory:", ftp.pwd())

# Files to upload
files = [
    ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php',
     '/Applications/XAMPP/xamppfiles/htdocs/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php'),
    ('common-components/header-modern.php',
     '/Applications/XAMPP/xamppfiles/htdocs/common-components/header-modern.php'),
    ('common-components/template-engine-ultimate.php',
     '/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php')
]

for remote_path, local_path in files:
    try:
        with open(local_path, 'rb') as f:
            content = f.read()
        
        bio = BytesIO(content)
        ftp.storbinary(f'STOR {remote_path}', bio)
        print(f"✓ Uploaded: {remote_path}")
    except Exception as e:
        print(f"✗ Failed: {remote_path} - {str(e)}")

ftp.quit()
print("\nDeployment complete!")
print("\nFixed issues:")
print("1. /spo-all-regions page should now display content")
print("2. Theme toggle should work on /news and /tests pages")
print("3. Theme toggle icon should be inline with user avatar")