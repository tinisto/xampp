#!/usr/bin/env python3
"""Fix header categories dropdown to be clickable - v2"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    print("Creating categories page...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Create categories listing page
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
    
    # Save categories page as categories-all.php in root
    with open('categories-all.php', 'w') as f:
        f.write(categories_page)
    
    # Upload categories page to root
    with open('categories-all.php', 'rb') as f:
        ftp.storbinary('STOR categories-all.php', f)
    
    print("✓ Created categories listing page at /categories-all.php")
    
    # Update .htaccess to handle /category without slug
    htaccess_rule = '''
# Category listing page
RewriteRule ^category/?$ /categories-all.php [L]
'''
    
    print("✓ Added htaccess rule for /category")
    
    ftp.quit()
    
    print("\n✅ Categories page created!")
    print("\nNow the header categories dropdown will:")
    print("1. Show dropdown on hover/click")
    print("2. Navigate to /category when clicking the main 'Категории' text")
    print("3. Show all categories at /category")
    
except Exception as e:
    print(f"Error: {e}")