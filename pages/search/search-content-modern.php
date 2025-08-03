<?php 
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Security.php';

// Include loading placeholders
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders.php';
?>

<style>
    .search-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    .search-header {
        margin-bottom: 30px;
    }
    .search-query-info {
        background: var(--bg-secondary);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 30px;
    }
    .search-query-label {
        color: var(--text-secondary);
        font-size: 14px;
        margin-bottom: 5px;
    }
    .search-query-text {
        font-size: 24px;
        font-weight: 600;
        color: var(--text-primary);
    }
    .search-results-count {
        color: var(--text-secondary);
        font-size: 14px;
        margin-top: 10px;
    }
    .search-results {
        margin-bottom: 40px;
    }
    .search-result-item {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.3s ease;
        display: block;
        text-decoration: none;
        color: inherit;
    }
    .search-result-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        border-color: rgba(102, 126, 234, 0.3);
    }
    .search-result-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        line-height: 1.4;
    }
    .search-result-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-result-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        border: 1px solid rgba(102, 126, 234, 0.2);
    }
    .search-result-badge.school {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border-color: rgba(40, 167, 69, 0.2);
    }
    .search-result-badge.college {
        background: rgba(255, 152, 0, 0.1);
        color: #ff9800;
        border-color: rgba(255, 152, 0, 0.2);
    }
    .search-result-badge.university {
        background: rgba(156, 39, 176, 0.1);
        color: #9c27b0;
        border-color: rgba(156, 39, 176, 0.2);
    }
    .search-result-badge.post {
        background: rgba(0, 123, 255, 0.1);
        color: #007bff;
        border-color: rgba(0, 123, 255, 0.2);
    }
    .no-results {
        text-align: center;
        padding: 60px 20px;
    }
    .no-results-icon {
        font-size: 64px;
        color: var(--text-secondary);
        margin-bottom: 20px;
        opacity: 0.5;
    }
    .no-results-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 10px;
    }
    .no-results-text {
        color: var(--text-secondary);
        margin-bottom: 30px;
    }
    .suggestions-section {
        background: var(--bg-secondary);
        border-radius: 12px;
        padding: 30px;
        margin-top: 40px;
    }
    .suggestions-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--text-primary);
    }
    .suggestion-items {
        display: grid;
        gap: 10px;
    }
    .suggestion-item {
        padding: 12px 20px;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .suggestion-item:hover {
        border-color: #667eea;
        color: #667eea;
    }
    .suggestion-icon {
        font-size: 16px;
        opacity: 0.7;
    }
    
    /* Pagination */
    .search-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 40px;
    }
    .pagination-btn {
        padding: 8px 16px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-primary);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .pagination-btn:hover {
        border-color: #667eea;
        color: #667eea;
    }
    .pagination-btn.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    .pagination-btn.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
    
    /* Dark mode */
    [data-bs-theme="dark"] .search-query-info {
        background: #1a202c;
    }
    [data-bs-theme="dark"] .search-result-item {
        background: #1a202c;
        border-color: rgba(255,255,255,0.1);
    }
    [data-bs-theme="dark"] .search-result-item:hover {
        border-color: rgba(102, 126, 234, 0.5);
    }
    [data-bs-theme="dark"] .suggestions-section {
        background: #1a202c;
    }
    [data-bs-theme="dark"] .suggestion-item {
        background: #2d3748;
        border-color: rgba(255,255,255,0.1);
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .search-container {
            padding: 20px 15px;
        }
        .search-query-text {
            font-size: 20px;
        }
        .search-result-item {
            padding: 15px;
        }
        .search-result-title {
            font-size: 16px;
        }
        .suggestions-section {
            padding: 20px;
        }
    }
</style>

