#!/usr/bin/env python3
"""Fix all router files to avoid double template inclusion"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Router files that need to be fixed
router_files = {
    'news-new.php': '''<?php
/**
 * News router - handles both listing and single news pages
 * DO NOT include template here - the page itself will include it
 */

// Suppress errors but log them
error_reporting(0);
ini_set('display_errors', 0);

// Include the actual page content
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // If page doesn't exist, show error with template
    $greyContent1 = '<div style="padding: 30px;"><h1>Новости</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 40px; text-align: center;">
        <h2>Page temporarily unavailable</h2>
        <p>Please try again later.</p>
        <p><a href="/" style="color: #28a745;">Return to homepage</a></p>
    </div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Новости - 11-классники';
    
    // Only include template if page doesn't exist
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>''',
    
    'tests-new.php': '''<?php
/**
 * Tests router
 * DO NOT include template here - the page itself will include it
 */

// Suppress errors
error_reporting(0);

// First check which test page to load
$testUrl = $_GET['url_test'] ?? '';

if (!empty($testUrl)) {
    // Single test page
    $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/test.php';
} else {
    // Tests listing page
    $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/tests-main-working.php';
}

// Try the new template version first
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback to error page with template
    $greyContent1 = '<div style="padding: 30px;"><h1>Тесты</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px; text-align: center;">
        <h2>Страница временно недоступна</h2>
        <p><a href="/" style="color: #28a745;">Вернуться на главную</a></p>
    </div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Тесты - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>''',
    
    'spo-all-regions-new.php': '''<?php
/**
 * SPO All Regions router
 */

// Suppress errors
error_reporting(0);

// Set type for the page
$_GET['type'] = 'spo';

// Include the actual page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/vpo-spo-all-regions.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>СПО всех регионов</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p>Загрузка данных...</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'СПО всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>''',
    
    'vpo-all-regions-new.php': '''<?php
/**
 * VPO All Regions router
 */

// Suppress errors
error_reporting(0);

// Set type for the page
$_GET['type'] = 'vpo';

// Include the actual page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/vpo-spo-all-regions.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>ВПО всех регионов</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p>Загрузка данных...</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'ВПО всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>'''
}

try:
    print("Fixing all router files...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    for filename, content in router_files.items():
        with open(filename, 'w') as f:
            f.write(content)
        
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR {filename}', f)
        print(f"✓ Fixed {filename}")
    
    ftp.quit()
    
    print("\n✅ All router files fixed!")
    print("\nTest these pages:")
    print("- https://11klassniki.ru/news")
    print("- https://11klassniki.ru/tests")
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/vpo-all-regions")
    
except Exception as e:
    print(f"Error: {e}")