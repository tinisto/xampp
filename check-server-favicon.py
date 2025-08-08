#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        print("🔍 CHECKING SERVER FOR FAVICON REFERENCES...")
        
        # Check if any PHP files on server still reference favicon.php
        suspect_files = [
            'common-components/header.php',
            'common-components/template-engine-ultimate.php',
            'pages/login/login-secure.php',
            'pages/registration/registration_template.php',
            'pages/registration/registration_template_fast.php',
            'pages/registration/registration-old.php',
            'forgot-password.php'
        ]
        
        for file in suspect_files:
            try:
                print(f"\n📁 Checking {file}...")
                with open(f'temp_{file.replace("/", "_")}', 'wb') as f:
                    ftp.retrbinary(f'RETR {file}', f.write)
                
                with open(f'temp_{file.replace("/", "_")}', 'r') as f:
                    content = f.read()
                    if 'favicon.php' in content:
                        print(f"  ❌ FOUND favicon.php reference!")
                        lines = content.split('\n')
                        for i, line in enumerate(lines, 1):
                            if 'favicon.php' in line:
                                print(f"    Line {i}: {line.strip()}")
                    else:
                        print(f"  ✅ Clean - no favicon.php references")
                        
            except Exception as e:
                print(f"  ⚠️ Could not check {file}: {e}")
        
        # Upload debug scripts
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-news-error.php", 'rb') as f:
            ftp.storbinary('STOR debug-news-error.php', f)
        print("\n✅ Uploaded debug-news-error.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/common/news/news-single-safe.php", 'rb') as f:
            ftp.storbinary('STOR pages/common/news/news-single-safe.php', f)
        print("✅ Uploaded news-single-safe.php")
        
        print("\n🔍 DEBUG TOOLS AVAILABLE:")
        print("  1. https://11klassniki.ru/debug-news-error.php")
        print("  2. Test safe version by temporarily renaming files")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())