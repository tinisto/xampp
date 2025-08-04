#!/usr/bin/env python3
import subprocess
import os
from datetime import datetime

def deploy_file(filename):
    """Deploy a single file using curl"""
    print(f"📤 Uploading {filename}...")
    
    # Read file content
    with open(filename, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Create temporary file with content
    temp_file = f'temp_{filename}'
    with open(temp_file, 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Deploy using curl
    curl_command = [
        'curl',
        '-T', temp_file,
        '-u', '11klassnikiru_0:Tg)LyR)qC3',
        f'ftp://ftp.11klassniki.ru/11klassnikiru/{filename}'
    ]
    
    try:
        result = subprocess.run(curl_command, capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ Successfully uploaded {filename}")
        else:
            print(f"❌ Failed to upload {filename}")
            print(f"Error: {result.stderr}")
    except Exception as e:
        print(f"❌ Error: {str(e)}")
    finally:
        # Clean up temp file
        if os.path.exists(temp_file):
            os.remove(temp_file)

def main():
    print("🚀 Deploying Image Preview Feature")
    print("=" * 50)
    
    # Deploy the file
    deploy_file('dashboard-create-content-unified.php')
    
    print("\n" + "=" * 50)
    print("✅ Deployment complete!")
    print("🌐 Test at: https://11klassniki.ru/create/news")
    print("📸 Supported formats: PNG, JPG, JPEG, GIF, WebP")
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")

if __name__ == "__main__":
    main()