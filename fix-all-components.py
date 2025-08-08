#!/usr/bin/env python3
"""Fix all component files to be self-contained and not reference real_components.php"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Component files that need to be self-contained
component_files = {
    'common-components/search-inline.php': '''<?php
/**
 * Inline Search Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderSearchInline')) {
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
}
?>''',
    
    'common-components/cards-grid.php': '''<?php
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
?>''',
    
    'common-components/filters-dropdown.php': '''<?php
/**
 * Filters Dropdown Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderFiltersDropdown')) {
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
}
?>''',
    
    'common-components/pagination-modern.php': '''<?php
/**
 * Modern Pagination Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderPaginationModern')) {
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
}
?>''',
    
    'common-components/category-navigation.php': '''<?php
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
?>'''
}

# Create simplified router files
router_files = {
    'tests-new.php': '''<?php
// Tests router
error_reporting(0);

// Default content
$greyContent1 = '<div style="padding: 30px;"><h1>Тесты</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="padding: 20px;"><p>Загрузка тестов...</p></div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Тесты - 11-классники';

// Include the actual page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/tests-main-working.php';
if (file_exists($pageFile)) {
    include $pageFile;
}

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>''',
    
    'spo-all-regions-new.php': '''<?php
// SPO All Regions router
error_reporting(0);

// Default content
$greyContent1 = '<div style="padding: 30px;"><h1>СПО всех регионов</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="padding: 20px;"><p>Загрузка данных...</p></div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'СПО всех регионов - 11-классники';

// Include the actual page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/vpo-spo-all-regions.php';
if (file_exists($pageFile)) {
    // Set type before including
    $_GET['type'] = 'spo';
    include $pageFile;
}

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>''',
    
    'vpo-all-regions-new.php': '''<?php
// VPO All Regions router
error_reporting(0);

// Default content
$greyContent1 = '<div style="padding: 30px;"><h1>ВПО всех регионов</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="padding: 20px;"><p>Загрузка данных...</p></div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'ВПО всех регионов - 11-классники';

// Include the actual page
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/vpo-spo-all-regions.php';
if (file_exists($pageFile)) {
    // Set type before including
    $_GET['type'] = 'vpo';
    include $pageFile;
}

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
}

try:
    print("Fixing component files and routers...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Upload component files
    for filepath, content in component_files.items():
        filename = filepath.split('/')[-1]
        with open(filename, 'w') as f:
            f.write(content)
        
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR {filepath}', f)
        print(f"✓ Fixed {filepath}")
    
    # Upload router files
    for filename, content in router_files.items():
        with open(filename, 'w') as f:
            f.write(content)
        
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR {filename}', f)
        print(f"✓ Fixed {filename}")
    
    ftp.quit()
    
    print("\n✅ All component files fixed!")
    print("\nPages should now work properly:")
    print("- https://11klassniki.ru/news")
    print("- https://11klassniki.ru/tests")
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/vpo-all-regions")
    
except Exception as e:
    print(f"Error: {e}")