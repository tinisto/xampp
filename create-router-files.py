#!/usr/bin/env python3
"""Create router files for pages that need them"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Router files to create
router_files = {
    'news-new.php': '/pages/common/news/news.php',
    'post-new.php': '/pages/post/post.php',
    'tests-new.php': '/pages/tests/tests-main.php',
    'category-new.php': '/pages/category/category.php',
    'about-new.php': '/pages/about/about.php',
    'write-new.php': '/pages/write/write-simple.php',
    'search-results-new.php': '/pages/search/search.php'
}

try:
    print("Creating and uploading router files...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    for filename, include_path in router_files.items():
        # Create router file
        content = f"""<?php
// Router file for {filename}
// Include the actual page
include $_SERVER['DOCUMENT_ROOT'] . '{include_path}';
?>"""
        
        # Save locally
        with open(filename, 'w') as f:
            f.write(content)
        
        # Upload
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR ' + filename, f)
        print(f"✓ Created and uploaded {filename}")
    
    # Also upload the news-new.php and tests-new.php we just created
    for f in ['news-new.php', 'tests-new.php']:
        try:
            with open(f, 'rb') as file:
                ftp.storbinary(f'STOR {f}', file)
            print(f"✓ Uploaded {f}")
        except:
            pass
    
    ftp.quit()
    
    print("\n✅ Router files created!")
    print("\nThese pages should now work:")
    print("- https://11klassniki.ru/news")
    print("- https://11klassniki.ru/tests")
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/vpo-all-regions")
    
except Exception as e:
    print(f"Error: {e}")