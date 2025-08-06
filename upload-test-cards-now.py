#!/usr/bin/env python3

import ftplib
import os
from datetime import datetime

# Files to upload
files = [
    'common-components/test-card.php',
    'pages/tests/tests-main-content.php'
]

# FTP connection
ftp = ftplib.FTP('77.232.131.89')
ftp.login('8b6cdc76_sitearchive', 'jU9%mHr1')
ftp.cwd('/domains/11klassniki.ru/public_html')

print(f"Uploading test cards - {datetime.now()}")

for file_path in files:
    local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
    print(f"\nUploading {file_path}...")
    
    with open(local_file, 'rb') as f:
        ftp.storbinary(f'STOR {file_path}', f)
    print(f"✓ Uploaded {file_path}")

ftp.quit()
print("\n✓ All files uploaded successfully!")