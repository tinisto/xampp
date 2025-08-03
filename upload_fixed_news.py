#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading corrected news.php to server...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to the news directory
    ftp.cwd('/11klassnikiru/pages/common/news')
    print("✓ Changed to /11klassnikiru/pages/common/news")
    
    # Upload the corrected file
    with open('pages/common/news/news.php', 'rb') as f:
        ftp.storbinary('STOR news.php', f)
    print("✓ Uploaded corrected news.php")
    
    # Verify upload by checking file size
    size = ftp.size('news.php')
    print(f"✓ File uploaded, size: {size} bytes")
    
    ftp.quit()
    print("\n✅ Successfully uploaded the corrected news.php with simple category filters!")
    print("\nThe category pages should now work correctly:")
    print("- https://11klassniki.ru/news/novosti-vuzov (category 1)")
    print("- https://11klassniki.ru/news/novosti-spo (category 2)")
    print("- https://11klassniki.ru/news/novosti-shkol (category 3)")
    print("- https://11klassniki.ru/news/novosti-obrazovaniya (category 4)")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()