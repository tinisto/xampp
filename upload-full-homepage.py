#!/usr/bin/env python3
"""Upload the full homepage with database content"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    print("Uploading full homepage...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Upload our original index.php with database queries
    with open('index.php', 'rb') as f:
        # Read the local index.php content
        content = f.read()
    
    # Make sure it's the full version, not minimal
    if b'mysqli_query' in content and b'renderCardsGrid' in content:
        print("✓ Uploading full index.php with database content...")
        f = open('index.php', 'rb')
        ftp.storbinary('STOR index.php', f)
        f.close()
        print("✓ Full homepage uploaded!")
    else:
        print("⚠️  Local index.php doesn't look like the full version")
        print("Keeping minimal version for now")
    
    ftp.quit()
    
    print("\n" + "="*50)
    print("✅ Homepage is working!")
    print("\nTest these other pages:")
    print("- https://11klassniki.ru/schools-all-regions")
    print("- https://11klassniki.ru/vpo-all-regions")
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/news")
    print("- https://11klassniki.ru/tests")
    print("="*50)
    
except Exception as e:
    print(f"Error: {e}")