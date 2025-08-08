<?php
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
?>