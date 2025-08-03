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
    print("🎨 Deploying design improvements and dark mode fixes...")
    
    files_to_upload = [
        # Unified CSS file for better design and dark mode
        ('css/unified-styles.css', 'css/unified-styles.css'),
        
        # Updated template engine with unified CSS
        ('common-components/template-engine-ultimate.php', 
         'common-components/template-engine-ultimate.php'),
        
        # Improved VPO/SPO pages with better design
        ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions-final.php', 
         'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-final.php'),
        
        # Updated .htaccess
        ('.htaccess', '.htaccess'),
        
        # Theme test page
        ('test-theme-status.php', 'test-theme-status.php'),
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
        
        # Create css directory if needed
        try:
            ftp.mkd('css')
            print("📁 Created css directory")
        except:
            pass  # Directory might already exist
        
        # Upload files
        success_count = 0
        
        print("\n📤 Uploading files...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            
            if os.path.exists(local_file):
                # Create directory structure if needed
                remote_dir = os.path.dirname(remote_path)
                if remote_dir and remote_dir != 'css':  # css already handled
                    dirs = remote_dir.split('/')
                    current_path = ''
                    for dir_name in dirs:
                        if dir_name:
                            current_path = current_path + '/' + dir_name if current_path else dir_name
                            try:
                                ftp.mkd(current_path)
                                print(f"📁 Created directory: {current_path}")
                            except:
                                pass  # Directory might already exist
                
                if upload_file(ftp, local_file, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_path}")
        
        if success_count == len(files_to_upload):
            print("\n🎉 Design improvements deployed successfully!")
            print("\n✨ What's improved:")
            print("   ✅ Unified CSS for consistent design across all pages")
            print("   ✅ Improved dark mode with better colors and transitions")
            print("   ✅ Better card designs with hover effects")
            print("   ✅ Fixed theme toggle button styling")
            print("   ✅ Improved responsive design")
            print("   ✅ Better region cards with modern styling")
            print("\n🔍 Check these pages:")
            print("   • https://11klassniki.ru/vpo-all-regions - Improved design")
            print("   • https://11klassniki.ru/tests - Dark mode toggle")
            print("   • https://11klassniki.ru/news - Dark mode toggle")
            print("   • https://11klassniki.ru/test-theme-status.php - Theme testing")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()