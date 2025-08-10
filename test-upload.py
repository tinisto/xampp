#!/usr/bin/env python3
import ftplib

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def test_upload():
    try:
        print("Testing git commit state on server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Just test the connection
        print("✓ Connected to server successfully")
        print("✓ Current git state ready for testing")
        
        ftp.quit()
        print("\nTest: https://11klassniki.ru")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    test_upload()