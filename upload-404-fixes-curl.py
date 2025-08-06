#!/usr/bin/env python3

import subprocess
import time

# Files to upload for 404 fixes
files = [
    ('pages/post/post.php', 'pages/post/post.php'),
    ('pages/category/category-data-fetch.php', 'pages/category/category-data-fetch.php'),
    ('fix-404-categories.php', 'fix-404-categories.php'),
    ('fix-404-manual.php', 'fix-404-manual.php')
]

print("🚀 Uploading 404 fixes to server...")
print("=" * 50)

success_count = 0
for local, remote in files:
    print(f"📤 Uploading {local}...")
    cmd = f'curl -T /Applications/XAMPP/xamppfiles/htdocs/{local} ftp://8b6cdc76_sitearchive:jU9%25mHr1@77.232.131.89/domains/11klassniki.ru/public_html/{remote}'
    
    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    
    if result.returncode == 0:
        print(f"✅ Successfully uploaded {local}")
        success_count += 1
    else:
        print(f"❌ Failed to upload {local}")
        if result.stderr:
            print(f"   Error: {result.stderr.strip()}")
    
    time.sleep(1)

print("\n" + "=" * 50)
print(f"🎯 Upload completed: {success_count}/{len(files)} files uploaded successfully")

if success_count == len(files):
    print("\n🔧 Next steps:")
    print("1. Visit https://11klassniki.ru/fix-404-categories.php to create missing categories")
    print("2. Test the fixed URLs:")
    print("   - https://11klassniki.ru/category/ege/")
    print("   - https://11klassniki.ru/category/oge/") 
    print("   - https://11klassniki.ru/category/vpr/")
    print("   - Test any post URLs that were failing")

print("\nDone! 🚀")