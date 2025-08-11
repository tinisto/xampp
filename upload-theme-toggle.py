#!/usr/bin/env python3
import ftplib

def upload_theme_toggle():
    # FTP credentials
    ftp_host = "ftp.ipage.com"
    ftp_user = "franko"
    ftp_pass = "JyvR!HK2E!N55Zt"
    
    try:
        print("🚀 Uploading header with theme toggle...")
        ftp = ftplib.FTP(ftp_host)
        ftp.login(ftp_user, ftp_pass)
        print("✅ Connected successfully!")
        
        # Upload updated header
        with open("includes/header_modern.php", 'rb') as file:
            ftp.storbinary('STOR /11klassnikiru/includes/header_modern.php', file)
        print("✅ Uploaded: header_modern.php with theme toggle")
        
        # Upload updated footer
        with open("includes/footer_modern.php", 'rb') as file:
            ftp.storbinary('STOR /11klassnikiru/includes/footer_modern.php', file)
        print("✅ Uploaded: footer_modern.php with dark mode support")
        
        # Upload updated favicon
        with open("favicon.svg", 'rb') as file:
            ftp.storbinary('STOR /11klassnikiru/favicon.svg', file)
        print("✅ Uploaded: favicon.svg with theme support")
        
        ftp.quit()
        print("\n🎉 Upload complete!")
        print("\n🌙 Dark/Light Mode Features Added:")
        print("  • Sun/Moon toggle button in header")
        print("  • Mobile theme toggle in hamburger menu")
        print("  • Full dark mode styling")
        print("  • Persistent theme storage")
        print("  • Smooth color transitions")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_theme_toggle()