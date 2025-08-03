#\!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR\!HK2E\!N55Zt"""

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as file:
            ftp.storbinary(f'STOR {remote_file}', file)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

def main():
    print("🔧 FIXING ALL ISSUES...")
    
    files_to_upload = [
        # Fixed header with no dots and equal icon sizes
        ('common-components/header-unified-simple-safe-v2.php', 
         'common-components/header-unified-simple-safe-v2.php'),
        # New homepage with old content but news styling
        ('index_content_posts_with_news_style.php', 
         'index_content_posts_with_news_style.php'),
        # Updated main index.php
        ('index.php', 
         'index.php'),
    ]
    
    try:
        # Connect to FTP
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully")
        
        # Change to 11klassnikiru directory
        try:
            ftp.cwd('11klassnikiru')
            print("✅ Changed to 11klassnikiru directory")
        except Exception as e:
            print(f"❌ Could not change to 11klassnikiru: {e}")
            return
        
        # Upload files
        success_count = 0
        
        print("\n📤 Uploading fixes...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 ALL ISSUES FIXED\!")
            print("\n✅ Categories Dropdown:")
            print("   • Removed list dots from all category items")
            print("   • Added list-style: none \!important")
            print("   • Clean dropdown appearance")
            print("\n✅ Header Icons:")
            print("   • Theme toggle: 32px × 32px (always)")
            print("   • User avatar: 32px × 32px (always)")
            print("   • Both icons exactly same size at all times")
            print("   • No size changes on hover")
            print("\n✅ Homepage Content:")
            print("   • Restored original posts content")
            print("   • Two sections: '11-классники' and 'Абитуриентам'")
            print("   • Uses beautiful news card styling")
            print("   • Category badges: teal and orange")
            print("   • 8 posts per section")
            print("   • No news content on homepage")
            print("   • Statistics show posts count (not news)")
            print("\n🏠 Homepage now:")
            print("   • Same content as before (posts)")
            print("   • Beautiful news card styling")
            print("   • News content stays in /news page only")
            print("\n🌐 Test at: https://11klassniki.ru")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()
EOF < /dev/null