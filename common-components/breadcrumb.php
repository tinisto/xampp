<?php
/**
 * Reusable Breadcrumb Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
 * renderBreadcrumb([
 *     ['text' => 'Главная', 'url' => '/'],
 *     ['text' => 'Школы России', 'url' => '/schools-all-regions'],
 *     ['text' => 'Калининградская область'] // Last item without URL
 * ]);
 */

function renderBreadcrumb($items = []) {
    if (empty($items)) return;
    
    // Include SEO helper if available
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/functions/seo.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/seo.php';
        
        // Convert items to SEO format
        $seoItems = [];
        foreach ($items as $item) {
            $seoItems[] = [
                'name' => $item['text'],
                'url' => $item['url'] ?? null
            ];
        }
        
        echo SEOHelper::generateBreadcrumbs($seoItems);
        return;
    }
    
    // Fallback to original breadcrumb with basic structured data
    ?>
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
            <?php 
            $validItems = array_filter($items, function($item) {
                return !empty($item['text']);
            });
            $position = 0;
            foreach ($validItems as $index => $item): 
                $position++;
                $isLast = ($index === array_key_last($validItems));
            ?>
                <li class="breadcrumb-item<?= $isLast ? ' active' : '' ?>" 
                    itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                    <?= $isLast ? 'aria-current="page"' : '' ?>>
                    
                    <?php if (!$isLast && isset($item['url'])): ?>
                        <a href="<?= htmlspecialchars($item['url']) ?>" itemprop="item">
                            <span itemprop="name"><?= htmlspecialchars($item['text']) ?></span>
                        </a>
                    <?php else: ?>
                        <span itemprop="name"><?= htmlspecialchars($item['text']) ?></span>
                    <?php endif; ?>
                    
                    <meta itemprop="position" content="<?= $position ?>">
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

// Include CSS only once
if (!defined('BREADCRUMB_CSS_INCLUDED')) {
    define('BREADCRUMB_CSS_INCLUDED', true);
    ?>
    <style>
        .breadcrumb-nav {
            margin-bottom: 10px;
        }
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            list-style: none;
        }
        .breadcrumb-item {
            font-size: 14px;
            color: #666;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            padding: 0 8px;
            color: #999;
        }
        .breadcrumb-item a {
            color: #28a745;
            text-decoration: none;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        .breadcrumb-item.active {
            color: #666;
        }
        @media (max-width: 576px) {
            .breadcrumb {
                font-size: 13px;
            }
            .breadcrumb-item + .breadcrumb-item::before {
                padding: 0 5px;
            }
        }
    </style>
    <?php
}
?>