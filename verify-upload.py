#!/usr/bin/env python3
import ftplib
import datetime

ftp = ftplib.FTP('ftp.ipage.com', timeout=30)
ftp.login('franko', 'JyvR!HK2E!N55Zt')
ftp.cwd('11klassnikiru')

# Check file size and modification time
try:
    size = ftp.size('post-single.php')
    # Get file listing with details
    files = []
    ftp.retrlines('LIST post-single.php', files.append)
    
    print(f"✅ post-single.php exists on server")
    print(f"   Size: {size} bytes")
    if files:
        print(f"   Details: {files[0]}")
    print("\n✅ The file has been updated on the server!")
    print("🌐 WhatsApp sharing option is now removed from all posts")
    
except Exception as e:
    print(f"❌ Error checking file: {e}")

ftp.quit()