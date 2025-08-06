<?php
// Search process content (to be included in template)

// Get search query
$searchQuery = $_GET['query'] ?? '';
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
                </div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
                <div class="text-center">
                    <a href="/search" class="btn btn-primary"><i class="fas fa-search"></i> Новый поиск</a>
                    <a href="/" class="btn btn-secondary"><i class="fas fa-home"></i> На главную</a>
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
                        <i class="fas fa-info-circle"></i> 
                        К сожалению, по вашему запросу ничего не найдено. Попробуйте изменить поисковую фразу.
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
                
                <div class="text-center mt-4">
                    <a href="/search" class="btn btn-primary"><i class="fas fa-search"></i> Новый поиск</a>
                    <a href="/" class="btn btn-secondary"><i class="fas fa-home"></i> На главную</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>