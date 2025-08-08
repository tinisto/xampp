#!/usr/bin/env python3
"""Fix category page routing"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Create category index page at root level
category_index_page = '''<?php
/**
 * Categories index page - accessible at /category
 */

// Redirect to categories-all.php which has the proper content
header('Location: /categories-all.php');
exit;
?>'''

# Also create the categories directory structure
category_index_dir = '''<?php
/**
 * Categories listing page in category directory
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Set content for template sections
$greyContent1 = '<div style="padding: 30px; text-align: center;"><h1>Все категории</h1></div>';

// Category listing
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php
        try {
            $query = "SELECT DISTINCT c.* FROM categories c 
                      ORDER BY c.title_category";
            $result = mysqli_query($connection, $query);
            
            $hasCategories = false;
            while ($cat = mysqli_fetch_assoc($result)) {
                $hasCategories = true;
                ?>
                <a href="/category/<?= htmlspecialchars($cat['url_category']) ?>" 
                   style="text-decoration: none; color: inherit;">
                    <div style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; 
                                text-align: center; transition: all 0.3s ease;
                                background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 10px 0; color: #333; font-size: 18px;">
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
            
            if (!$hasCategories) {
                echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">';
                echo '<i class="fas fa-folder-open fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>';
                echo '<p>Категории не найдены</p>';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">';
            echo '<p>Ошибка загрузки категорий</p>';
            echo '</div>';
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

# Updated htaccess rules
htaccess_addition = '''
# Categories routing
RewriteRule ^category/?$ /categories-all.php [L]
RewriteRule ^categories/?$ /categories-all.php [L]

# Single test pages  
RewriteRule ^test/([a-zA-Z0-9-]+)/?$ /test-single-real.php?url_test=$1 [L]
'''

try:
    print("Fixing category routing...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Create category index redirect
    with open('category-index-redirect.php', 'w') as f:
        f.write(category_index_page)
    
    with open('category-index-redirect.php', 'rb') as f:
        ftp.storbinary('STOR category.php', f)
    
    print("✓ Created category.php redirect")
    
    # Try to create category directory and index
    try:
        ftp.mkd('category')
        print("✓ Created category directory")
    except:
        print("- Category directory already exists")
    
    with open('category-index.php', 'w') as f:
        f.write(category_index_dir)
    
    with open('category-index.php', 'rb') as f:
        ftp.storbinary('STOR category/index.php', f)
    
    print("✓ Created category/index.php")
    
    ftp.quit()
    
    print("\n✅ Category routing fixed!")
    print("\nNow these should work:")
    print("- https://11klassniki.ru/category (redirects to categories listing)")
    print("- https://11klassniki.ru/categories-all.php (direct access)")
    print("- https://11klassniki.ru/category/CATEGORY_NAME (individual categories)")
    print("\nNote: You may need to add this to .htaccess:")
    print("RewriteRule ^category/?$ /categories-all.php [L]")
    
except Exception as e:
    print(f"Error: {e}")