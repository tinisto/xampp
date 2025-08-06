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

# Upload updated test-card.php with smaller buttons
print("Uploading updated test-card.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/test-card.php', 'rb') as f:
    ftp.storbinary('STOR common-components/test-card.php', f)
print("✓ Uploaded test-card.php")

# Create and upload test directories
tests_to_upload = [
    ('french-test', 'pages/tests/french-test/questions.php'),
    ('german-test', 'pages/tests/german-test/questions.php'),
    ('english-test', 'pages/tests/english-test/questions.php')
]

for test_name, file_path in tests_to_upload:
    # Create directory
    try:
        ftp.mkd(f'pages/tests/{test_name}')
        print(f"✓ Created {test_name} directory")
    except:
        print(f"- {test_name} directory already exists")
    
    # Upload questions file
    local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
    if os.path.exists(local_file):
        print(f"Uploading {test_name} questions...")
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {file_path}', f)
        print(f"✓ Uploaded {test_name} questions")

ftp.quit()
print("\n✓ All files uploaded successfully!")
print("Check https://11klassniki.ru/tests")