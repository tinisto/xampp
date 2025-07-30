<?php 
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Security.php';

include 'search-form.php'; 
?>
<div class="container mt-4">
  <?php if (!empty($additionalData['searchQuery'])): ?>
    <p class="custom-info-message">
      Поисковый запрос: <span class="fw-semibold"><?= Security::cleanOutput($additionalData['searchQuery']) ?></span>
    </p>

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
    
    // Display results with pagination
    echo '<ul class="list-group">';
    
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
        
        echo "<li class='list-group-item'>";
        echo "<a class='link-custom' href='$url'><small>$name</small></a>";
        echo "<span class='badge text-bg-warning ms-2'>$badge</span>";
        echo "</li>";
    }
    
    echo '</ul>';
    
    // Pagination logic
    $totalPages = ceil($totalResults / $itemsPerPage);
    if ($totalPages > 1) {
        include 'search-pagination.php';
    }
    
    if ($totalResults === 0) {
        echo '<p class="custom-alert">Нет результатов.</p>';
    }
    ?>
  <?php else: ?>
    <?php include 'search-show-categories-white-links.php'; ?>
    <?php include 'search-show-categories-black-links.php'; ?>
  <?php endif; ?>
</div>