#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading minimal test...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    # Upload minimal test
    with open('post_test_minimal.php', 'rb') as f:
        ftp.storbinary('STOR post_test_minimal.php', f)
    print("✓ Uploaded post_test_minimal.php")
    
    # Check current htaccess
    print("\nChecking current .htaccess...")
    try:
        with open('htaccess_from_server.txt', 'wb') as f:
            ftp.retrbinary('RETR .htaccess', f.write)
        
        # Check if post rule exists
        with open('htaccess_from_server.txt', 'r') as f:
            content = f.read()
            if 'post/([^/]+)' in content:
                print("✓ Post rewrite rule found in .htaccess")
            else:
                print("✗ Post rewrite rule NOT found in .htaccess")
                
    except Exception as e:
        print(f"Could not check .htaccess: {e}")
    
    ftp.quit()
    print("\nTest with: https://11klassniki.ru/post_test_minimal.php?url_post=ledi-v-pogonah")
    
except Exception as e:
    print(f"Error: {e}")