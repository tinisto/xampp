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

// Database connection with error handling
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
    
    if (!isset($connection)) {
        // Fallback database connection
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $connection->set_charset("utf8mb4");
        
        if ($connection->connect_error) {
            throw new Exception("Database connection failed");
        }
    }
} catch (Exception $e) {
    die("Ошибка подключения к базе данных. Попробуйте позже.");
}

// Simple header
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск: <?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
            --text-primary: #333;
            --text-secondary: #666;
            --background: #ffffff;
            --surface: #ffffff;
            --border-color: #e2e8f0;
        }
        
        [data-theme="dark"] {
            --primary-color: #68d391;
            --text-primary: #f7fafc;
            --text-secondary: #cbd5e0;
            --background: #1a202c;
            --surface: #1e293b;
            --border-color: #4a5568;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            min-height: 100vh;
        }
        
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px;
        }
        
        .search-header { 
            background: var(--surface); 
            padding: 30px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }
        
        .result-item { 
            background: var(--surface); 
            padding: 20px; 
            margin-bottom: 15px; 
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .result-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        
        .result-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 18px;
        }
        
        .result-item a:hover {
            text-decoration: underline;
        }
        
        .badge { 
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            margin-right: 10px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: var(--surface);
            color: var(--text-primary);
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }
        
        [data-theme="dark"] .alert-info {
            background: #1e3a5f;
            color: #9dd5f3;
            border-color: #2c5282;
        }
    </style>
    
    <!-- Theme script -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('preferred-theme') || 'light';
                document.documentElement.setAttribute('data-bs-theme', savedTheme);
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch(e) {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
</head>
<body>
    <div class="container">
        <div class="search-header">
            <h1><i class="fas fa-search"></i> Результаты поиска</h1>
            <p>По запросу: <strong>"<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>"</strong></p>
        </div>

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
            echo '<div class="alert alert-info">Ошибка поиска. Попробуйте позже.</div>';
        }
        
        if (empty($results)) {
            echo '<div class="alert alert-info">';
            echo '<i class="fas fa-info-circle"></i> ';
            echo 'К сожалению, по вашему запросу ничего не найдено. Попробуйте изменить поисковую фразу.';
            echo '</div>';
        } else {
            echo '<div style="margin-bottom: 20px;">';
            echo '<small style="color: var(--text-secondary);">Найдено результатов: ' . count($results) . '</small>';
            echo '</div>';
            
            foreach ($results as $item) {
                echo '<div class="result-item">';
                echo '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['name']) . '</a>';
                echo '<span class="badge">' . $item['type'] . '</span>';
                echo '</div>';
            }
        }
        ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="/search" class="btn btn-primary"><i class="fas fa-search"></i> Новый поиск</a>
            <a href="/" class="btn btn-secondary"><i class="fas fa-home"></i> На главную</a>
        </div>
    </div>
</body>
</html>
<?php
if (isset($connection)) {
    $connection->close();
}
?>