#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Simple test to understand server behavior
"""

import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create a very simple test file
        test_content = '''<!DOCTYPE html>
<html>
<head><title>SIMPLE TEST</title></head>
<body style="background: red; color: white; text-align: center; padding: 50px;">
<h1>SIMPLE TEST PAGE WORKING</h1>
<p>If you see this, the file uploaded correctly</p>
<p>No header, no footer, just this content</p>
</body>
</html>'''
        
        # Upload test file
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(test_content)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR /simple-test.html', file)
        
        os.unlink(tmp_path)
        print("✅ Created simple-test.html")
        
        # List files to see what's actually on the server
        print("\n📁 Files in root directory:")
        files = []
        ftp.retrlines('LIST', files.append)
        for file_info in files:
            if 'standalone' in file_info or 'login' in file_info or 'test' in file_info:
                print(f"  {file_info}")
        
        ftp.quit()
        print("\n🧪 Test URL: https://11klassniki.ru/simple-test.html")
        print("This should show a red page with white text if upload works")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    main()