#!/usr/bin/env python3
import ftplib

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed: {str(e)}")
        return False

try:
    print("🌙 Deploying Dark Mode Toggle")
    print("==============================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading main dashboard with dark mode...")
    
    # For now, just upload the main dashboard
    # We'll update other pages later
    if upload_file(ftp, 'dashboard-professional.php', 'dashboard-professional.php'):
        print("\n✅ Dark mode deployed to main dashboard!")
        print("\n🎯 Features:")
        print("   - 🌞/🌙 Toggle button in header")
        print("   - Smooth theme transitions")
        print("   - Saves preference in localStorage")
        print("   - Beautiful dark color scheme")
        
        print("\n📊 Dashboard: https://11klassniki.ru/dashboard")
        print("\n💡 Next: Will add dark mode to other dashboard pages")
        
    else:
        print("\n❌ Failed to deploy dark mode")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")