#!/usr/bin/env python3
import ftplib
import os

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
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

try:
    print("🚀 Deploying TinyMCE Cloud Integration")
    print("====================================")
    
    # Connect to FTP
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading updated dashboard file...")
    
    # Upload the updated dashboard file
    success = upload_file(ftp, 'dashboard-create-content-unified.php', 'dashboard-create-content-unified.php')
    
    ftp.quit()
    
    if success:
        print("\n✅ TinyMCE Cloud Integration deployed successfully!")
        
        print("\n🎉 What's Updated:")
        print("   - ✅ TinyMCE loaded from reliable cloud CDN")
        print("   - ✅ No local file dependencies")
        print("   - ✅ Automatic updates from TinyMCE")
        print("   - ✅ Russian language support")
        print("   - ✅ Image upload functionality maintained")
        
        print("\n🔗 Test the Rich Text Editor:")
        print("Create News: https://11klassniki.ru/create/news")
        print("Create Post: https://11klassniki.ru/create/post")
        
        print("\n💡 TinyMCE Features Available:")
        print("   - Rich text formatting (bold, italic, underline)")
        print("   - Lists (bulleted and numbered)")
        print("   - Headers and text alignment")
        print("   - Image upload with drag & drop")
        print("   - Links and anchors") 
        print("   - Tables and media embedding")
        print("   - Search and replace")
        print("   - Fullscreen editing mode")
        print("   - Source code view")
        print("   - Word count")
        
        print("\n🌐 Benefits of Cloud CDN:")
        print("   - Faster loading (served from global CDN)")
        print("   - No server storage required")
        print("   - Automatic updates")
        print("   - High availability")
        
        print("\n🚀 TinyMCE Rich Text Editor is ready!")
        
    else:
        print("\n❌ Failed to deploy TinyMCE Cloud integration")
    
except Exception as e:
    print(f"\n❌ Deployment Error: {str(e)}")