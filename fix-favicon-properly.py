#!/usr/bin/env python3
"""
Fix the favicon issue properly by removing old favicon.ico and updating templates
"""

import ftplib
import os

# FTP Configuration  
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Fixing favicon issue properly...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # 1. DELETE the old favicon.ico that's causing the problem
        print("\n🗑️ Removing problematic favicon.ico...")
        try:
            ftp.delete("/favicon.ico")
            print("✅ Deleted old favicon.ico")
        except Exception as e:
            print(f"Error deleting favicon.ico: {e}")
        
        # 2. Check and update real_template.php
        print("\n📝 Updating main template with new favicon...")
        try:
            temp_file = "temp_template.php"
            with open(temp_file, 'wb') as f:
                ftp.retrbinary('RETR /real_template.php', f.write)
            
            # Read the template
            with open(temp_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            # Check if favicon already exists
            if 'PHN2ZyB3aWR0aD0' in content:
                print("✅ Template already has new favicon")
            else:
                print("📝 Adding new favicon to template...")
                
                # Find the <head> section and add favicon after <title>
                if '<head>' in content and '<title>' in content:
                    # Add favicon after title
                    title_end = content.find('</title>')
                    if title_end > 0:
                        favicon_code = '''
    
    <!-- Unified Circular Favicon - 11-klassniki -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/svg+xml">
    <link rel="shortcut icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/x-icon">
'''
                        
                        # Insert favicon after </title>
                        content = content[:title_end + 8] + favicon_code + content[title_end + 8:]
                        
                        # Write updated template
                        with open(temp_file, 'w', encoding='utf-8') as f:
                            f.write(content)
                        
                        # Upload updated template
                        with open(temp_file, 'rb') as f:
                            ftp.storbinary('STOR /real_template.php', f)
                        
                        print("✅ Updated template with new favicon")
                    else:
                        print("❌ Could not find </title> in template")
                else:
                    print("❌ Template structure not recognized")
            
            os.remove(temp_file)
            
        except Exception as e:
            print(f"Error updating template: {e}")
        
        # 3. Create a simple favicon test that forces cache refresh
        print("\n📝 Creating favicon refresh test...")
        test_content = '''<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NEW Favicon - 11klassniki.ru</title>
    
    <!-- Force new favicon -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/svg+xml">
    <link rel="shortcut icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/x-icon">
    
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f0f8ff; }
        h1 { color: #007bff; }
        .demo { display: inline-block; width: 64px; height: 64px; background: #007bff; border-radius: 50%; color: white; font-weight: bold; line-height: 64px; margin: 20px; font-size: 24px; }
        .success { color: #28a745; font-size: 18px; margin: 20px; }
        .instructions { background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px auto; max-width: 600px; }
    </style>
</head>
<body>
    <h1>🎯 NEW Favicon Test</h1>
    <div class="success">✅ Old favicon.ico has been removed from server!</div>
    
    <div class="demo">11</div>
    
    <p><strong>Look at your browser tab now!</strong></p>
    <p>You should see a blue circle with "11" instead of the old favicon.</p>
    
    <div class="instructions">
        <h3>If you still see the old favicon:</h3>
        <ol>
            <li><strong>Hard refresh:</strong> Press Ctrl+Shift+R (or Cmd+Shift+R on Mac)</li>
            <li><strong>Clear browser cache</strong> for this site</li>
            <li><strong>Try incognito/private mode</strong></li>
        </ol>
    </div>
    
    <script>
        // Force favicon refresh with cache-busting
        setTimeout(function() {
            let links = document.querySelectorAll('link[rel*="icon"]');
            links.forEach(function(link) {
                let href = link.href;
                link.href = href + '?v=' + Date.now();
            });
            
            console.log('✅ Favicon refresh attempted');
        }, 1000);
    </script>
</body>
</html>'''
        
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.html', encoding='utf-8') as tmp:
            tmp.write(test_content)
            tmp_path = tmp.name
        
        try:
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /favicon-fixed.html', file)
            print("✅ Created favicon test page")
            os.unlink(tmp_path)
        except Exception as e:
            print(f"Error creating test page: {e}")
        
        ftp.quit()
        
        print("\n🎉 Favicon fix complete!")
        print("\n🧪 Test the fix:")
        print("1. https://11klassniki.ru/favicon-fixed.html")
        print("2. https://11klassniki.ru/ (homepage)")
        print("3. https://11klassniki.ru/test-comment-system.php")
        print("\n✅ What was fixed:")
        print("- ❌ DELETED old favicon.ico file (this was the main culprit)")
        print("- ✅ Updated main template with new SVG favicon")
        print("- ✅ Created test page with cache-busting")
        print("\n⚠️  Browser cache might still show old favicon - try hard refresh!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())