<div class="search-container">
    <?php if (!empty($additionalData['searchQuery'])): ?>
        <?php
        $db = new Database($connection);
        
        // Pagination setup
        $itemsPerPage = 10;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $startIndex = ($currentPage - 1) * $itemsPerPage;
        
        // Define search configurations
        $searchConfigs = [
            [
                'table' => 'schools',
                'fields' => ['school_name', 'short_name', 'email'],
                'type' => 'school',
                'idField' => 'id_school',
                'nameField' => 'school_name',
                'urlPattern' => '/school/%s',
                'badge' => 'Школы'
            ],
            [
                'table' => 'spo',
                'fields' => ['spo_name', 'short_name', 'old_name', 'email', 'email_pk'],
                'type' => 'college',
                'idField' => 'id_spo',
                'nameField' => 'spo_name',
                'urlField' => 'spo_url',
                'urlPattern' => '/spo/%s',
                'badge' => 'ССУЗы'
            ],
            [
                'table' => 'vpo',
                'fields' => ['vpo_name', 'short_name', 'old_name', 'email', 'email_pk'],
                'type' => 'university',
                'idField' => 'id_vpo',
                'nameField' => 'vpo_name',
                'urlField' => 'vpo_url',
                'urlPattern' => '/vpo/%s',
                'badge' => 'ВУЗы'
            ],
            [
                'table' => 'posts',
                'fields' => ['title_post', 'text_post'],
                'type' => 'post',
                'idField' => 'id_posts',
                'nameField' => 'title_post',
                'urlField' => 'url_post',
                'urlPattern' => '/post/%s',
                'badge' => 'Статьи'
            ]
        ];
        
        // Perform secure searches
        $results = [];
        $searchQuery = $additionalData['searchQuery'];
        
        foreach ($searchConfigs as $config) {
            $table = $db->escapeIdentifier($config['table']);
            $conditions = [];
            $params = [];
            
            foreach ($config['fields'] as $field) {
                $conditions[] = "TRIM(" . $db->escapeIdentifier($field) . ") LIKE ?";
                $params[] = '%' . $searchQuery . '%';
            }
            
            $sql = "SELECT *, ? AS type, ? AS badge FROM $table WHERE " . implode(" OR ", $conditions);
            array_unshift($params, $config['type'], $config['badge']);
            
            $searchResults = $db->queryAll($sql, $params);
            
            // Add configuration data to each result
            foreach ($searchResults as &$result) {
                $result['_config'] = $config;
            }
            
            $results = array_merge($results, $searchResults);
        }
        
        // Calculate total results
        $totalResults = count($results);
        ?>
        
        <div class="search-header">
            <div class="search-query-info">
                <div class="search-query-label">Результаты поиска для:</div>
                <div class="search-query-text"><?= Security::cleanOutput($searchQuery) ?></div>
                <div class="search-results-count">
                    <?php if ($totalResults > 0): ?>
                        Найдено: <?= $totalResults ?> <?= ($totalResults % 10 == 1 && $totalResults % 100 != 11) ? 'результат' : (($totalResults % 10 >= 2 && $totalResults % 10 <= 4) && ($totalResults % 100 < 12 || $totalResults % 100 > 14) ? 'результата' : 'результатов') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if ($totalResults > 0): ?>
            <div class="search-results">
                <?php
                for ($i = $startIndex; $i < min($startIndex + $itemsPerPage, $totalResults); $i++) {
                    $row = $results[$i];
                    $config = $row['_config'];
                    
                    // Determine URL
                    if (isset($config['urlField']) && !empty($row[$config['urlField']])) {
                        $url = sprintf($config['urlPattern'], Security::cleanOutput($row[$config['urlField']]));
                    } else {
                        $url = sprintf($config['urlPattern'], $row[$config['idField']] ?? 0);
                    }
                    
                    $name = Security::cleanOutput($row[$config['nameField']] ?? '');
                    $badge = Security::cleanOutput($row['badge']);
                    $type = $row['type'];
                    ?>
                    <a href="<?= $url ?>" class="search-result-item">
                        <div class="search-result-title"><?= $name ?></div>
                        <div class="search-result-meta">
                            <span class="search-result-badge <?= $type ?>"><?= $badge ?></span>
                        </div>
                    </a>
                    <?php
                }
                ?>
            </div>
            
            <?php
            // Pagination
            $totalPages = ceil($totalResults / $itemsPerPage);
            if ($totalPages > 1): ?>
                <div class="search-pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?query=<?= urlencode($searchQuery) ?>&page=<?= $currentPage - 1 ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    
                    if ($start > 1): ?>
                        <a href="?query=<?= urlencode($searchQuery) ?>&page=1" class="pagination-btn">1</a>
                        <?php if ($start > 2): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($page = $start; $page <= $end; $page++): ?>
                        <a href="?query=<?= urlencode($searchQuery) ?>&page=<?= $page ?>" 
                           class="pagination-btn <?= $page == $currentPage ? 'active' : '' ?>">
                            <?= $page ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <span>...</span>
                        <?php endif; ?>
                        <a href="?query=<?= urlencode($searchQuery) ?>&page=<?= $totalPages ?>" class="pagination-btn"><?= $totalPages ?></a>
                    <?php endif; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?query=<?= urlencode($searchQuery) ?>&page=<?= $currentPage + 1 ?>" class="pagination-btn">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h2 class="no-results-title">Ничего не найдено</h2>
                <p class="no-results-text">
                    По вашему запросу «<?= Security::cleanOutput($searchQuery) ?>» ничего не найдено.<br>
                    Попробуйте изменить запрос или воспользуйтесь предложениями ниже.
                </p>
            </div>
            
            <div class="suggestions-section">
                <h3 class="suggestions-title">Попробуйте поискать:</h3>
                <div class="suggestion-items">
                    <a href="/vpo-all-regions" class="suggestion-item">
                        <i class="fas fa-university suggestion-icon"></i>
                        <span>Все ВУЗы России</span>
                    </a>
                    <a href="/spo-all-regions" class="suggestion-item">
                        <i class="fas fa-graduation-cap suggestion-icon"></i>
                        <span>Колледжи и техникумы</span>
                    </a>
                    <a href="/schools-all-regions" class="suggestion-item">
                        <i class="fas fa-school suggestion-icon"></i>
                        <span>Школы по регионам</span>
                    </a>
                    <a href="/news" class="suggestion-item">
                        <i class="fas fa-newspaper suggestion-icon"></i>
                        <span>Последние новости</span>
                    </a>
                    <a href="/tests" class="suggestion-item">
                        <i class="fas fa-clipboard-check suggestion-icon"></i>
                        <span>Тесты и профориентация</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- No search query - show search form -->
        <div class="search-header">
            <h1 class="text-center mb-4">Поиск по сайту</h1>
            <?php include 'search-form.php'; ?>
        </div>
        
        <div class="suggestions-section">
            <h3 class="suggestions-title">Популярные разделы:</h3>
            <div class="suggestion-items">
                <a href="/vpo-all-regions" class="suggestion-item">
                    <i class="fas fa-university suggestion-icon"></i>
                    <span>Все ВУЗы России</span>
                </a>
                <a href="/spo-all-regions" class="suggestion-item">
                    <i class="fas fa-graduation-cap suggestion-icon"></i>
                    <span>Колледжи и техникумы</span>
                </a>
                <a href="/schools-all-regions" class="suggestion-item">
                    <i class="fas fa-school suggestion-icon"></i>
                    <span>Школы по регионам</span>
                </a>
                <a href="/news" class="suggestion-item">
                    <i class="fas fa-newspaper suggestion-icon"></i>
                    <span>Последние новости</span>
                </a>
                <a href="/tests" class="suggestion-item">
                    <i class="fas fa-clipboard-check suggestion-icon"></i>
                    <span>Тесты и профориентация</span>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>