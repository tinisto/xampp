<?php
// Breadcrumbs navigation component

function render_breadcrumbs($items = []) {
    if (empty($items)) {
        return;
    }
    
    ?>
    <nav aria-label="breadcrumb" style="padding: 20px 0;">
        <ol style="display: flex; align-items: center; gap: 10px; list-style: none; 
                   margin: 0; padding: 0; font-size: 14px; color: var(--text-secondary);">
            <li>
                <a href="/" style="color: var(--text-secondary); text-decoration: none; 
                                   display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-home"></i>
                    Главная
                </a>
            </li>
            
            <?php foreach ($items as $index => $item): ?>
            <li style="display: flex; align-items: center; gap: 10px;">
                <span style="color: var(--text-secondary);">/</span>
                <?php if ($index === count($items) - 1): ?>
                    <span style="color: var(--text-primary);"><?= htmlspecialchars($item['title']) ?></span>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>" 
                       style="color: var(--text-secondary); text-decoration: none;">
                        <?= htmlspecialchars($item['title']) ?>
                    </a>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Главная",
                "item": "https://11klassniki.ru/"
            }<?php foreach ($items as $index => $item): ?>,
            {
                "@type": "ListItem",
                "position": <?= $index + 2 ?>,
                "name": "<?= htmlspecialchars($item['title']) ?>",
                <?php if ($index < count($items) - 1): ?>
                "item": "https://11klassniki.ru<?= htmlspecialchars($item['url']) ?>"
                <?php endif; ?>
            }<?php endforeach; ?>
        ]
    }
    </script>
    <?php
}

// Helper function to generate breadcrumbs for different page types
function get_breadcrumbs($pageType, $data = []) {
    $breadcrumbs = [];
    
    switch ($pageType) {
        case 'news-list':
            $breadcrumbs[] = ['title' => 'Новости', 'url' => '/news'];
            break;
            
        case 'news-single':
            $breadcrumbs[] = ['title' => 'Новости', 'url' => '/news'];
            if (isset($data['category_name'])) {
                $breadcrumbs[] = ['title' => $data['category_name'], 'url' => '/news?category=' . $data['category_id']];
            }
            $breadcrumbs[] = ['title' => $data['title'], 'url' => null];
            break;
            
        case 'posts-list':
            $breadcrumbs[] = ['title' => 'Статьи', 'url' => '/posts'];
            break;
            
        case 'post-single':
            $breadcrumbs[] = ['title' => 'Статьи', 'url' => '/posts'];
            if (isset($data['category_name'])) {
                $breadcrumbs[] = ['title' => $data['category_name'], 'url' => '/posts?category=' . $data['category_id']];
            }
            $breadcrumbs[] = ['title' => $data['title'], 'url' => null];
            break;
            
        case 'vpo-list':
            $breadcrumbs[] = ['title' => 'ВУЗы', 'url' => '/vpo'];
            break;
            
        case 'vpo-single':
            $breadcrumbs[] = ['title' => 'ВУЗы', 'url' => '/vpo'];
            if (isset($data['region_name'])) {
                $breadcrumbs[] = ['title' => $data['region_name'], 'url' => '/vpo?region=' . $data['region_id']];
            }
            $breadcrumbs[] = ['title' => $data['title'], 'url' => null];
            break;
            
        case 'spo-list':
            $breadcrumbs[] = ['title' => 'ССУЗы', 'url' => '/spo'];
            break;
            
        case 'spo-single':
            $breadcrumbs[] = ['title' => 'ССУЗы', 'url' => '/spo'];
            if (isset($data['region_name'])) {
                $breadcrumbs[] = ['title' => $data['region_name'], 'url' => '/spo?region=' . $data['region_id']];
            }
            $breadcrumbs[] = ['title' => $data['title'], 'url' => null];
            break;
            
        case 'schools-list':
            $breadcrumbs[] = ['title' => 'Школы', 'url' => '/schools'];
            break;
            
        case 'school-single':
            $breadcrumbs[] = ['title' => 'Школы', 'url' => '/schools'];
            if (isset($data['region_name'])) {
                $breadcrumbs[] = ['title' => $data['region_name'], 'url' => '/schools?region=' . $data['region_id']];
            }
            $breadcrumbs[] = ['title' => $data['title'], 'url' => null];
            break;
            
        case 'search':
            $breadcrumbs[] = ['title' => 'Поиск', 'url' => '/search'];
            if (isset($data['query'])) {
                $breadcrumbs[] = ['title' => 'Результаты для: ' . $data['query'], 'url' => null];
            }
            break;
            
        case 'profile':
            $breadcrumbs[] = ['title' => 'Личный кабинет', 'url' => '/profile'];
            break;
            
        case 'favorites':
            $breadcrumbs[] = ['title' => 'Личный кабинет', 'url' => '/profile'];
            $breadcrumbs[] = ['title' => 'Избранное', 'url' => '/favorites'];
            break;
            
        case 'login':
            $breadcrumbs[] = ['title' => 'Вход', 'url' => '/login'];
            break;
            
        case 'register':
            $breadcrumbs[] = ['title' => 'Регистрация', 'url' => '/register'];
            break;
            
        case 'privacy':
            $breadcrumbs[] = ['title' => 'Политика конфиденциальности', 'url' => '/privacy'];
            break;
    }
    
    return $breadcrumbs;
}
?>