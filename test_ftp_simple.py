#!/usr/bin/env python3
import ftplib

# Try simple connection test
try:
    ftp = ftplib.FTP('ftp.ipage.com')
    ftp.login('franko', 'JyvR!HK2E!N55Zt')
    print("✅ Connected!")
    ftp.quit()
except Exception as e:
    print(f"❌ Error: {e}")
    print("\nTrying alternative password...")
    try:
        ftp = ftplib.FTP('ftp.ipage.com')
        ftp.login('franko', 'Qazwsxedc123')
        print("✅ Connected with alternative password!")
        ftp.quit()
    except Exception as e2:
        print(f"❌ Alternative also failed: {e2}")