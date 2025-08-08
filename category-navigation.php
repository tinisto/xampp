<?php
/**
 * Category Navigation Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderCategoryNavigation')) {
    function renderCategoryNavigation($items = [], $activeUrl = '') {
        if (empty($items)) return;
        ?>
        <nav class="category-navigation" style="border-bottom: 1px solid #ddd; margin-bottom: 20px;">
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; gap: 0; flex-wrap: wrap;">
                <?php foreach ($items as $item): 
                    $isActive = ($activeUrl === $item['url'] || strpos($activeUrl, $item['url']) === 0);
                ?>
                    <li>
                        <a href="<?= htmlspecialchars($item['url']) ?>" 
                           class="<?= $isActive ? 'active' : '' ?>"
                           style="display: block; padding: 12px 20px; text-decoration: none; 
                                  color: <?= $isActive ? '#28a745' : '#333' ?>; 
                                  border-bottom: 2px solid <?= $isActive ? '#28a745' : 'transparent' ?>;
                                  transition: all 0.3s ease;">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }
}
?>