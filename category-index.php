<?php
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
?>