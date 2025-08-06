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

# Upload search-process.php with template system
print("\n1. Uploading search-process.php with proper template...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/search/search-process.php', 'rb') as f:
    ftp.storbinary('STOR pages/search/search-process.php', f)
print("✓ Uploaded pages/search/search-process.php")

# Upload search process content
print("\n2. Uploading search-process-content.php...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/search/search-process-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/search/search-process-content.php', f)
print("✓ Uploaded pages/search/search-process-content.php")

# Upload footer with restored links
print("\n3. Uploading footer with Privacy/Terms links...")
with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php', 'rb') as f:
    ftp.storbinary('STOR common-components/footer-unified.php', f)
print("✓ Uploaded footer-unified.php")

ftp.quit()
print("\n✓ Upload completed!")
print("Fixed issues:")
print("1. ✅ Search page now uses proper template system")
print("   - Unified header and footer")
print("   - Consistent styling and dark mode")
print("   - Bootstrap/custom CSS framework")
print("2. ✅ Footer restored with missing links")
print("   - О проекте")
print("   - Связаться с нами") 
print("   - Конфиденциальность")
print("   - Условия использования")
print("\nTest: https://11klassniki.ru/search-process?query=test")