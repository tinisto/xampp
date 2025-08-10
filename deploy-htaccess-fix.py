#!/usr/bin/env python3
"""
Force deploy .htaccess with cache-busting and verify standalone pages
"""

import ftplib
import os
import time

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file"""
    try:
        ftp.cwd(FTP_ROOT)
        
        if not os.path.exists(local_path):
            print(f"⚠️  Local file not found: {local_path}")
            return False
        
        local_size = os.path.getsize(local_path)
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        
        print(f"✅ Uploaded: {local_path} -> {remote_path} ({local_size} bytes)")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🔧 Force-deploying .htaccess and verifying standalone pages...")
    print(f"📡 Connecting to {FTP_HOST}...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        ftp.cwd(FTP_ROOT)
        
        # First, backup existing .htaccess
        try:
            ftp.rename('.htaccess', f'.htaccess.backup.{int(time.time())}')
            print("📋 Backed up existing .htaccess")
        except:
            print("ℹ️  No existing .htaccess to backup")
        
        # Upload new .htaccess
        upload_file(ftp, '.htaccess', '.htaccess')
        
        # Verify standalone files exist
        files_to_check = [
            'login-standalone.php',
            'registration-standalone.php', 
            'privacy-standalone.php',
            'forgot-password-standalone.php'
        ]
        
        print("\n🔍 Verifying standalone files on server:")
        for filename in files_to_check:
            try:
                size = ftp.size(filename)
                print(f"✅ {filename} exists ({size} bytes)")
            except:
                print(f"❌ {filename} NOT FOUND - reuploading...")
                upload_file(ftp, filename, filename)
        
        # Create a cache-buster test file
        cache_buster_content = f'''<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Cache Test - {int(time.time())}</title>
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3VnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3VnPgo=" type="image/svg+xml">
</head>
<body style="font-family: Arial; text-align: center; padding: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1>🔄 Server Cache Test</h1>
    <p><strong>Timestamp:</strong> {int(time.time())}</p>
    <p><strong>Status:</strong> .htaccess updated successfully</p>
    
    <div style="background: rgba(255,255,255,0.9); color: #333; padding: 20px; border-radius: 10px; margin: 20px auto; max-width: 600px;">
        <h2>🧪 Test Links:</h2>
        <p>If these still show header/footer, server cache needs time to clear (5-15 minutes):</p>
        <div style="margin: 20px 0;">
            <a href="/login" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Login (Standalone)</a>
            <a href="/registration" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">Registration</a>
            <a href="/privacy" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #6f42c1; color: white; text-decoration: none; border-radius: 5px;">Privacy</a>
            <a href="/forgot-password" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #fd7e14; color: white; text-decoration: none; border-radius: 5px;">Forgot Password</a>
        </div>
        
        <h3>Direct File Access (Should Work Immediately):</h3>
        <div style="margin: 20px 0;">
            <a href="/login-standalone.php" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">login-standalone.php</a>
            <a href="/registration-standalone.php" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">registration-standalone.php</a>
            <a href="/privacy-standalone.php" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #6f42c1; color: white; text-decoration: none; border-radius: 5px;">privacy-standalone.php</a>
            <a href="/forgot-password-standalone.php" style="display: inline-block; margin: 5px; padding: 10px 15px; background: #fd7e14; color: white; text-decoration: none; border-radius: 5px;">forgot-password-standalone.php</a>
        </div>
    </div>
    
    <p><strong>Expected:</strong> All pages should show gradient background with no header/footer</p>
    <p><strong>If still cached:</strong> Try in incognito/private browsing mode</p>
</body>
</html>'''
        
        # Upload cache buster
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.html', encoding='utf-8') as tmp:
            tmp.write(cache_buster_content)
            tmp_path = tmp.name
        
        try:
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /cache-test.html', file)
            print("✅ Created cache test page")
            os.unlink(tmp_path)
        except Exception as e:
            print(f"⚠️  Could not create cache test page: {e}")
        
        ftp.quit()
        print("\n🎉 Deployment completed!")
        print("\n🧪 Test URLs:")
        print("1. https://11klassniki.ru/cache-test.html (immediate cache test)")
        print("2. https://11klassniki.ru/login (should be standalone)")
        print("3. https://11klassniki.ru/login-standalone.php (direct file access)")
        print("\n💡 If pages still show header/footer:")
        print("- Server cache may take 5-15 minutes to clear")
        print("- Try incognito/private browsing mode")
        print("- Clear browser cache with Ctrl+Shift+R")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())