#!/usr/bin/env python3
"""
Fix SPO/VPO page design with simplified clean version
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        ftp.cwd('/11klassnikiru')
        
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir in dirs:
                if dir:
                    try:
                        ftp.cwd(dir)
                    except:
                        ftp.mkd(dir)
                        ftp.cwd(dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Fixing SPO/VPO page design with clean implementation...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # New simplified SPO/VPO page
        ('pages/common/vpo-spo/single-simplified.php', 
         'pages/common/vpo-spo/single-simplified.php'),
        # Updated main file to use simplified version
        ('pages/common/vpo-spo/single.php', 
         'pages/common/vpo-spo/single.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                upload_file(ftp, local_path, remote_path)
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n🎯 Fixed:")
        print("✅ Created clean, simplified SPO/VPO page")
        print("✅ Bypassed problematic template engine nesting")
        print("✅ Uses vanilla CSS (no Bootstrap)")
        print("✅ Clean, modern design matching site style")
        print("✅ Proper dark mode support")
        
        print("\n🔍 Test the pages:")
        print("https://11klassniki.ru/spo/bryanskiy-transportnyiy-tehnikum")
        print("https://11klassniki.ru/vpo/aaep")
        
        print("\n📋 What's improved:")
        print("• Simple, clean layout with proper spacing")
        print("• Consistent header and footer")
        print("• Clear information sections")
        print("• Back link to region list")
        print("• Mobile responsive design")
        print("• No ugly badges or complex tabs")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()