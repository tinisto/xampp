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

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php';
?>

<style>
    body {
        background-color: #f8f9fa;
    }
    .search-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .search-header {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .search-title {
        font-size: 28px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }
    .search-query {
        font-size: 18px;
        color: #666;
    }
    .search-query strong {
        color: #28a745;
    }
    .result-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }
    .result-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .result-title {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 5px;
    }
    .result-title a {
        color: #333;
        text-decoration: none;
    }
    .result-title a:hover {
        color: #28a745;
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
        background: white;
        padding: 60px 30px;
        border-radius: 12px;
        text-align: center;
        color: #666;
    }
    .search-actions {
        margin-top: 30px;
        text-align: center;
    }
    .btn-custom {
        display: inline-block;
        padding: 12px 30px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        margin: 0 10px;
    }
    .btn-custom:hover {
        background-color: #218838;
        transform: translateY(-1px);
        color: white;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
    }
</style>

<div class="search-container">
    <div class="search-header">
        <h1 class="search-title">Результаты поиска</h1>
        <p class="search-query">Поисковый запрос: <strong><?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    </div>

    <?php
    $results = [];
    $searchTerm = '%' . $searchQuery . '%';
    
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
    
    <div class="search-actions">
        <a href="/search" class="btn-custom">Новый поиск</a>
        <a href="/" class="btn-custom btn-secondary">На главную</a>
    </div>
</div>

<?php
$connection->close();
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
?>