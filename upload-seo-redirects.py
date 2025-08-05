#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_seo_redirects():
    print("🚀 Uploading SEO redirect system")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload .htaccess with new redirect rules
        with open('/Applications/XAMPP/xamppfiles/htdocs/.htaccess', 'rb') as f:
            ftp.storbinary('STOR .htaccess', f)
        print("✅ .htaccess updated (ID → slug redirects)")
        
        # Upload school redirect handler
        ftp.cwd('pages/school')
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/school/school-redirect.php', 'rb') as f:
            ftp.storbinary('STOR school-redirect.php', f)
        print("✅ School redirect handler uploaded")
        
        # Upload updated school template (slug-only)
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/school/school-single-simplified.php', 'rb') as f:
            ftp.storbinary('STOR school-single-simplified.php', f)
        print("✅ School template updated (slug-only)")
        
        # Upload updated region template (slug links)
        ftp.cwd('/')
        ftp.cwd(PATH + 'pages/common/educational-institutions-in-region')
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR educational-institutions-in-region.php', f)
        print("✅ Region template updated (slug links)")
        
        ftp.quit()
        
        print("\n🎉 SEO redirect system deployed!")
        print("\n📈 SEO Benefits:")
        print("- ✅ Single canonical URLs (no duplicate content)")
        print("- ✅ Friendly URLs with keywords (better SEO)")
        print("- ✅ 301 redirects preserve link authority")
        print("- ✅ Better user experience")
        
        print("\n🔗 URL Structure:")
        print("- OLD: /school/2718 → REDIRECTS → /school/sosh-1-shimanovsk")
        print("- NEW: /school/sosh-1-shimanovsk (canonical)")
        
        print("\n🧪 Test the redirects:")
        print("🌐 https://11klassniki.ru/school/2718 (should redirect)")  
        print("🌐 https://11klassniki.ru/school/sosh-1-shimanovsk (canonical)")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_seo_redirects()