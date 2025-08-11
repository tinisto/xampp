#!/usr/bin/env python3
import ftplib

def upload_header_with_toggle():
    # FTP credentials
    ftp_host = "ftp.ipage.com"
    ftp_user = "franko"
    ftp_pass = "JyvR!HK2E!N55Zt"
    
    try:
        print("🚀 Uploading header with mobile toggle...")
        ftp = ftplib.FTP(ftp_host)
        ftp.login(ftp_user, ftp_pass)
        print("✅ Connected successfully!")
        
        # Upload header
        with open("includes/header_modern.php", 'rb') as file:
            ftp.storbinary('STOR /11klassnikiru/includes/header_modern.php', file)
        print("✅ Uploaded: header_modern.php with mobile toggle")
        
        ftp.quit()
        print("\n🎉 Upload complete!")
        print("\n📱 Mobile toggle features added:")
        print("  • Hamburger menu (☰) on mobile devices")
        print("  • Transforms to X when open")
        print("  • Dropdown navigation menu")
        print("  • Auto-closes when clicking outside")
        print("  • Auto-closes when resizing to desktop")
        
        print("\n🌐 Test at: https://11klassniki.ru/test-simple-header.php")
        print("   (Resize browser to < 768px to see mobile menu)")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_header_with_toggle()