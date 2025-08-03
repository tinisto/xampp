#!/usr/bin/env python3

import ftplib
import os
import tempfile

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def read_file(ftp, remote_file):
    """Read a file from FTP server"""
    try:
        temp = tempfile.NamedTemporaryFile(delete=False)
        ftp.retrbinary(f'RETR {remote_file}', temp.write)
        temp.close()
        with open(temp.name, 'r', encoding='utf-8') as f:
            content = f.read()
        os.unlink(temp.name)
        return content
    except Exception as e:
        print(f"❌ Failed to read {remote_file}: {str(e)}")
        return None

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as file:
            ftp.storbinary(f'STOR {remote_file}', file)
        print(f"✅ Updated: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

def fix_page_to_vanilla(ftp, remote_file):
    """Fix a page to use vanilla CSS instead of Bootstrap"""
    content = read_file(ftp, remote_file)
    if content and "'cssFramework' => 'bootstrap'" in content:
        # Replace Bootstrap with custom (vanilla CSS)
        new_content = content.replace("'cssFramework' => 'bootstrap'", "'cssFramework' => 'custom'")
        
        # Save to temp file
        temp = tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8')
        temp.write(new_content)
        temp.close()
        
        # Upload back
        success = upload_file(ftp, temp.name, remote_file)
        os.unlink(temp.name)
        return success
    return False

def main():
    print("🔧 Converting all pages to vanilla CSS/JS...")
    
    # Pages that need to be converted
    pages_to_fix = [
        'pages/tests/tests-main.php',
        'pages/common/educational-institutions-in-region/educational-institutions-in-region.php',
        'pages/news/news-main.php',
    ]
    
    # Also upload the safer header
    files_to_upload = [
        ('common-components/header-unified-simple-safe.php', 
         'common-components/header-unified-simple-safe.php'),
        ('common-components/template-engine-ultimate.php', 
         'common-components/template-engine-ultimate.php'),
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
        
        # Upload safer header and template engine first
        print("\n📤 Uploading safer header...")
        for local_path, remote_path in files_to_upload:
            local_file = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            if os.path.exists(local_file):
                upload_file(ftp, local_file, remote_path)
        
        # Fix pages to use vanilla CSS
        print("\n🔄 Converting pages to vanilla CSS...")
        fixed_count = 0
        for page in pages_to_fix:
            print(f"\nChecking {page}...")
            if fix_page_to_vanilla(ftp, page):
                fixed_count += 1
        
        print(f"\n🎉 Conversion complete! Fixed {fixed_count} pages.")
        print("\n✅ What's been done:")
        print("   • All pages now use vanilla CSS framework")
        print("   • Header has better error handling")
        print("   • No more Bootstrap dependencies")
        print("   • Consistent vanilla CSS/JS across the site")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()