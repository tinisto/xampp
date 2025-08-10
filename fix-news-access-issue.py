#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def upload_files():
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected")
        
        ftp.cwd(FTP_ROOT)
        
        # Create/ensure directories exist
        try:
            ftp.mkd('common-components')
        except:
            pass  # Directory might already exist
        
        # Upload missing category-navigation.php component
        print("Uploading category-navigation.php...")
        category_nav_content = '''<?php
function renderCategoryNavigation($items, $currentPath = '') {
    if (empty($items)) return;
    
    echo '<div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #e9ecef;">';
    echo '<nav style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">';
    
    foreach ($items as $item) {
        $isActive = ($currentPath === $item['url']);
        $activeStyle = $isActive ? 'color: #007bff; font-weight: bold; border-bottom: 2px solid #007bff;' : 'color: #6c757d;';
        
        echo '<a href="' . htmlspecialchars($item['url']) . '" ';
        echo 'style="text-decoration: none; padding: 10px 0; ' . $activeStyle . '">';
        echo htmlspecialchars($item['title']);
        echo '</a>';
    }
    
    echo '</nav>';
    echo '</div>';
}
?>'''
        
        # Upload category navigation
        ftp.cwd('common-components')
        with open('temp_category_nav.php', 'w', encoding='utf-8') as f:
            f.write(category_nav_content)
        
        with open('temp_category_nav.php', 'rb') as f:
            ftp.storbinary('STOR category-navigation.php', f)
        print("✓ Uploaded category-navigation.php")
        
        # Clean up temp file
        os.remove('temp_category_nav.php')
        
        # Go back to root and check .htaccess
        ftp.cwd('..')
        
        # Download current .htaccess to see the rules
        try:
            with open('current_htaccess.txt', 'wb') as f:
                ftp.retrbinary('RETR .htaccess', f.write)
            print("✓ Downloaded current .htaccess")
            
            # Read and analyze
            with open('current_htaccess.txt', 'r') as f:
                htaccess_content = f.read()
            
            print(f"Current .htaccess length: {len(htaccess_content)} characters")
            
            # Look for news rules
            lines = htaccess_content.split('\\n')
            news_rules = [line for line in lines if 'news' in line.lower()]
            
            if news_rules:
                print("Found news-related rules:")
                for rule in news_rules[:5]:  # Show first 5 rules
                    print(f"  {rule}")
            
            # Clean up
            os.remove('current_htaccess.txt')
            
        except Exception as e:
            print(f"Could not download .htaccess: {e}")
        
        ftp.quit()
        print("\\n✓ Fix deployed!")
        print("\\nTest URLs:")
        print("- Direct access: https://11klassniki.ru/news-new.php")
        print("- Test page: https://11klassniki.ru/test-news-simple.php")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Fixing News Access Issue ===")
    upload_files()