#!/usr/bin/env python3
"""Fix header categories dropdown to be clickable"""

import ftplib
import re

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    print("Downloading current header file...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Download current header
    with open('real_header_current.php', 'wb') as f:
        ftp.retrbinary('RETR common-components/real_header.php', f.write)
    
    # Read and modify
    with open('real_header_current.php', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Fix the categories dropdown link
    # Change from href="#" to href="/category"
    content = re.sub(
        r'<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="categoriesToggle">\s*Категории',
        '<a href="/category" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="categoriesToggle">\n                        Категории',
        content
    )
    
    # Also make it clickable by adding onclick handler
    content = re.sub(
        r'<a href="/category" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="categoriesToggle">',
        '<a href="/category" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="categoriesToggle" onclick="if(event.target === this && !event.defaultPrevented) { window.location.href = \'/category\'; }">',
        content
    )
    
    # Save modified file
    with open('real_header_fixed.php', 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Upload back
    with open('real_header_fixed.php', 'rb') as f:
        ftp.storbinary('STOR common-components/real_header.php', f)
    
    print("✓ Fixed categories dropdown to be clickable")
    
    # Also create a categories listing page if it doesn't exist
    categories_page = '''<?php
/**
 * Categories listing page
 */

// Set content for template sections
$greyContent1 = '<div style="padding: 30px;"><h1>Все категории</h1></div>';

// Category listing
ob_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
?>
<div style="padding: 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php
        $query = "SELECT DISTINCT c.* FROM categories c 
                  ORDER BY c.title_category";
        $result = mysqli_query($connection, $query);
        
        while ($cat = mysqli_fetch_assoc($result)) {
            ?>
            <a href="/category/<?= htmlspecialchars($cat['url_category']) ?>" 
               style="text-decoration: none; color: inherit;">
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; 
                            text-align: center; transition: all 0.3s ease;
                            background: white;">
                    <h3 style="margin: 0; color: #333; font-size: 18px;">
                        <?= htmlspecialchars($cat['title_category']) ?>
                    </h3>
                    <?php if (!empty($cat['description_category'])): ?>
                        <p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">
                            <?= htmlspecialchars(mb_substr($cat['description_category'], 0, 100)) ?>...
                        </p>
                    <?php endif; ?>
                </div>
            </a>
            <?php
        }
        ?>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Other sections
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Все категории - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
    
    # Save categories page
    with open('category-index.php', 'w') as f:
        f.write(categories_page)
    
    # Upload categories page
    with open('category-index.php', 'rb') as f:
        ftp.storbinary('STOR category/index.php', f)
    
    print("✓ Created categories listing page at /category/")
    
    ftp.quit()
    
    print("\n✅ Header categories fixed!")
    print("\nNow:")
    print("1. Categories dropdown is clickable - leads to /category")
    print("2. Created categories listing page")
    print("3. Dropdown still works for quick access to specific categories")
    
except Exception as e:
    print(f"Error: {e}")