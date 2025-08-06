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
    print("🔧 Post Page Header Fix")
    print("=" * 30)
    print("Moving author/date/views to header badge")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Files to upload
        files = [
            ('pages/post/post.php', 'pages/post/post.php'),
            ('pages/post/post-content-professional.php', 'pages/post/post-content-professional.php')
        ]
        
        for local_file, remote_file in files:
            with open(local_file, 'rb') as f:
                ftp.storbinary(f'STOR {remote_file}', f)
            print(f"✓ Uploaded: {local_file}")
        
        ftp.quit()
        
        print("=" * 30)
        print("✅ Post header fixed!")
        print("📝 Changes:")
        print("   • Removed duplicate title")
        print("   • Moved author/date/views to header badge")
        print("   • Admin buttons repositioned")
        print("")
        print("🔗 Test: https://11klassniki.ru/post/ledi-v-pogonah")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()