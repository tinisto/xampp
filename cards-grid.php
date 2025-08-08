<?php
/**
 * Cards Grid Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderCardsGrid')) {
    function renderCardsGrid($items = [], $type = 'news', $options = []) {
        $columns = $options['columns'] ?? 3;
        $gap = $options['gap'] ?? 20;
        $showBadge = $options['showBadge'] ?? false;
        
        if (empty($items)) {
            echo '<p style="text-align: center; color: #666;">Нет элементов для отображения</p>';
            return;
        }
        
        ?>
        <div class="cards-grid" style="display: grid; grid-template-columns: repeat(<?= $columns ?>, 1fr); gap: <?= $gap ?>px;">
            <?php foreach ($items as $item): ?>
                <?php
                // Determine URLs based on type
                switch ($type) {
                    case 'news':
                        $url = '/news/' . ($item['url_news'] ?? '');
                        $title = $item['title_news'] ?? 'Без названия';
                        $image = $item['image_news'] ?? '/images/default-news.jpg';
                        break;
                    case 'post':
                        $url = '/post/' . ($item['url_news'] ?? $item['url_post'] ?? '');
                        $title = $item['title_news'] ?? $item['title_post'] ?? 'Без названия';
                        $image = $item['image_news'] ?? $item['image_post'] ?? '/images/default-post.jpg';
                        break;
                    case 'test':
                        $url = '/test/' . ($item['url_test'] ?? '');
                        $title = $item['title_test'] ?? 'Без названия';
                        $image = $item['image_test'] ?? '/images/default-test.jpg';
                        break;
                    case 'school':
                        $url = '/school/' . ($item['url_school'] ?? '');
                        $title = $item['name_school'] ?? 'Без названия';
                        $image = $item['image_school'] ?? '/images/default-school.jpg';
                        break;
                    default:
                        $url = '#';
                        $title = 'Unknown type';
                        $image = '/images/default.jpg';
                }
                ?>
                <div class="card" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; transition: transform 0.2s;">
                    <?php if ($showBadge && !empty($item['category_title'])): ?>
                        <div style="position: absolute; top: 10px; left: 10px; z-index: 1;">
                            <a href="/category/<?= htmlspecialchars($item['category_url'] ?? '') ?>" 
                               class="badge" 
                               style="background: #28a745; color: white; padding: 4px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                <?= htmlspecialchars($item['category_title']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?= htmlspecialchars($url) ?>" style="text-decoration: none; color: inherit;">
                        <div style="aspect-ratio: 16/9; background: #f0f0f0; position: relative; overflow: hidden;">
                            <img src="<?= htmlspecialchars($image) ?>" 
                                 alt="<?= htmlspecialchars($title) ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.src='/images/default.jpg'">
                        </div>
                        <div style="padding: 15px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 18px; line-height: 1.4;">
                                <?= htmlspecialchars($title) ?>
                            </h3>
                            
                            <?php if ($type === 'test' && isset($item['questions_count'])): ?>
                                <div style="display: flex; gap: 15px; color: #666; font-size: 14px;">
                                    <span>📝 <?= $item['questions_count'] ?> вопросов</span>
                                    <span>⏱ <?= $item['duration'] ?? 30 ?> мин</span>
                                    <span>📊 <?= $item['difficulty'] ?? 'Средний' ?></span>
                                </div>
                            <?php elseif (!empty($item['created_at'])): ?>
                                <div style="color: #666; font-size: 14px;">
                                    <?= date('d.m.Y', strtotime($item['created_at'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
?>