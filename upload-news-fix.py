#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_PATH = '/11klassnikiru/'

def main():
    print("📰 News Page Fix")
    print("=" * 20)
    print("Fixing undefined index error")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload fixed file
        with open('pages/common/news/news-data-fetch.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/news/news-data-fetch.php', f)
        print("✓ Uploaded: news-data-fetch.php")
        
        ftp.quit()
        
        print("=" * 20)
        print("✅ News page fixed!")
        print("")
        print("📝 Fixed issue:")
        print("   • Added null checks for meta fields")
        print("   • Uses title as fallback for meta description")
        print("")
        print("🔗 Test: https://11klassniki.ru/news/neobyichnyiy-otvet-habarovskoy-uchenitsyi-na-uroke-literaturyi")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()