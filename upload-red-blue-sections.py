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

# Upload template with red/blue sections
print("\nUploading template with red main content and blue comments section...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php', 'rb') as f:
    ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
print("✓ Uploaded template-engine-ultimate.php")

# Also upload content wrapper to ensure red background
print("\nUploading content wrapper with red background...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/content-wrapper.php', 'rb') as f:
    ftp.storbinary('STOR common-components/content-wrapper.php', f)
print("✓ Uploaded content-wrapper.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nVISUAL DEBUG SECTIONS:")
print("1. RED SECTION = Main content area")
print("   - Contains all page content")
print("   - Expands to fill space")
print("2. BLUE SECTION = Comments area") 
print("   - Below main content")
print("   - For comments (not on all pages)")
print("\nLayout structure:")
print("- Header (green)")
print("- Main Content (RED)")
print("- Comments (BLUE)")
print("- Footer")
print("\nTest: https://11klassniki.ru/")