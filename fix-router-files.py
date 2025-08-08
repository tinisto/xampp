#!/usr/bin/env python3
"""Fix router files to handle errors better"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Better router template
router_template = '''<?php
// Router for {name}
error_reporting(0); // Suppress errors for production

// Set default content
$greyContent1 = '<div style="padding: 30px;"><h1>{title}</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="padding: 20px;"><p>Loading...</p></div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = '{title}';
$metaD = '';
$metaK = '';

// Try to include the actual page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '{include_path}';
if (file_exists($pageFile)) {{
    include $pageFile;
}} else {{
    $greyContent5 = '<div style="padding: 40px; text-align: center;">
        <h2>Page temporarily unavailable</h2>
        <p>Please try again later.</p>
        <p><a href="/" style="color: #28a745;">Return to homepage</a></p>
    </div>';
}}

// Ensure template exists
if (!isset($greyContent1)) {{
    $greyContent1 = '<div style="padding: 30px;"><h1>{title}</h1></div>';
}}

// Include template - this should always be at the end
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/real_template.php')) {{
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}} else {{
    echo "Template not found";
}}
?>'''

# Files to create
files_to_fix = {
    'news-new.php': {
        'title': 'Новости',
        'include_path': '/pages/common/news/news.php'
    },
    'tests-new.php': {
        'title': 'Тесты',
        'include_path': '/pages/tests/tests-main.php'
    },
    'category-new.php': {
        'title': 'Категория',
        'include_path': '/pages/category/category.php'
    },
    'post-new.php': {
        'title': 'Статья',
        'include_path': '/pages/post/post.php'
    }
}

try:
    print("Creating better router files...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    for filename, config in files_to_fix.items():
        content = router_template.format(
            name=filename,
            title=config['title'],
            include_path=config['include_path']
        )
        
        # Save locally
        with open(filename, 'w', encoding='utf-8') as f:
            f.write(content)
        
        # Upload
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR {filename}', f)
        print(f"✓ Fixed {filename}")
    
    # Also upload the news-working.php
    with open('news-working.php', 'rb') as f:
        ftp.storbinary('STOR news-working.php', f)
    print("✓ Uploaded news-working.php")
    
    ftp.quit()
    
    print("\n✅ Router files fixed!")
    print("\nTest these again:")
    print("- https://11klassniki.ru/news")
    print("- https://11klassniki.ru/tests")
    print("- https://11klassniki.ru/news-working.php (debug version)")
    
except Exception as e:
    print(f"Error: {e}")