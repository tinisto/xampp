<?php
// Search page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Security.php';

// Get search query
$searchQuery = $_GET['q'] ?? '';

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Поиск', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Найдите школы, ВУЗы, СПО и статьи'
]);
$greyContent1 = ob_get_clean();

// Section 2: Search form
ob_start();
?>
<div style="padding: 20px;">
    <form method="get" action="/search" style="max-width: 600px; margin: 0 auto;">
        <div style="display: flex; gap: 10px;">
            <input type="text" 
                   name="q" 
                   value="<?= htmlspecialchars($searchQuery) ?>" 
                   placeholder="Поиск по сайту..." 
                   style="flex: 1; padding: 12px 20px; border: 1px solid #ddd; border-radius: 25px; font-size: 16px;">
            <button type="submit" 
                    style="padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 25px; font-size: 16px; cursor: pointer;">
                Найти
            </button>
        </div>
    </form>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Search info
ob_start();
if (!empty($searchQuery)) {
    echo '<div style="padding: 20px; text-align: center;">';
    echo '<p style="color: #666;">Поисковый запрос: <strong>' . htmlspecialchars($searchQuery) . '</strong></p>';
    echo '</div>';
}
$greyContent4 = ob_get_clean();

// Section 5: Search Results
ob_start();
if (!empty($searchQuery)) {
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
            'urlField' => 'school_url',
            'urlPattern' => '/school/%s',
            'badge' => 'Школы'
        ],
        [
            'table' => 'spo',
            'fields' => ['spo_name', 'short_name', 'old_name', 'email'],
            'type' => 'college',
            'idField' => 'id_spo',
            'nameField' => 'spo_name',
            'urlField' => 'spo_url',
            'urlPattern' => '/spo/%s',
            'badge' => 'СПО'
        ],
        [
            'table' => 'vpo',
            'fields' => ['vpo_name', 'short_name', 'old_name', 'email'],
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
    
    // Perform searches
    $allResults = [];
    
    foreach ($searchConfigs as $config) {
        $conditions = [];
        $params = [];
        
        foreach ($config['fields'] as $field) {
            $conditions[] = "$field LIKE ?";
            $params[] = '%' . $searchQuery . '%';
        }
        
        $sql = "SELECT * FROM {$config['table']} WHERE " . implode(" OR ", $conditions) . " LIMIT 20";
        
        $stmt = $connection->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $row['_type'] = $config['type'];
                $row['_badge'] = $config['badge'];
                $row['_config'] = $config;
                $allResults[] = $row;
            }
        }
    }
    
    // Display results
    if (!empty($allResults)) {
        echo '<div style="padding: 20px;">';
        echo '<h3 style="margin-bottom: 20px;">Найдено результатов: ' . count($allResults) . '</h3>';
        
        // Group results by type
        $groupedResults = [];
        foreach ($allResults as $result) {
            $groupedResults[$result['_badge']][] = $result;
        }
        
        foreach ($groupedResults as $badge => $results) {
            echo '<div style="margin-bottom: 40px;">';
            echo '<h4 style="color: #28a745; margin-bottom: 15px;">' . htmlspecialchars($badge) . ' (' . count($results) . ')</h4>';
            echo '<div style="display: flex; flex-direction: column; gap: 15px;">';
            
            foreach ($results as $result) {
                $config = $result['_config'];
                $name = $result[$config['nameField']] ?? 'Без названия';
                $url = $result[$config['urlField']] ?? $result[$config['idField']];
                $link = sprintf($config['urlPattern'], $url);
                
                echo '<div style="padding: 15px; background: var(--surface, #f8f9fa); border-radius: 8px; border: 1px solid var(--border-color, #e2e8f0);">';
                echo '<a href="' . htmlspecialchars($link) . '" style="color: #333; text-decoration: none; font-weight: 500; font-size: 18px;">';
                echo htmlspecialchars($name);
                echo '</a>';
                
                // Show snippet if it's a post
                if ($result['_type'] === 'post' && isset($result['text_post'])) {
                    $snippet = mb_substr(strip_tags($result['text_post']), 0, 150) . '...';
                    echo '<p style="margin-top: 8px; color: #666; font-size: 14px;">' . htmlspecialchars($snippet) . '</p>';
                }
                
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div style="text-align: center; padding: 60px 20px;">';
        echo '<i class="fas fa-search fa-3x" style="color: #ddd; margin-bottom: 20px;"></i>';
        echo '<p style="color: #666; font-size: 18px;">По вашему запросу ничего не найдено</p>';
        echo '<p style="color: #999; margin-top: 10px;">Попробуйте изменить поисковый запрос</p>';
        echo '</div>';
    }
} else {
    echo '<div style="text-align: center; padding: 60px 20px;">';
    echo '<i class="fas fa-search fa-3x" style="color: #ddd; margin-bottom: 20px;"></i>';
    echo '<p style="color: #666; font-size: 18px;">Введите поисковый запрос</p>';
    echo '</div>';
}
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Поиск';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>