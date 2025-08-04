#!/usr/bin/env python3
import ftplib
from datetime import datetime

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
    print("🚀 Deploying Modern Comments System")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading modern comments system...")
    
    files_to_upload = [
        {
            'local': 'comments/modern-comments-component.php',
            'remote': 'comments/modern-comments-component.php',
            'description': 'Modern reusable comments component'
        },
        {
            'local': 'comments/load_comments_modern.php',
            'remote': 'comments/load_comments_modern.php',
            'description': 'Modern comments loader with professional styling'
        },
        {
            'local': 'pages/post/post-content-professional.php',
            'remote': 'pages/post/post-content-professional.php',
            'description': 'Updated post template with modern comments'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Modern comments system deployed successfully!")
        
        print("\n🌟 New Features:")
        print("   - 🎨 Modern, professional design matching post layout")
        print("   - 📱 Fully responsive for mobile and desktop")
        print("   - 🔒 Enhanced security with XSS protection")
        print("   - 💬 Threaded replies with nested comments")
        print("   - ⏱️ Real-time character counter")
        print("   - 👤 Improved avatar and user handling")
        print("   - 📄 Pagination for large comment lists")
        print("   - ⚡ Optimized database queries")
        
        print("\n🎨 Design Improvements:")
        print("   - Gradient header with comment count")
        print("   - Clean, card-based layout")
        print("   - Consistent typography and spacing")
        print("   - Hover effects and smooth transitions")
        print("   - Better form styling and UX")
        print("   - Professional color scheme")
        
        print("\n🔧 Technical Fixes:")
        print("   - Fixed database column references (avatar_url vs avatar)")
        print("   - Fixed first_name/last_name field mapping")
        print("   - Improved entity ID detection for posts")
        print("   - Better error handling and validation")
        print("   - Optimized SQL queries")
        
        print("\n🌐 Test the New Comments:")
        print("   - https://11klassniki.ru/post/ledi-v-pogonah")
        print("   - https://11klassniki.ru/post/prinosit-dobro-lyudyam")
        
        print("\n📝 Next Steps:")
        print("   - Test comment submission and replies")
        print("   - Verify all entity types (posts, VPO, SPO, schools)")
        print("   - Check admin comment management")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")