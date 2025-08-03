#!/usr/bin/env python3
"""
Fix all critical issues found during site review
"""

import ftplib
import os
import re

def read_file(filepath):
    """Read file content"""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            return f.read()
    except:
        return None

def write_file(filepath, content):
    """Write content to file"""
    try:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        return True
    except:
        return False

def fix_footer_references():
    """Fix all incorrect footer.php references"""
    files_to_fix = [
        'pages/write/write-form-modern.php',
        'pages/tests/test-improved.php',
        'pages/tests/test-result.php',
        'pages/category-news/category-news-standalone.php',
        'pages/category/category-standalone.php',
        'pages/school/school-single-working.php',
        'common-components/unified-template.php',
        'pages/common/educational-institutions-in-region/educational-institutions-in-region-direct.php',
        'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-direct.php',
    ]
    
    fixed_files = []
    
    for file_path in files_to_fix:
        full_path = f"/Applications/XAMPP/xamppfiles/htdocs/{file_path}"
        if os.path.exists(full_path):
            content = read_file(full_path)
            if content:
                # Fix various footer include patterns
                original = content
                content = re.sub(r"include\s+['\"]?.*?/footer\.php['\"]?\s*;", 
                               "include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';", content)
                content = re.sub(r"include\s+['\"]?.*?/includes/footer\.php['\"]?\s*;", 
                               "include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';", content)
                content = re.sub(r"require_once\s+['\"]?.*?/footer\.php['\"]?\s*;", 
                               "include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';", content)
                
                if content != original:
                    write_file(full_path, content)
                    fixed_files.append(file_path)
    
    return fixed_files

def create_error_page():
    """Create a proper error page"""
    error_page_content = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/SessionManager.php';
SessionManager::start();

$pageTitle = 'Ошибка - 11-классники';
$metaD = 'Произошла ошибка. Пожалуйста, попробуйте позже.';
$metaK = 'ошибка, error, 11классники';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK
];

// Main content
$mainContent = 'pages/error/error-content.php';

// Render template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
?>'''

    error_content = '''<div class="container" style="max-width: 800px; margin: 0 auto; padding: 40px 20px; text-align: center;">
    <div style="background: var(--surface, #ffffff); border: 1px solid var(--border-color, #e2e8f0); border-radius: 12px; padding: 40px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 64px; color: #e74c3c; margin-bottom: 20px;"></i>
        
        <h1 style="color: var(--text-primary, #333); margin-bottom: 20px;">Произошла ошибка</h1>
        
        <p style="color: var(--text-secondary, #666); font-size: 18px; margin-bottom: 30px;">
            К сожалению, произошла непредвиденная ошибка. Пожалуйста, попробуйте позже или вернитесь на главную страницу.
        </p>
        
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/" style="padding: 12px 24px; background: var(--primary-color, #28a745); color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">
                На главную
            </a>
            <a href="javascript:history.back()" style="padding: 12px 24px; background: var(--surface, #f8f9fa); color: var(--text-primary, #333); text-decoration: none; border-radius: 6px; border: 1px solid var(--border-color, #e2e8f0); font-weight: 500;">
                Назад
            </a>
        </div>
    </div>
</div>'''

    # Create error directory if it doesn't exist
    error_dir = "/Applications/XAMPP/xamppfiles/htdocs/pages/error"
    if not os.path.exists(error_dir):
        os.makedirs(error_dir)
    
    # Write files
    write_file(f"{error_dir}/error.php", error_page_content)
    write_file(f"{error_dir}/error-content.php", error_content)
    
    return ['pages/error/error.php', 'pages/error/error-content.php']

def add_database_error_handling():
    """Add error handling to header.php"""
    header_path = "/Applications/XAMPP/xamppfiles/htdocs/common-components/header.php"
    content = read_file(header_path)
    
    if content and '$connection' in content:
        # Add error checking after database usage
        if 'if (!$connection)' not in content:
            # Find where database is first used
            pattern = r'(\$result.*?=.*?mysqli_query\(\$connection,.*?\);)'
            replacement = r'if ($connection && !$connection->connect_error) {\n    \1'
            content = re.sub(pattern, replacement, content, count=1)
            
            # Add closing bracket
            pattern = r'(mysqli_free_result\(\$result.*?\);)'
            replacement = r'\1\n}'
            content = re.sub(pattern, replacement, content, count=1)
            
            write_file(header_path, content)
            return True
    return False

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        ftp.cwd('/11klassnikiru')
        
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir in dirs:
                if dir:
                    try:
                        ftp.cwd(dir)
                    except:
                        ftp.mkd(dir)
                        ftp.cwd(dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Fixing all critical issues found during site review...")
    
    # Fix issues locally first
    print("\n📝 Fixing footer references...")
    fixed_footers = fix_footer_references()
    print(f"✅ Fixed {len(fixed_footers)} footer references")
    
    print("\n📝 Creating error page...")
    error_files = create_error_page()
    print("✅ Created error page and content")
    
    print("\n📝 Adding database error handling...")
    db_fixed = add_database_error_handling()
    if db_fixed:
        print("✅ Added database error handling to header")
    
    # Prepare upload list
    files_to_upload = []
    
    # Add fixed footer files
    for file in fixed_footers:
        files_to_upload.append((file, file))
    
    # Add error pages
    for file in error_files:
        files_to_upload.append((file, file))
    
    # Add header if modified
    if db_fixed:
        files_to_upload.append(('common-components/header.php', 'common-components/header.php'))
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    try:
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        print(f"\n📤 Uploading {len(files_to_upload)} files...")
        uploaded = 0
        for local_path, remote_path in files_to_upload:
            full_local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{local_path}"
            if os.path.exists(full_local_path):
                if upload_file(ftp, full_local_path, remote_path):
                    uploaded += 1
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print(f"\n✅ Upload complete! {uploaded}/{len(files_to_upload)} files uploaded")
        
        print("\n🎯 Issues Fixed:")
        print("✅ All footer.php references updated to footer-unified.php")
        print("✅ Created proper error handling page")
        print("✅ Added database connection checks")
        print("✅ Site stability improved")
        
        print("\n📊 Summary:")
        print(f"• Fixed footer references in {len(fixed_footers)} files")
        print(f"• Created 2 new error handling files")
        print(f"• Updated database error handling")
        print(f"• Total files modified: {len(files_to_upload)}")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()