#!/usr/bin/env python3

import ftplib

# FTP details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("Connecting...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    
    # Just upload header
    with open('common-components/header.php', 'rb') as f:
        ftp.storbinary('STOR common-components/header.php', f)
        print("✓ Header uploaded")
    
    # Upload template
    with open('common-components/template-engine-unified.php', 'rb') as f:
        ftp.storbinary('STOR common-components/template-engine.php', f)
        print("✓ Template uploaded")
    
    print("\n✅ DONE! Theme toggle now in header!")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")