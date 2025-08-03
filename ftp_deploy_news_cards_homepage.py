#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

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
    print("🎨 DEPLOYING REUSABLE COMPONENTS & FIXING PAGE HEIGHTS...")
    
    files_to_upload = [
        # New reusable components
        ('common-components/content-wrapper.php', 
         'common-components/content-wrapper.php'),
        ('common-components/typography.php', 
         'common-components/typography.php'),
        # Fixed tests page with smaller header and reusable components
        ('pages/tests/tests-main.php', 
         'pages/tests/tests-main.php'),
        ('pages/tests/tests-main-content-fixed.php', 
         'pages/tests/tests-main-content-fixed.php'),
        # Fixed category page with reusable header and Bootstrap->custom
        ('pages/category/category.php', 
         'pages/category/category.php'),
        ('pages/category/category-content-unified.php', 
         'pages/category/category-content-unified.php'),
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
        
        print("\n📤 Uploading news cards homepage...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 NEWS CARDS HOMEPAGE DEPLOYED!")
            print("\n✨ What's new on the homepage:")
            print("   • Beautiful news cards (same as /news page)")
            print("   • Shows latest 8 news items with category badges")
            print("   • Clean grid layout (4-3-2-1 columns responsive)")
            print("   • Hover effects and smooth transitions")
            print("   • 'Все новости' button links to /news")
            print("   • Dark mode support")
            print("   • Updated statistics (shows news count instead of posts)")
            print("\n📋 Features:")
            print("   • Hero section with search")
            print("   • Latest news section with cards")
            print("   • Statistics section")
            print("   • Fully responsive design")
            print("   • Category badges on each news card")
            print("\n🌐 Homepage now matches /news page design!")
            print("   Test at: https://11klassniki.ru")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()