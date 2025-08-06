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
    print("🔧 Final VPO Region Fix")
    print("=" * 25)
    print("Fixing column name in function-query.php")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload both files
        files = [
            ('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 
             'pages/common/educational-institutions-in-region/educational-institutions-in-region.php'),
            ('pages/common/educational-institutions-in-region/function-query.php',
             'pages/common/educational-institutions-in-region/function-query.php')
        ]
        
        for local_file, remote_file in files:
            with open(local_file, 'rb') as f:
                ftp.storbinary(f'STOR {remote_file}', f)
            print(f"✓ Uploaded: {local_file.split('/')[-1]}")
        
        ftp.quit()
        
        print("=" * 25)
        print("✅ Fix complete!")
        print("📝 Changes:")
        print("   • VPO table uses 'region_id' column")
        print("   • SPO/Schools use 'id_region' column")
        print("   • Using simpler direct version")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()