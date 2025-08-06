<?php
// Search process content (to be included in template)

// Get search query - ensure it's available
$searchQuery = $_GET['query'] ?? '';
$searchQuery = trim($searchQuery);

// Debug output
echo "<!-- DEBUG: searchQuery = '" . htmlspecialchars($searchQuery) . "' -->\n";

$error = null;

// Basic validation
if (empty($searchQuery)) {
    header('Location: /search');
    exit();
}

$searchQuery = trim($searchQuery);

// Validate search query
if (!preg_match("/^[\p{L}0-9\s]+$/u", $searchQuery)) {
    $error = "Недопустимый поисковый запрос.";
} elseif (mb_strlen($searchQuery) < 2) {
    $error = "Поисковый запрос слишком короткий. Введите минимум 2 символа.";
} elseif (mb_strlen($searchQuery) > 100) {
    $error = "Поисковый запрос слишком длинный. Максимум 100 символов.";
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h1><i class="fas fa-search"></i> Результаты поиска</h1>
                    <p>По запросу: <strong>"<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>"</strong></p>
                    
                    <!-- Search bar from homepage -->
                    <div class="mt-3">
                        <?php 
                        $searchInstanceId = 'search_' . time();
                        ?>
                        <div style="max-width: 600px; margin: 0 auto;">
                            <form action="/search-process" method="get">
                                <div style="position: relative;">
                                    <input 
                                        type="text" 
                                        name="query" 
                                        placeholder="Попробуйте другой запрос..."
                                        value=""
                                        style="width: 100%; padding: 16px 50px 16px 24px; border: 1px solid var(--border-color, #ddd); border-radius: 50px; font-size: 16px; outline: none; background: var(--surface, white); color: var(--text-primary, #333); transition: border-color 0.3s ease;"
                                        id="search-input-<?= $searchInstanceId ?>"
                                        oninput="document.getElementById('clear-<?= $searchInstanceId ?>').style.display = this.value.length > 0 ? 'flex' : 'none'"
                                        onfocus="this.style.borderColor = 'var(--primary-color, #28a745)'"
                                        onblur="this.style.borderColor = 'var(--border-color, #ddd)'"
                                    >
                                    <button 
                                        type="button" 
                                        id="clear-<?= $searchInstanceId ?>"
                                        style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; background: transparent; color: var(--text-secondary, #666); border: none; cursor: pointer; font-size: 18px; font-weight: normal; display: none; align-items: center; justify-content: center; z-index: 1000; transition: all 0.2s ease;"
                                        onmouseover="this.style.color = 'var(--text-primary, #333)'; this.style.background = 'var(--surface-variant, #f8f9fa)';"
                                        onmouseout="this.style.color = 'var(--text-secondary, #666)'; this.style.background = 'transparent';"
                                        onclick="document.getElementById('search-input-<?= $searchInstanceId ?>').value = ''; this.style.display = 'none'; document.getElementById('search-input-<?= $searchInstanceId ?>').focus();"
                                        title="Очистить поиск"
                                    >✕</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php else: ?>
                <?php
                $results = [];
                $searchTerm = '%' . $searchQuery . '%';
                
                try {
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
                    
                    // Search posts - use url_slug (fixed for 404 compatibility)
                    $stmt = $connection->prepare("SELECT COALESCE(url_slug, url_post) as post_url, title_post FROM posts WHERE (title_post LIKE ? OR text_post LIKE ?) AND status = 1 LIMIT 10");
                    if ($stmt) {
                        $stmt->bind_param("ss", $searchTerm, $searchTerm);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $results[] = [
                                'name' => $row['title_post'],
                                'url' => '/post/' . $row['post_url'],
                                'type' => 'Статья'
                            ];
                        }
                        $stmt->close();
                    }
                    
                    // Search news
                    $stmt = $connection->prepare("SELECT url_slug, title_news FROM news WHERE (title_news LIKE ? OR text_news LIKE ?) AND approved = 1 LIMIT 10");
                    if ($stmt) {
                        $stmt->bind_param("ss", $searchTerm, $searchTerm);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $results[] = [
                                'name' => $row['title_news'],
                                'url' => '/news/' . $row['url_slug'],
                                'type' => 'Новость'
                            ];
                        }
                        $stmt->close();
                    }
                    
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Ошибка поиска. Попробуйте позже.</div>';
                }
                
                if (empty($results)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-search"></i> 
                        По запросу <strong>"<?= htmlspecialchars($searchQuery) ?>"</strong> ничего не найдено.
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <small class="text-muted">Найдено результатов: <?= count($results) ?></small>
                    </div>
                    
                    <?php foreach ($results as $item): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?= htmlspecialchars($item['url']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </a>
                                    <span class="badge bg-primary ms-2"><?= $item['type'] ?></span>
                                </h5>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
            <?php endif; ?>
        </div>
    </div>
</div>