<?php
session_start();

// Check if a search query is set and not empty
if (!isset($_GET['query']) || empty($_GET['query'])) {
    header('Location: /search');
    exit();
}

$searchQuery = trim($_GET['query']);

// Basic validation - allow Cyrillic, Latin, numbers, and spaces
if (!preg_match("/^[\p{L}0-9\s]+$/u", $searchQuery)) {
    die("Недопустимый поисковый запрос.");
}

// Check length
if (mb_strlen($searchQuery) < 2) {
    die("Поисковый запрос слишком короткий. Введите минимум 2 символа.");
}

if (mb_strlen($searchQuery) > 100) {
    die("Поисковый запрос слишком длинный. Максимум 100 символов.");
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

if ($connection->connect_error) {
    die("Ошибка подключения к базе данных");
}

// Simple header
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск - 11-классники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 30px; }
        .search-box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .result-item { background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .badge { margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-box">
            <h1>Результаты поиска</h1>
            <p>Поисковый запрос: <strong><?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        </div>

        <?php
        $results = [];
        $searchTerm = '%' . $searchQuery . '%';
        
        // Search schools
        $stmt = $connection->prepare("SELECT id_school, school_name FROM schools WHERE school_name LIKE ? OR short_name LIKE ? LIMIT 10");
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
        
        if (empty($results)) {
            echo '<div class="alert alert-info">Нет результатов по вашему запросу.</div>';
        } else {
            foreach ($results as $item) {
                echo '<div class="result-item">';
                echo '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['name']) . '</a>';
                echo '<span class="badge bg-secondary">' . $item['type'] . '</span>';
                echo '</div>';
            }
        }
        ?>
        
        <div class="mt-4">
            <a href="/search" class="btn btn-primary">Новый поиск</a>
            <a href="/" class="btn btn-secondary">На главную</a>
        </div>
    </div>
</body>
</html>
<?php
$connection->close();
?>