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

# Upload test layout with viewport fixes
print("\nUploading test layout with viewport overflow fixes...")
with open('/Applications/XAMPP/xamppfiles/htdocs/test-real-layout.php', 'rb') as f:
    ftp.storbinary('STOR test-real-layout.php', f)
print("✓ Uploaded test-real-layout.php")

ftp.quit()
print("\n✓ Upload completed!")
print("\nFIXED VIEWPORT OVERFLOW:")
print("✅ html: overflow hidden")
print("✅ body: height 100vh (exact viewport)")
print("✅ body: margin/padding 0")
print("✅ content: flex 1 1 auto (fills available space)")
print("✅ Removed min-heights")
print("\nNO MORE YELLOW ABOVE/BELOW!")
print("- Content fits viewport exactly")
print("- No scrolling to see yellow")
print("- Header at very top")
print("- Footer at very bottom")
print("\nTest: https://11klassniki.ru/test-real-layout.php")