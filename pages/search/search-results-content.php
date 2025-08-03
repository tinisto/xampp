<style>
    /* Professional search results styling with dark mode support */
    :root {
        --bg-primary: #ffffff;
        --bg-secondary: #f8f9fa;
        --text-primary: #333333;
        --text-secondary: #666666;
        --primary-color: #28a745;
        --border-color: #e9ecef;
    }
    
    [data-theme="dark"] {
        --bg-primary: #1f2937;
        --bg-secondary: #374151;
        --text-primary: #f9fafb;
        --text-secondary: #9ca3af;
        --primary-color: #10b981;
        --border-color: #4b5563;
    }
    .search-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 0 20px;
    }
    .search-header {
        background: transparent;
        padding: 20px 0;
        margin-bottom: 30px;
    }
    .search-title {
        font-size: 2rem;
        font-weight: 600;
        color: var(--text-primary, #333);
        margin-bottom: 15px;
    }
    .search-query {
        font-size: 1.1rem;
        color: var(--text-secondary, #666);
        margin-bottom: 0;
    }
    .search-query strong {
        color: var(--primary-color, #28a745);
        font-weight: 600;
    }
    .result-card {
        background: transparent;
        padding: 16px 0;
        margin-bottom: 12px;
        border-bottom: 1px solid var(--border-color, #e9ecef);
        transition: all 0.2s ease;
    }
    .result-card:hover {
        background: var(--bg-secondary, #f8f9fa);
        padding-left: 8px;
        padding-right: 8px;
        border-radius: 4px;
    }
    
    [data-theme="dark"] .result-card:hover {
        background: var(--bg-secondary, #374151);
    }
    .result-title {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 5px;
    }
    .result-title a {
        color: var(--text-primary, #333);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .result-title a:hover {
        color: var(--primary-color, #28a745);
        text-decoration: underline;
    }
    .result-type {
        display: inline-block;
        background-color: #28a745;
        color: white;
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 12px;
        margin-top: 8px;
    }
    .no-results {
        padding: 40px 20px;
        text-align: center;
        color: var(--text-secondary, #666);
    }
    .no-results h3 {
        color: var(--text-primary, #333);
        margin-bottom: 16px;
        font-size: 1.5rem;
        font-weight: 500;
    }
    .search-form-wrapper {
        margin-top: 20px;
    }
</style>

<div class="search-container">
    <div class="search-header">
        <h1 class="search-title">Результаты поиска</h1>
        <p class="search-query">Поисковый запрос: <strong><?php echo htmlspecialchars($additionalData['searchQuery'] ?? $searchQuery ?? $_GET['query'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></p>
        
        <!-- Professional search form using main page component -->
        <div class="search-form-wrapper" style="margin-top: 20px;">
            <?php
            include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-box.php';
            
            $currentQuery = $additionalData['searchQuery'] ?? $searchQuery ?? $_GET['query'] ?? '';
            
            renderSearchBox([
                'id' => 'searchResults',
                'placeholder' => 'Введите поисковый запрос...',
                'action' => '/search-process',
                'method' => 'GET',
                'inputName' => 'query',
                'size' => 'medium',
                'showButton' => false,
                'autofocus' => false,
                'value' => $currentQuery
            ]);
            ?>
        </div>
    </div>

    <?php
    $results = [];
    // Get search query from various sources
    $currentSearchQuery = $additionalData['searchQuery'] ?? $searchQuery ?? $_GET['query'] ?? '';
    $searchTerm = '%' . $currentSearchQuery . '%';
    
    // Search schools
    $stmt = $connection->prepare("SELECT id_school, school_name FROM schools WHERE school_name LIKE ? OR short_name LIKE ? LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = [
                'name' => $row['school_name'],
                'url' => '/school/' . $row['id_school'],
                'type' => 'Школа'
            ];
        }
        $stmt->close();
    }
    
    // Search posts  
    $stmt = $connection->prepare("SELECT url_post, title_post FROM posts WHERE (title_post LIKE ? OR text_post LIKE ?) AND status = 1 LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = [
                'name' => $row['title_post'],
                'url' => '/post/' . $row['url_post'],
                'type' => 'Статья'
            ];
        }
        $stmt->close();
    }
    
    // Search VPO
    $stmt = $connection->prepare("SELECT vpo_url, vpo_name FROM vpo WHERE vpo_name LIKE ? OR short_name LIKE ? LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = [
                'name' => $row['vpo_name'],
                'url' => '/vpo/' . $row['vpo_url'],
                'type' => 'ВУЗ'
            ];
        }
        $stmt->close();
    }
    
    // Search SPO
    $stmt = $connection->prepare("SELECT spo_url, spo_name FROM spo WHERE spo_name LIKE ? OR short_name LIKE ? LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = [
                'name' => $row['spo_name'],
                'url' => '/spo/' . $row['spo_url'],
                'type' => 'ССУЗ'
            ];
        }
        $stmt->close();
    }
    
    if (empty($results)) {
        echo '<div class="no-results">';
        echo '<h3>Ничего не найдено</h3>';
        echo '<p>По вашему запросу не найдено результатов. Попробуйте изменить поисковый запрос.</p>';
        echo '</div>';
    } else {
        foreach ($results as $item) {
            echo '<div class="result-card">';
            echo '<h3 class="result-title"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['name']) . '</a></h3>';
            echo '<span class="result-type">' . $item['type'] . '</span>';
            echo '</div>';
        }
    }
    ?>
    
    <!-- Removed search actions as requested -->
</div>