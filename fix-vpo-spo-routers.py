#!/usr/bin/env python3
"""Fix VPO/SPO router files to use correct page paths"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Router files that need to be fixed
router_files = {
    'vpo-all-regions-new.php': '''<?php
/**
 * VPO All Regions router
 */

// Suppress errors
error_reporting(0);

// Set type for the page
$_GET['type'] = 'vpo';
$institutionType = 'vpo';

// Include the actual page - use educational-institutions page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
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
?>''',
    
    'spo-all-regions-new.php': '''<?php
/**
 * SPO All Regions router
 */

// Suppress errors
error_reporting(0);

// Set type for the page
$_GET['type'] = 'spo';
$institutionType = 'spo';

// Include the actual page - use educational-institutions page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
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
    
    'schools-all-regions-real.php': '''<?php
/**
 * Schools All Regions router
 */

// Suppress errors
error_reporting(0);

// Set type for the page
$_GET['type'] = 'school';
$institutionType = 'school';

// Include the actual page - use educational-institutions page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>Школы всех регионов</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p>Загрузка данных...</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Школы всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>'''
}

try:
    print("Fixing VPO/SPO/Schools router files...")
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
    
    print("\n✅ All educational institution router files fixed!")
    print("\nTest these pages:")
    print("- https://11klassniki.ru/vpo-all-regions")
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/schools-all-regions")
    
except Exception as e:
    print(f"Error: {e}")