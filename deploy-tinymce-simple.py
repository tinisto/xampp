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

def upload_directory(ftp, local_dir, remote_dir):
    """Upload a directory recursively"""
    success_count = 0
    total_files = 0
    
    for root, dirs, files in os.walk(local_dir):
        # Create remote directories
        rel_path = os.path.relpath(root, local_dir)
        if rel_path != '.':
            remote_path = f"{remote_dir}/{rel_path}".replace('\\', '/')
            try:
                ftp.mkd(remote_path)
                print(f"📁 Created directory: {remote_path}")
            except:
                pass  # Directory might already exist
        
        # Upload files
        for file in files:
            total_files += 1
            local_file_path = os.path.join(root, file)
            if rel_path == '.':
                remote_file_path = f"{remote_dir}/{file}"
            else:
                remote_file_path = f"{remote_dir}/{rel_path}/{file}".replace('\\', '/')
            
            if upload_file(ftp, local_file_path, remote_file_path):
                success_count += 1
    
    return success_count, total_files

try:
    print("🚀 Deploying TinyMCE Rich Text Editor")
    print("=====================================")
    
    # Connect to FTP
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📁 Creating remote directories...")
    
    # Create directory structure
    directories = [
        'assets',
        'assets/js',
        'uploads',
        'uploads/images'
    ]
    
    for directory in directories:
        try:
            ftp.mkd(directory)
            print(f"📁 Created: {directory}")
        except:
            print(f"📁 Directory exists: {directory}")
    
    print("\n📤 Uploading TinyMCE files...")
    
    # Upload TinyMCE directory
    tinymce_success, tinymce_total = upload_directory(ftp, 'assets/js/tinymce', 'assets/js/tinymce')
    
    print(f"\n📤 Uploading PHP files...")
    
    # Files to upload
    files_to_upload = {
        'dashboard-create-content-unified.php': 'dashboard-create-content-unified.php',
        'upload-image.php': 'upload-image.php'
    }
    
    php_success = 0
    php_total = len(files_to_upload)
    
    for local_file, remote_file in files_to_upload.items():
        if os.path.exists(local_file):
            if upload_file(ftp, local_file, remote_file):
                php_success += 1
        else:
            print(f"⚠️  Local file not found: {local_file}")
    
    ftp.quit()
    
    total_success = tinymce_success + php_success
    total_files = tinymce_total + php_total
    
    print(f"\n📊 Deployment Summary:")
    print(f"   - TinyMCE files: {tinymce_success}/{tinymce_total}")
    print(f"   - PHP files: {php_success}/{php_total}")
    print(f"   - Total success: {total_success}/{total_files}")
    
    if total_success == total_files:
        print("\n✅ TinyMCE Rich Text Editor deployed successfully!")
        
        print("\n🎉 What's Deployed:")
        print("   - ✅ TinyMCE 7.6.0 rich text editor")
        print("   - ✅ Russian language support")
        print("   - ✅ Image upload functionality")
        print("   - ✅ Updated content creation form")
        print("   - ✅ Secure image upload handler")
        
        print("\n🔗 Test the Rich Text Editor:")
        print("Create News: https://11klassniki.ru/create/news")
        print("Create Post: https://11klassniki.ru/create/post")
        
        print("\n💡 Features:")
        print("   - Rich text formatting (bold, italic, lists)")
        print("   - Image upload with drag & drop")
        print("   - Tables, links, and code blocks")
        print("   - Fullscreen editing mode")
        print("   - Auto-save functionality")
        
    else:
        print(f"\n⚠️  Some files failed to upload ({total_files - total_success} failed)")
        print("Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Deployment Error: {str(e)}")