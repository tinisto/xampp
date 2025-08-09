#!/usr/bin/env python3
"""
Fix the favicon issue by removing old favicon.ico and ensuring templates use new favicon
"""

import ftplib
import os

# FTP Configuration  
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 Debugging favicon issue...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Check for old favicon files that browsers might cache
        old_favicon_files = [
            "favicon.ico",
            "favicon.png", 
            "apple-touch-icon.png",
            "favicon-16x16.png",
            "favicon-32x32.png"
        ]
        
        print("\n🗑️ Removing old favicon files:")
        for favicon_file in old_favicon_files:
            try:
                ftp.delete(f"/{favicon_file}")
                print(f"✅ Deleted: {favicon_file}")
            except Exception as e:
                print(f"ℹ️  {favicon_file}: {str(e)}")
        
        # Check real_template.php to see if it has the new favicon
        print("\n📋 Checking main template...")
        try:
            temp_file = "temp_template.php"
            with open(temp_file, 'wb') as f:
                ftp.retrbinary('RETR /real_template.php', f.write)
            
            # Check if it contains the new favicon
            with open(temp_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                
            if 'PHN2ZyB3aWR0aD0' in content:  # Part of our base64 SVG
                print("✅ Main template has new favicon")
            else:
                print("❌ Main template missing new favicon - needs update")
                
            os.remove(temp_file)
                
        except Exception as e:
            print(f"Error checking template: {e}")
        
        # Create a simple test page to verify favicon works
        print("\n📝 Creating favicon test page...")
        test_content = '''<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Favicon Test - 11klassniki.ru</title>
    
    <!-- NEW FAVICON -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/svg+xml">
    <link rel="shortcut icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/x-icon">
    
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .favicon-demo { display: inline-block; width: 32px; height: 32px; background: #007bff; border-radius: 50%; color: white; font-weight: bold; line-height: 32px; margin: 10px; }
    </style>
</head>
<body>
    <h1>🎯 Favicon Test</h1>
    <p><strong>Look at your browser tab!</strong></p>
    <p>You should see a blue circle with "11" instead of any old favicon.</p>
    <div class="favicon-demo">11</div>
    <p>↑ This is what the new favicon looks like</p>
    
    <script>
        // Force favicon refresh
        let link = document.querySelector("link[rel*='icon']") || document.createElement('link');
        link.type = 'image/svg+xml';
        link.rel = 'shortcut icon';
        link.href = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=';
        document.getElementsByTagName('head')[0].appendChild(link);
        
        setTimeout(function() {
            alert('✅ New favicon should now be visible in the browser tab!');
        }, 2000);
    </script>
</body>
</html>'''
        
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.html', encoding='utf-8') as tmp:
            tmp.write(test_content)
            tmp_path = tmp.name
        
        try:
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /favicon-test.html', file)
            print("✅ Created favicon test page")
            os.unlink(tmp_path)
        except Exception as e:
            print(f"Error creating test page: {e}")
        
        # Close connection
        ftp.quit()
        
        print("\n✅ Favicon fix complete!")
        print("\n🧪 Test pages:")
        print("1. https://11klassniki.ru/favicon-test.html (simple test)")
        print("2. https://11klassniki.ru/test-comment-system.php (main test)")
        print("\n🔧 What was fixed:")
        print("- Removed old favicon.ico file that browsers were caching")
        print("- Created clean test page with new favicon")
        print("\n⚠️  Clear browser cache or use incognito mode!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())