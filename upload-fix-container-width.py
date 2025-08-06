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

# Upload test layout with fixed container width
print("\nUploading test layout with fixed container width...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFIXED CONTAINER WIDTH:")
print("✅ Removed max-width: 1200px limitation")
print("✅ Container now uses full width minus padding")
print("✅ Targeted only .content and .comments-section containers")
print("\nNow you should see:")
print("- Mobile: 20px padding on ALL sides")
print("- Desktop: 40px padding on ALL sides")
print("- White boxes with consistent spacing from edges")
print("- No red bleeding beyond white borders")
print("\nTest: https://11klassniki.ru/test-real-layout.php")