#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the rendering debug script
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-news-rendering.php", 'rb') as f:
            ftp.storbinary('STOR debug-news-rendering.php', f)
        
        print("✅ Uploaded news rendering debug script")
        print("\n🔍 NEWS RENDERING DEBUG:")
        print("  📊 Visit: https://11klassniki.ru/debug-news-rendering.php")
        print("\n🎯 This will test step-by-step:")
        print("  ✅ Database query (we know this works)")
        print("  ✅ Template variable creation")
        print("  ✅ Component loading (real_title.php)")
        print("  ✅ Content rendering")
        print("  ✅ Template file existence")
        print("\n📝 This will show exactly where the rendering fails")
        print("💡 The article EXISTS and is FOUND, but something prevents display")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())