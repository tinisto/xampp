#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        print("🔍 Checking .htaccess content...")
        
        # Download and check .htaccess content
        htaccess_lines = []
        try:
            ftp.retrlines('RETR .htaccess', htaccess_lines.append)
            print("✅ Current .htaccess content:")
            print("=" * 50)
            for i, line in enumerate(htaccess_lines[-20:], len(htaccess_lines)-19):  # Show last 20 lines
                print(f"{i:3}: {line}")
            print("=" * 50)
        except Exception as e:
            print(f"❌ Could not read .htaccess: {e}")
        
        # Check if there are other config files
        print("\n🔍 Checking for other config files:")
        files = []
        ftp.retrlines('LIST', files.append)
        for file_info in files:
            filename = file_info.split()[-1] if file_info.split() else ""
            if any(ext in filename.lower() for ext in ['.htaccess', 'web.config', '.conf', 'nginx']):
                print(f"  📄 {filename}")
        
        # Test creating a file with a unique name that bypasses routing
        unique_test = f'''<!DOCTYPE html>
<html>
<head><title>BYPASS TEST</title></head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; padding: 50px; font-family: Arial;">
<div style="background: rgba(255,255,255,0.9); color: #333; padding: 30px; border-radius: 15px; max-width: 500px; margin: 0 auto;">
<h1>🎉 SUCCESS!</h1>
<h2>Standalone Login Form</h2>
<p>This bypassed server routing - no header/footer!</p>
<form style="margin: 20px 0;">
<input type="email" placeholder="Email" style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px;">
<input type="password" placeholder="Password" style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px;">
<button type="submit" style="width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px;">Login</button>
</form>
<p><a href="/" style="color: #007bff;">← Back to Main Site</a></p>
</div>
</body>
</html>'''
        
        # Upload with a more unique name
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(unique_test)
            tmp_path = tmp.name
        
        import os
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR /working-login-bypass.html', file)
        
        os.unlink(tmp_path)
        print("✅ Created working-login-bypass.html")
        
        ftp.quit()
        
        print("\n🧪 Test this URL:")
        print("https://11klassniki.ru/working-login-bypass.html")
        print("\nIf this STILL shows 404, then server has aggressive front-controller routing")
        print("that catches ALL requests regardless of file existence.")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    main()