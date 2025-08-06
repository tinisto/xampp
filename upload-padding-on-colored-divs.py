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

# Upload test layout with padding on colored divs
print("\nUploading test layout with padding on colored divs...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nPADDING NOW ON COLORED DIVS:")
print("✅ Padding moved to .content (RED div)")
print("✅ Padding moved to .comments-section (BLUE div)")
print("✅ Container has no padding (just shows content)")
print("\nNow the structure is:")
print("- RED div has 20px/40px padding")
print("- White border shows actual content area")
print("- Red won't extend beyond white border")
print("- Proper spacing from screen edges")
print("\nTest: https://11klassniki.ru/test-real-layout.php")