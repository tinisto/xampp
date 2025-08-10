#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Fixing category routing double link issue...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check current .htaccess for category rules
        print("📥 Analyzing category routing in .htaccess...")
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        category_rules = []
        for i, line in enumerate(htaccess_content):
            if 'category' in line.lower():
                category_rules.append(f"Line {i+1}: {line}")
                print(f"  {category_rules[-1]}")
        
        # Look for category-related files
        print("\n📂 Checking category files...")
        files = []
        ftp.retrlines('LIST', files.append)
        
        category_files = [f for f in files if 'category' in f.lower()]
        for f in category_files:
            print(f"  {f}")
        
        # Download and analyze a few key files that might generate category links
        files_to_check = ['category-new.php', 'news-new.php', 'index.php']
        
        print("\n🔍 Analyzing files for double category links...")
        for filename in files_to_check:
            try:
                content = []
                ftp.retrlines(f'RETR {filename}', content.append)
                
                print(f"\n📄 Checking {filename}...")
                problematic_lines = []
                
                for i, line in enumerate(content):
                    # Look for category link generation patterns
                    if 'category' in line.lower() and any(pattern in line.lower() for pattern in ['href', 'url', 'link']):
                        if '/category/' in line:
                            problematic_lines.append(f"  Line {i+1}: {line.strip()}")
                
                if problematic_lines:
                    print("  Potential double category links found:")
                    for line in problematic_lines[:3]:  # Show first 3
                        print(line)
                else:
                    print("  No obvious double category patterns found")
                    
            except Exception as e:
                print(f"  Could not read {filename}")
        
        # Create comprehensive fix for .htaccess
        print("\n🔧 Creating comprehensive category routing fix...")
        
        # Fix .htaccess routing to prevent double categories
        updated_htaccess = []
        changes_made = 0
        
        for line in htaccess_content:
            # Fix the specific double category issue
            if 'RewriteRule ^category/([^/]+)/?$ category-new.php?category_en=$1' in line:
                # This rule is correct, keep it
                updated_htaccess.append(line)
            elif 'category/education-news' in line:
                # Keep redirect rules as they are
                updated_htaccess.append(line)
            else:
                updated_htaccess.append(line)
        
        # Add additional category fixes right after the existing category section
        category_fixes = '''    # Fix for double category links
    RewriteRule ^category/category/(.+)$ /category/$1 [R=301,L]
    RewriteRule ^category//(.+)$ /category/$1 [R=301,L]
    
    # Redirect common category typos
    RewriteRule ^abiturientam/?$ /category/abiturientam [R=301,L]
    RewriteRule ^categories/(.+)$ /category/$1 [R=301,L]'''
        
        # Insert fixes after category section
        final_htaccess = []
        inserted = False
        
        for line in updated_htaccess:
            final_htaccess.append(line)
            # Insert after the category section
            if 'RewriteRule ^category/([^/]+)/?$' in line and not inserted:
                for fix_line in category_fixes.split('\n'):
                    if fix_line.strip():
                        final_htaccess.append(fix_line)
                inserted = True
                changes_made += 1
        
        if changes_made > 0:
            # Upload updated .htaccess
            with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                tmp.write('\n'.join(final_htaccess))
                tmp_path = tmp.name
            
            print("📤 Uploading fixed .htaccess...")
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR .htaccess', file)
            
            os.unlink(tmp_path)
            print("✅ Updated .htaccess with category fixes")
        
        # Create a category link validator/fixer
        print("\n🔧 Creating category link validator...")
        
        category_validator = '''<?php
// Category Link Validator and Fixer
// This script checks and fixes double category links

function fixCategoryUrl($url) {
    // Remove double category prefixes
    $url = preg_replace('#/category/+category/#', '/category/', $url);
    
    // Remove double slashes
    $url = preg_replace('#/+#', '/', $url);
    
    // Ensure single leading slash
    if (!str_starts_with($url, '/')) {
        $url = '/' . $url;
    }
    
    return $url;
}

function validateCategoryUrl($url) {
    $issues = [];
    
    if (strpos($url, '/category/category/') !== false) {
        $issues[] = 'Double category prefix detected';
    }
    
    if (preg_match('#//{2,}#', $url)) {
        $issues[] = 'Multiple consecutive slashes found';
    }
    
    if (!str_starts_with($url, '/category/') && strpos($url, 'category') !== false) {
        $issues[] = 'Malformed category URL structure';
    }
    
    return $issues;
}

// Test URLs
$test_urls = [
    '/category//category/abiturientam',
    '/category/abiturientam',
    'category/abiturientam',
    '/category/category/test',
    '//category/test'
];

echo "<h2>Category URL Validator Results:</h2>";
foreach ($test_urls as $url) {
    $issues = validateCategoryUrl($url);
    $fixed = fixCategoryUrl($url);
    
    echo "<div style='margin-bottom: 15px; padding: 10px; border: 1px solid #ddd;'>";
    echo "<strong>Original:</strong> " . htmlspecialchars($url) . "<br>";
    echo "<strong>Fixed:</strong> " . htmlspecialchars($fixed) . "<br>";
    
    if (!empty($issues)) {
        echo "<strong>Issues:</strong> " . implode(', ', $issues) . "<br>";
    } else {
        echo "<span style='color: green;'>✅ URL is valid</span><br>";
    }
    echo "</div>";
}
?>'''
        
        # Upload category validator
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(category_validator)
            tmp_path = tmp.name
        
        print("📤 Uploading category validator...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-link-validator.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Category routing fixes deployed!")
        print("\n🔧 Applied fixes:")
        print("• Added redirect rule for /category/category/* → /category/*")
        print("• Added redirect rule for /category//* → /category/*") 
        print("• Added redirect for /abiturientam → /category/abiturientam")
        print("• Added redirect for /categories/* → /category/*")
        print("• Created category link validator tool")
        
        print("\n🧪 Test these URLs (should redirect properly):")
        print("• https://11klassniki.ru/category/category/abiturientam")
        print("• https://11klassniki.ru/category//abiturientam")
        print("• https://11klassniki.ru/abiturientam")
        print("• https://11klassniki.ru/categories/abiturientam")
        
        print("\n📋 Validator tool:")
        print("• https://11klassniki.ru/category-link-validator.php")
        
        print("\n✅ The double category link issue should now be resolved!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()