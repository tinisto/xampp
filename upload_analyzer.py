#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading HTML entities analyzer...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('analyze_html_entities.php', 'rb') as f:
        ftp.storbinary('STOR analyze_html_entities.php', f)
    print("✓ Uploaded analyze_html_entities.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/analyze_html_entities.php")
    print("\nThis will show:")
    print("- Which posts have HTML entities")
    print("- What entities are most common")
    print("- Preview of what cleaning would look like")
    
except Exception as e:
    print(f"Error: {e}")