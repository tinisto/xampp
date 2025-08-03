#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def create_remote_directory(ftp, path):
    """Create directory structure on remote server"""
    dirs = path.split('/')
    for i in range(1, len(dirs)):
        dir_path = '/'.join(dirs[:i])
        if dir_path:
            try:
                ftp.mkd(dir_path)
                print(f"📁 Created directory: {dir_path}")
            except ftplib.error_perm:
                pass  # Directory might already exist

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        # Create remote directory if needed
        create_remote_directory(ftp, remote_file)
        
        # Upload the file
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

# Files to deploy
FILES_TO_DEPLOY = {
    # Cookie consent system
    'includes/components/cookie-consent.php': 'includes/components/cookie-consent.php',
    'cookie-consent-process.php': 'cookie-consent-process.php',
    
    # Updated template with cookie fixes
    'common-components/template-engine-modern.php': 'common-components/template-engine-modern.php',
    
    # Updated .htaccess with cookie route
    '.htaccess': '.htaccess'
}

try:
    print("🍪 Deploying Cookie Consent & Security System")
    print("===========================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    success_count = 0
    total_files = len(FILES_TO_DEPLOY)
    
    for local_file, remote_file in FILES_TO_DEPLOY.items():
        if os.path.exists(local_file):
            if upload_file(ftp, local_file, remote_file):
                success_count += 1
        else:
            print(f"⚠️  Local file not found: {local_file}")
    
    ftp.quit()
    
    print(f"\n📊 Deployment Summary:")
    print(f"   - Total files: {total_files}")
    print(f"   - Successfully uploaded: {success_count}")
    print(f"   - Failed: {total_files - success_count}")
    
    if success_count == total_files:
        print("\n✅ Cookie system deployed successfully!")
        print("\n🍪 Cookie System Features:")
        print("   - GDPR compliant consent banner")
        print("   - Secure cookie settings (SameSite, Secure, HttpOnly)")
        print("   - Theme preferences with proper security")
        print("   - Analytics consent management")
        print("   - Russian law compliance")
        
        print("\n🔧 Technical Improvements:")
        print("   - SameSite=Lax attribute for all cookies")
        print("   - Secure flag for HTTPS connections")
        print("   - HttpOnly flag for sensitive cookies")
        print("   - Proper cookie consent tracking")
        
        print("\n🌐 Now cookies should work properly in:")
        print("   - Chrome incognito mode")
        print("   - All modern browsers")
        print("   - Cross-site scenarios")
        
        print("\n📋 Next Steps:")
        print("   1. Test cookie consent banner on site")
        print("   2. Verify theme switching works in incognito")
        print("   3. Check cookie storage in browser dev tools")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")
    
print("\n🔍 To test: Visit site in Chrome incognito mode and check if cookies persist!")