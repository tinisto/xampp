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

# Upload test layout with visual padding indicators
print("\nUploading test layout with visual padding indicators...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nVISUAL PADDING INDICATORS ADDED:")
print("✅ Semi-transparent white background on content")
print("✅ White dashed border showing padding boundary")
print("✅ Labels showing padding values")
print("\nNow you can clearly see:")
print("- Red/blue backgrounds (full width)")
print("- White semi-transparent boxes (content area)")
print("- Space between box edge and screen edge = padding")
print("- Mobile: 20px gap all around")
print("- Desktop: 40px gap all around")
print("\nTest: https://11klassniki.ru/test-real-layout.php")