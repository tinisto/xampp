<?php
/**
 * Real Components Functions Library
 * This file ONLY defines functions - it does not execute any page code
 */

// Prevent direct access
if (basename($_SERVER['SCRIPT_NAME']) === 'real_components_functions.php') {
    header('HTTP/1.1 404 Not Found');
    exit;
}

/**
 * Render the real title component
 */
function renderRealTitle($title = '', $options = []) {
    $fontSize = $options['fontSize'] ?? '32px';
    $margin = $options['margin'] ?? '0';
    $textAlign = $options['textAlign'] ?? 'center';
    $color = $options['color'] ?? '#333';
    $subtitle = $options['subtitle'] ?? '';
    
    ?>
    <div class="real-title" style="text-align: <?= htmlspecialchars($textAlign) ?>; margin: <?= htmlspecialchars($margin) ?>;">
        <h1 style="font-size: <?= htmlspecialchars($fontSize) ?>; color: <?= htmlspecialchars($color) ?>; margin: 0; font-weight: 600;">
            <?= htmlspecialchars($title) ?>
        </h1>
        <?php if ($subtitle): ?>
            <p style="color: #666; margin-top: 10px; font-size: 18px;"><?= htmlspecialchars($subtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Render inline search box
 */
function renderSearchInline($options = []) {
    $placeholder = $options['placeholder'] ?? 'Поиск...';
    $buttonText = $options['buttonText'] ?? 'Найти';
    $action = $options['action'] ?? '/search';
    $method = $options['method'] ?? 'get';
    $paramName = $options['paramName'] ?? 'q';
    $width = $options['width'] ?? '300px';
    $value = $options['value'] ?? '';
    
    ?>
    <form action="<?= htmlspecialchars($action) ?>" method="<?= htmlspecialchars($method) ?>" class="search-inline-form" style="display: inline-flex; gap: 10px; align-items: center;">
        <input type="text" 
               name="<?= htmlspecialchars($paramName) ?>" 
               placeholder="<?= htmlspecialchars($placeholder) ?>" 
               value="<?= htmlspecialchars($value) ?>"
               class="form-control" 
               style="width: <?= htmlspecialchars($width) ?>; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
        <button type="submit" class="btn btn-success" style="padding: 8px 20px; white-space: nowrap;">
            <?= htmlspecialchars($buttonText) ?>
        </button>
    </form>
    <?php
}

/**
 * Render category navigation tabs
 */
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

/**
 * Render filters dropdown
 */
function renderFiltersDropdown($options = []) {
    $sortOptions = $options['sortOptions'] ?? [
        'date_desc' => 'По дате (новые)',
        'date_asc' => 'По дате (старые)',
        'popular' => 'По популярности'
    ];
    $currentSort = $options['currentSort'] ?? 'date_desc';
    $label = $options['label'] ?? 'Сортировать:';
    
    ?>
    <div class="filters-dropdown" style="display: inline-flex; align-items: center; gap: 10px;">
        <?php if ($label): ?>
            <label style="color: #666; font-size: 14px;"><?= htmlspecialchars($label) ?></label>
        <?php endif; ?>
        <select class="form-select" style="width: auto; padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; background: white; color: #333;" 
                onchange="window.location.href = updateQueryStringParameter(window.location.href, 'sort', this.value)">
            <?php foreach ($sortOptions as $value => $text): ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $currentSort === $value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($text) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <script>
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
    </script>
    <?php
}

/**
 * Render cards grid
 */
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

/**
 * Render pagination
 */
function renderPaginationModern($currentPage = 1, $totalPages = 1, $baseUrl = '') {
    if ($totalPages <= 1) return;
    
    $range = 2; // Pages to show on each side of current
    ?>
    <nav class="pagination-modern" style="display: flex; justify-content: center; align-items: center; gap: 5px; margin: 30px 0;">
        <?php if ($currentPage > 1): ?>
            <a href="<?= htmlspecialchars($baseUrl) ?>?page=<?= $currentPage - 1 ?>" 
               class="page-link" 
               style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                ←
            </a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range)): ?>
                <a href="<?= htmlspecialchars($baseUrl) ?>?page=<?= $i ?>" 
                   class="page-link <?= $i == $currentPage ? 'active' : '' ?>"
                   style="padding: 8px 12px; border: 1px solid <?= $i == $currentPage ? '#28a745' : '#ddd' ?>; 
                          border-radius: 4px; text-decoration: none; 
                          color: <?= $i == $currentPage ? 'white' : '#333' ?>;
                          background: <?= $i == $currentPage ? '#28a745' : 'white' ?>;">
                    <?= $i ?>
                </a>
            <?php elseif ($i == $currentPage - $range - 1 || $i == $currentPage + $range + 1): ?>
                <span style="padding: 8px;">...</span>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= htmlspecialchars($baseUrl) ?>?page=<?= $currentPage + 1 ?>" 
               class="page-link" 
               style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                →
            </a>
        <?php endif; ?>
    </nav>
    <?php
}

/**
 * Render breadcrumb navigation
 */
function renderBreadcrumb($items = []) {
    if (empty($items)) return;
    ?>
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol style="list-style: none; padding: 0; margin: 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <li>
                <a href="/" style="color: #28a745; text-decoration: none;">Главная</a>
            </li>
            <?php foreach ($items as $index => $item): ?>
                <li style="display: flex; align-items: center; gap: 10px;">
                    <span style="color: #999;">›</span>
                    <?php if (isset($item['url']) && $index < count($items) - 1): ?>
                        <a href="<?= htmlspecialchars($item['url']) ?>" style="color: #28a745; text-decoration: none;">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    <?php else: ?>
                        <span style="color: #666;"><?= htmlspecialchars($item['title']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

/**
 * Render news grid (specific implementation)
 */
function renderNewsGrid($news = [], $options = []) {
    return renderCardsGrid($news, 'news', array_merge(['columns' => 3, 'showBadge' => true], $options));
}

/**
 * Render test card
 */
function renderTestCard($test, $options = []) {
    $showStartButton = $options['showStartButton'] ?? true;
    ?>
    <div class="test-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: white;">
        <h3 style="margin: 0 0 15px 0;"><?= htmlspecialchars($test['title_test'] ?? 'Test') ?></h3>
        
        <div style="display: flex; gap: 20px; margin-bottom: 15px; color: #666;">
            <span>📝 <?= $test['questions_count'] ?? 0 ?> вопросов</span>
            <span>⏱ <?= $test['duration'] ?? 30 ?> минут</span>
            <span>📊 <?= $test['difficulty'] ?? 'Средний' ?></span>
        </div>
        
        <?php if (!empty($test['description'])): ?>
            <p style="color: #666; margin-bottom: 15px;"><?= htmlspecialchars($test['description']) ?></p>
        <?php endif; ?>
        
        <?php if ($showStartButton): ?>
            <a href="/test/<?= htmlspecialchars($test['url_test'] ?? '') ?>" 
               class="btn btn-success" 
               style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">
                Начать тест
            </a>
        <?php endif; ?>
    </div>
    <?php
}
?>