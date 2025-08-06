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
    print("🔧 Remove Post Navigation Links")
    print("=" * 32)
    print("Removing post type navigation from post pages")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload the updated file
        with open('pages/post/post-content-professional.php', 'rb') as f:
            ftp.storbinary('STOR pages/post/post-content-professional.php', f)
        print("✓ Uploaded: post-content-professional.php")
        
        ftp.quit()
        
        print("=" * 32)
        print("✅ Post navigation removed!")
        print("📝 Removed:")
        print("   • Все посты")
        print("   • Образование")
        print("   • Карьера")
        print("   • Студенческая жизнь")
        print("")
        print("🔗 Test: https://11klassniki.ru/post/kogda-ege-ostalis-pozadi")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()