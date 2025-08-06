#!/usr/bin/env python3

import ftplib

# FTP credentials
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

print("Connecting to FTP server...")
ftp = ftplib.FTP()
ftp.connect(HOST, 21)
ftp.login(USER, PASS)
ftp.cwd(PATH)

# Upload test layout with clear padding visualization
print("\nUploading test layout with clear padding visualization...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nCLEAR PADDING VISUALIZATION:")
print("✅ White boxes = actual content")
print("✅ Red area around white box = padding (20px mobile, 40px desktop)")
print("✅ Blue area around white box = padding (20px mobile, 40px desktop)")
print("\nThe colored areas you see are the PADDING!")
print("- RED padding surrounds the main content")
print("- BLUE padding surrounds the comments")
print("\nTest: https://11klassniki.ru/test-real-layout.php")