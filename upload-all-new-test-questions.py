#!/usr/bin/env python3

import ftplib
import os

# FTP credentials
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

# Test directories to create and upload
NEW_TESTS = [
    'literature-test',
    'computer-science-test',
    'social-studies-test',
    'economics-test',
    'financial-literacy-test',
    'time-management-test',
    'critical-thinking-test',
    'study-skills-test',
    'chinese-test',
    'italian-test',
    'japanese-test',
    'logic-reasoning-test',
    'memory-test',
    'creativity-test',
    'leadership-test'
]

print("Connecting to FTP server...")
ftp = ftplib.FTP()
ftp.connect(HOST, 21)
ftp.login(USER, PASS)
ftp.cwd(PATH)

# Create directories if needed
def create_dir_if_not_exists(path):
    try:
        ftp.mkd(path)
        print(f"✓ Created directory: {path}")
    except ftplib.error_perm:
        print(f"Directory already exists: {path}")

# Upload each test's questions.php if it exists
uploaded = 0
for test_dir in NEW_TESTS:
    local_file = f'/Applications/XAMPP/xamppfiles/htdocs/pages/tests/{test_dir}/questions.php'
    
    if os.path.exists(local_file):
        # Create directory
        create_dir_if_not_exists(f"pages/tests/{test_dir}")
        
        # Upload file
        print(f"\nUploading {test_dir}/questions.php...")
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR pages/tests/{test_dir}/questions.php', f)
        print(f"✓ Uploaded {test_dir}/questions.php")
        uploaded += 1
    else:
        print(f"⚠️  File not found: {test_dir}/questions.php")

ftp.quit()
print(f"\n✅ UPLOAD COMPLETED!")
print(f"Uploaded {uploaded} test question files")
print("\nRemember to create questions for tests that are missing!")
print("\nTest all new tests at: https://11klassniki.ru/tests")