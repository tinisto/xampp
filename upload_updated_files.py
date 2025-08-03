#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading updated files...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Upload main page posts checker
    ftp.cwd('/11klassnikiru')
    with open('check_main_page_posts.php', 'rb') as f:
        ftp.storbinary('STOR check_main_page_posts.php', f)
    print("✓ Uploaded check_main_page_posts.php")
    
    # Upload updated post.php with logging
    ftp.cwd('/11klassnikiru/pages/post')
    with open('pages/post/post-working.php', 'rb') as f:
        ftp.storbinary('STOR post.php', f)
    print("✓ Uploaded post.php with error logging")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/check_main_page_posts.php")
    print("This will show all posts that appear on the main page")
    
except Exception as e:
    print(f"Error: {e}")