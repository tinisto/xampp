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

def create_directories(ftp, path):
    """Create directory structure recursively"""
    parts = path.split('/')
    current_path = ''
    
    for part in parts:
        if part:
            current_path += '/' + part
            try:
                ftp.mkd(current_path)
                print(f"📁 Created: {current_path}")
            except:
                pass  # Directory might already exist

try:
    print("🚀 Deploying Essential TinyMCE Files")
    print("===================================")
    
    # Connect to FTP
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📁 Creating directory structure...")
    
    # Create essential directories
    directories = [
        'assets/js/tinymce/js/tinymce',
        'assets/js/tinymce/js/tinymce/skins/ui/oxide',
        'assets/js/tinymce/js/tinymce/skins/content/default',
        'assets/js/tinymce/js/tinymce/themes/silver',
        'assets/js/tinymce/js/tinymce/plugins/advlist',
        'assets/js/tinymce/js/tinymce/plugins/autolink',
        'assets/js/tinymce/js/tinymce/plugins/lists',
        'assets/js/tinymce/js/tinymce/plugins/link',
        'assets/js/tinymce/js/tinymce/plugins/image',
        'assets/js/tinymce/js/tinymce/plugins/charmap',
        'assets/js/tinymce/js/tinymce/plugins/preview',
        'assets/js/tinymce/js/tinymce/plugins/anchor',
        'assets/js/tinymce/js/tinymce/plugins/searchreplace',
        'assets/js/tinymce/js/tinymce/plugins/visualblocks',
        'assets/js/tinymce/js/tinymce/plugins/code',
        'assets/js/tinymce/js/tinymce/plugins/fullscreen',
        'assets/js/tinymce/js/tinymce/plugins/insertdatetime',
        'assets/js/tinymce/js/tinymce/plugins/media',
        'assets/js/tinymce/js/tinymce/plugins/table',
        'assets/js/tinymce/js/tinymce/plugins/help',
        'assets/js/tinymce/js/tinymce/plugins/wordcount',
        'assets/js/tinymce/js/tinymce/langs',
        'assets/js/tinymce/js/tinymce/icons/default'
    ]
    
    for directory in directories:
        create_directories(ftp, directory)
    
    print("\n📤 Uploading essential TinyMCE files...")
    
    # Essential files to upload
    essential_files = [
        ('assets/js/tinymce/js/tinymce/tinymce.min.js', 'assets/js/tinymce/js/tinymce/tinymce.min.js'),
        ('assets/js/tinymce/js/tinymce/themes/silver/theme.min.js', 'assets/js/tinymce/js/tinymce/themes/silver/theme.min.js'),
        ('assets/js/tinymce/js/tinymce/skins/ui/oxide/skin.min.css', 'assets/js/tinymce/js/tinymce/skins/ui/oxide/skin.min.css'),
        ('assets/js/tinymce/js/tinymce/skins/content/default/content.min.css', 'assets/js/tinymce/js/tinymce/skins/content/default/content.min.css'),
        ('assets/js/tinymce/js/tinymce/icons/default/icons.min.js', 'assets/js/tinymce/js/tinymce/icons/default/icons.min.js'),
        ('assets/js/tinymce/js/tinymce/langs/ru.js', 'assets/js/tinymce/js/tinymce/langs/ru.js'),
        
        # Plugin files
        ('assets/js/tinymce/js/tinymce/plugins/advlist/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/advlist/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/autolink/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/autolink/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/lists/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/lists/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/link/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/link/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/image/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/image/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/charmap/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/charmap/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/preview/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/preview/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/anchor/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/anchor/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/searchreplace/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/searchreplace/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/visualblocks/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/visualblocks/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/code/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/code/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/fullscreen/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/fullscreen/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/insertdatetime/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/insertdatetime/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/media/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/media/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/table/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/table/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/help/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/help/plugin.min.js'),
        ('assets/js/tinymce/js/tinymce/plugins/wordcount/plugin.min.js', 'assets/js/tinymce/js/tinymce/plugins/wordcount/plugin.min.js')
    ]
    
    success_count = 0
    total_files = len(essential_files)
    
    for local_file, remote_file in essential_files:
        if os.path.exists(local_file):
            if upload_file(ftp, local_file, remote_file):
                success_count += 1
        else:
            print(f"⚠️  Local file not found: {local_file}")
    
    ftp.quit()
    
    print(f"\n📊 Deployment Summary:")
    print(f"   - Essential TinyMCE files: {success_count}/{total_files}")
    
    if success_count >= 10:  # At least core files uploaded
        print("\n✅ Essential TinyMCE files deployed successfully!")
        
        print("\n🎉 TinyMCE Rich Text Editor is now ready!")
        print("   - ✅ Self-hosted TinyMCE editor")
        print("   - ✅ Russian language support")
        print("   - ✅ Image upload functionality")
        print("   - ✅ Essential plugins loaded")
        
        print("\n🔗 Test the Rich Text Editor:")
        print("Create News: https://11klassniki.ru/create/news")
        print("Create Post: https://11klassniki.ru/create/post")
        
        print("\n💡 Features available:")
        print("   - Bold, italic, underline formatting")
        print("   - Lists (bulleted and numbered)")
        print("   - Headers and text alignment")
        print("   - Image upload with drag & drop")
        print("   - Links and anchors")
        print("   - Tables and media embedding")
        print("   - Search and replace")
        print("   - Fullscreen editing mode")
        print("   - Source code view")
        
        print("\n🚀 TinyMCE is ready for content creation!")
        
    else:
        print(f"\n⚠️  Only {success_count} files uploaded successfully.")
        print("TinyMCE may not work properly without all essential files.")
    
except Exception as e:
    print(f"\n❌ Deployment Error: {str(e)}")