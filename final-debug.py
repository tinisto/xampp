#!/usr/bin/env python3
"""
Final debug - check if files exist and their permissions
"""

import ftplib
import os

# FTP Configuration  
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 Final debug - checking file existence and permissions...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Check specific files we've been having trouble with
        files_to_check = [
            "test-comment-system.php",
            "dashboard-monitoring.php", 
            "favicon.ico",
            "real_template.php",
            "favicon-fixed.html"
        ]
        
        print("\n📋 File status check:")
        for filename in files_to_check:
            try:
                # Try to get file info using LIST command
                response = ftp.sendcmd(f'LIST /{filename}')
                print(f"✅ {filename}: {response}")
            except Exception as e:
                print(f"❌ {filename}: {str(e)}")
                
                # Try NLST to see if it exists
                try:
                    files = ftp.nlst(f"/{filename}")
                    if files:
                        print(f"   ↳ Found via NLST: {files}")
                except:
                    print(f"   ↳ Not found via NLST either")
        
        # Try to access the files via different methods
        print(f"\n🔧 Testing different access methods:")
        
        # Method 1: Direct FTP download
        print("Method 1: FTP download test-comment-system.php")
        try:
            with open("temp_test.php", "wb") as f:
                ftp.retrbinary("RETR /test-comment-system.php", f.write, blocksize=1024)
            
            size = os.path.getsize("temp_test.php")
            print(f"✅ Downloaded {size} bytes")
            
            # Check first few lines
            with open("temp_test.php", "r", encoding="utf-8", errors="ignore") as f:
                first_lines = [f.readline().strip() for _ in range(3)]
                print(f"First 3 lines: {first_lines}")
                
            os.remove("temp_test.php")
        except Exception as e:
            print(f"❌ Download failed: {e}")
            
        # Method 2: Check if it's a permissions issue
        print(f"\nMethod 2: Permissions check")
        try:
            # Try to change permissions
            ftp.sendcmd("SITE CHMOD 644 /test-comment-system.php")
            print("✅ Permissions changed to 644")
        except Exception as e:
            print(f"❌ Permissions change failed: {e}")
            
        # Method 3: Try uploading a fresh version
        print(f"\nMethod 3: Fresh upload test")
        test_content = '''<?php
echo "🧪 FRESH TEST FILE - " . date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fresh Test - 11klassniki.ru</title>
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbGNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/svg+xml">
</head>
<body>
    <h1>✅ Fresh upload successful!</h1>
    <p>New favicon should appear in tab (blue circle with "11")</p>
</body>
</html>'''
        
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.php', encoding='utf-8') as tmp:
            tmp.write(test_content)
            tmp_path = tmp.name
            
        try:
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /fresh-test.php', file)
            print("✅ Fresh file uploaded successfully")
            
            # Test if we can retrieve it
            try:
                response = ftp.sendcmd('LIST /fresh-test.php')
                print(f"✅ Fresh file verified: {response}")
            except:
                print("❌ Fresh file not accessible")
                
            os.unlink(tmp_path)
        except Exception as e:
            print(f"❌ Fresh upload failed: {e}")
            
        ftp.quit()
        
        print(f"\n📝 Summary:")
        print(f"Test these URLs to check if the new favicon appears:")
        print(f"1. https://11klassniki.ru/fresh-test.php (should have new favicon)")
        print(f"2. https://11klassniki.ru/favicon-fixed.html (cache-busting test)")
        print(f"\n🔍 If favicon still doesn't appear, the issue is likely:")
        print(f"- Browser cache (try incognito mode)")
        print(f"- Server-side caching")
        print(f"- DNS/CDN caching")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())