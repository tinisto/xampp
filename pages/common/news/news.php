<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
// Database connection is already included in check_under_construction.php

// Debug GET parameters
if (isset($_GET['debug'])) {
    echo "<h2>News.php Debug</h2>";
    echo "<h3>GET parameters:</h3><pre>";
    print_r($_GET);
    echo "</pre>";
}

// Check if this is a specific news article, filtered news, or news listing
$categoryUrls = ['novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya'];
if (isset($_GET['url_news']) && !empty($_GET['url_news']) && !in_array($_GET['url_news'], $categoryUrls)) {
    // Include the news data fetch logic for specific article
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-data-fetch.php';
} else {
    // This is a news listing page
    $newsType = $_GET['news_type'] ?? '';
    
    // Map category URLs to news types
    if (isset($_GET['url_news'])) {
        switch ($_GET['url_news']) {
            case 'novosti-vuzov':
                $newsType = 'vpo';
                break;
            case 'novosti-spo':
                $newsType = 'spo';
                break;
            case 'novosti-shkol':
                $newsType = 'school';
                break;
            case 'novosti-obrazovaniya':
                $newsType = 'education';
                break;
        }
    }
    
    $showBadges = empty($newsType); // Show badges only on main news page
    
    // Set page title and meta based on news type
    switch ($newsType) {
        case 'vpo':
            $pageTitle = 'Новости ВПО';
            $metaD = 'Новости высшего профессионального образования, университетов и институтов России.';
            $categoryFilter = "AND category_news = 1";
            break;
        case 'spo':
            $pageTitle = 'Новости СПО';
            $metaD = 'Новости среднего профессионального образования, колледжей и техникумов России.';
            $categoryFilter = "AND category_news = 2";
            break;
        case 'school':
            $pageTitle = 'Новости школ';
            $metaD = 'Новости общего образования, школ и учебных заведений России.';
            $categoryFilter = "AND category_news = 3";
            break;
        case 'education':
            $pageTitle = 'Новости образования';
            $metaD = 'Новости системы образования, ЕГЭ, ОГЭ и образовательной политики России.';
            $categoryFilter = "AND category_news = 4";
            break;
        default:
            $pageTitle = 'Новости образования';
            $metaD = 'Актуальные новости образования, поступления в вузы, ЕГЭ и ОГЭ. Последние события в мире образования России.';
            $categoryFilter = '';
    }
    
    $metaK = 'новости образования, вузы, ЕГЭ, ОГЭ, поступление, школы, университеты';
    $newsData = null; // No specific news data for listing
    $urlNews = '';
}

// Debug after data fetch
if (isset($_GET['debug'])) {
    echo "<h3>After data fetch:</h3>";
    echo "newsData: " . (isset($newsData) ? "SET" : "NOT SET") . "<br>";
    echo "pageTitle: " . (isset($pageTitle) ? htmlspecialchars($pageTitle) : "NOT SET") . "<br>";
    echo "metaD: " . (isset($metaD) ? "SET" : "NOT SET") . "<br>";
    echo "urlNews: " . (isset($urlNews) ? htmlspecialchars($urlNews) : "NOT SET") . "<br>";
    if (isset($newsData)) {
        echo "<h4>News Data Keys:</h4><pre>";
        print_r(array_keys($newsData));
        echo "</pre>";
    }
    
    // Add database debug info here
    echo "<h3>Database Debug:</h3>";
    if (isset($connection)) {
        echo "✅ Database connection exists<br>";
        echo "Connection status: " . ($connection->ping() ? "Active" : "Failed") . "<br>";
        
        // Test news count
        $testCountQuery = "SELECT COUNT(*) as total FROM news";
        $testCountResult = mysqli_query($connection, $testCountQuery);
        if ($testCountResult) {
            $totalAllNews = mysqli_fetch_assoc($testCountResult)['total'];
            echo "Total news in database: <strong>$totalAllNews</strong><br>";
        }
        
        $approvedCountQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
        $approvedCountResult = mysqli_query($connection, $approvedCountQuery);
        if ($approvedCountResult) {
            $approvedNews = mysqli_fetch_assoc($approvedCountResult)['total'];
            echo "Approved news: <strong>$approvedNews</strong><br>";
        }
        
        // Show environment
        echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'Not set') . "<br>";
        echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "<br>";
        echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "<br>";
    } else {
        echo "❌ Database connection not found<br>";
    }
    exit();
}

// Ensure variables are set
$metaD = $metaD ?? '';
$metaK = $metaK ?? '';
$newsData = $newsData ?? null;
$urlNews = $urlNews ?? '';
$pageTitle = $pageTitle ?? 'News';

// Additional debug - Check if content file exists
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-content.php')) {
    die('News content file not found: /pages/common/news/news-content.php');
}

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',  // Use unified CSS framework
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK,
    'newsData' => $newsData,
    'urlNews' => $urlNews,
];

// Test if we can render content directly
if (isset($_GET['direct_test'])) {
    echo "<!DOCTYPE html><html><head><title>Direct Test</title></head><body>";
    echo "<h1>Direct News Test</h1>";
    echo "<h2>" . htmlspecialchars($newsData['title_news']) . "</h2>";
    echo "<p>" . htmlspecialchars($newsData['description_news']) . "</p>";
    echo "<div>" . nl2br(htmlspecialchars($newsData['text_news'])) . "</div>";
    echo "</body></html>";
    exit();
}

// Temporary fix: Use simplified template rendering instead of template engine
// This bypasses the template engine issue until we can debug it further
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    
    <?php if (!empty($metaD)): ?>
        <meta name="description" content="<?php echo htmlspecialchars($metaD); ?>">
    <?php endif; ?>
    
    <?php if (!empty($metaK)): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($metaK); ?>">
    <?php endif; ?>
    
    <!-- Unified Styles for improved design and dark mode -->
    <link href="/css/unified-styles.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Immediate theme application -->
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
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: var(--background);
            color: var(--text-primary);
        }
        
        h1 {
            color: var(--text-primary);
            margin-bottom: 20px;
        }
        
        .lead {
            color: var(--text-secondary);
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        
        .content {
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 16px;
        }
        
        /* Alert styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
        
        .alert-warning {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border-color: #faebcc;
        }
        
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        
        [data-theme="dark"] .alert-info {
            color: #9dd5f3;
            background-color: #1e3a5f;
            border-color: #2c5282;
        }
        
        [data-theme="dark"] .alert-warning {
            color: #fbd38d;
            background-color: #744210;
            border-color: #975a16;
        }
        
        [data-theme="dark"] .alert-danger {
            color: #feb2b2;
            background-color: #742a2a;
            border-color: #9c2a2a;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <!-- Main Content -->
    <main style="flex: 1;">
        <?php
        if (isset($_GET['url_news']) && !empty($_GET['url_news'])) {
            // Show specific news article
            include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-content.php';
        } else {
            // Show news listing
            ?>
            <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; background: var(--background, #ffffff); color: var(--text-primary, #333);">
                <?php if (empty($newsType)): ?>
                <h1 style="color: var(--text-primary, #333); margin-bottom: 20px;"><?= htmlspecialchars($pageTitle) ?></h1>
                <?php endif; ?>
                
                <!-- News Type Navigation -->
                <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px;">
                    <?php
                    $currentNewsType = $_GET['news_type'] ?? '';
                    $activeStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; background: var(--primary-color, #28a745); color: white; border: 2px solid var(--primary-color, #28a745);";
                    $inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; transition: all 0.3s ease; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0);";
                    ?>
                    
                    <a href="/news" style="<?= empty($currentNewsType) ? $activeStyle : $inactiveStyle ?>">Все новости</a>
                    <a href="/news/novosti-vuzov" style="<?= $currentNewsType === 'vpo' ? $activeStyle : $inactiveStyle ?>">Новости ВПО</a>
                    <a href="/news/novosti-spo" style="<?= $currentNewsType === 'spo' ? $activeStyle : $inactiveStyle ?>">Новости СПО</a>
                    <a href="/news/novosti-shkol" style="<?= $currentNewsType === 'school' ? $activeStyle : $inactiveStyle ?>">Новости школ</a>
                    <a href="/news/novosti-obrazovaniya" style="<?= $currentNewsType === 'education' ? $activeStyle : $inactiveStyle ?>">Новости образования</a>
                </div>
                
                <?php
                // Pagination setup
                $newsPerPage = 12; // 3 rows x 4 columns
                $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                $offset = ($currentPage - 1) * $newsPerPage;
                
                // Count total news for pagination - with fallback logic
                $countQuery = "SELECT COUNT(*) as total FROM news n WHERE n.approved = 1";
                if (isset($categoryFilter) && !empty($categoryFilter)) {
                    $countQuery .= " " . $categoryFilter;
                }
                $countResult = mysqli_query($connection, $countQuery);
                
                if (!$countResult) {
                    // Database query failed - show error
                    echo '<div class="alert alert-danger">Database query error: ' . mysqli_error($connection) . '<br>Query: ' . htmlspecialchars($countQuery) . '</div>';
                    $totalNews = 0;
                } else {
                    $totalNews = mysqli_fetch_assoc($countResult)['total'];
                }
                
                // If no approved news found, try to count all news
                if ($totalNews == 0) {
                    $fallbackCountQuery = "SELECT COUNT(*) as total FROM news n";
                    $fallbackCountResult = mysqli_query($connection, $fallbackCountQuery);
                    if ($fallbackCountResult) {
                        $totalAllNews = mysqli_fetch_assoc($fallbackCountResult)['total'];
                        if ($totalAllNews > 0) {
                            echo '<div class="alert alert-warning">Found ' . $totalAllNews . ' total news, but 0 approved news. Contact admin to approve news articles.</div>';
                        }
                    }
                }
                
                $totalPages = ceil($totalNews / $newsPerPage);
                
                // Fetch latest news with optional filtering and pagination
                $baseQuery = "SELECT n.* 
                             FROM news n 
                             WHERE n.approved = 1";
                if (isset($categoryFilter) && !empty($categoryFilter)) {
                    $query = $baseQuery . " " . $categoryFilter . " ORDER BY n.date_news DESC LIMIT $newsPerPage OFFSET $offset";
                } else {
                    $query = $baseQuery . " ORDER BY n.date_news DESC LIMIT $newsPerPage OFFSET $offset";
                }
                
                // Add debug info if requested
                if (isset($_GET['debug'])) {
                    echo '<div class="alert alert-info">Debug Info:<br>';
                    echo 'Query: ' . htmlspecialchars($query) . '<br>';
                    echo 'Total approved news: ' . $totalNews . '<br>';
                    echo 'Connection status: ' . ($connection->ping() ? 'Active' : 'Failed') . '<br>';
                    echo '</div>';
                }
                
                $result = mysqli_query($connection, $query);
                
                if (!$result) {
                    echo '<div class="alert alert-danger">News query failed: ' . mysqli_error($connection) . '<br>Query: ' . htmlspecialchars($query) . '</div>';
                }
                
                if ($result && mysqli_num_rows($result) > 0) {
                    echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">';
                    while ($row = mysqli_fetch_assoc($result)) {
                        $date = new DateTime($row['date_news']);
                        
                        $categoryName = '';
                        $badgeColor = '';
                        
                        // Get news category info for badge
                        if ($showBadges) {
                            // Set category name and color based on category ID
                            switch ($row['category_news']) {
                                case '1':
                                    $categoryName = 'Новости ВПО';
                                    $badgeColor = '#9b59b6'; // Purple for ВПО
                                    break;
                                case '2':
                                    $categoryName = 'Новости СПО';
                                    $badgeColor = '#f39c12'; // Orange for СПО
                                    break;
                                case '3':
                                    $categoryName = 'Новости школ';
                                    $badgeColor = '#2ecc71'; // Green for Школы
                                    break;
                                case '4':
                                    $categoryName = 'Новости образования';
                                    $badgeColor = '#3498db'; // Blue for Образование
                                    break;
                                default:
                                    $categoryName = 'Общие новости';
                                    $badgeColor = '#95a5a6'; // Gray for others
                            }
                        }
                        ?>
                        <div style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 20px; background: var(--surface, #ffffff); display: flex; flex-direction: column;">
                            <?php if ($showBadges): ?>
                            <!-- Category Badge -->
                            <div style="margin-bottom: 10px;">
                                <span style="background: <?= $badgeColor ?>; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 500; display: inline-block;">
                                    <?= htmlspecialchars($categoryName) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <h2 style="margin: 0 0 10px 0; font-size: 18px; line-height: 1.3;">
                                <a href="/news/<?= htmlspecialchars($row['url_news']) ?>" style="color: var(--text-primary, #333); text-decoration: none;">
                                    <?= htmlspecialchars($row['title_news']) ?>
                                </a>
                            </h2>
                            <div style="color: var(--text-secondary, #666); font-size: 12px; margin-bottom: 15px;">
                                <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>
                                <?= $date->format('d.m.Y') ?>
                                <span style="margin-left: 10px;">
                                    <i class="fas fa-eye" style="margin-right: 5px;"></i>
                                    <?= number_format((int)$row['view_news']) ?>
                                </span>
                            </div>
                            <p style="color: var(--text-secondary, #666); margin: 0; font-size: 14px; line-height: 1.4; flex-grow: 1;">
                                <?= htmlspecialchars(mb_substr($row['description_news'], 0, 120)) ?><?= mb_strlen($row['description_news']) > 120 ? '...' : '' ?>
                            </p>
                        </div>
                        <?php
                    }
                    echo '</div>';
                    mysqli_free_result($result);
                    
                    // Show pagination if there are multiple pages
                    if ($totalPages > 1) {
                        echo '<div style="margin-top: 40px; display: flex; justify-content: center; align-items: center; gap: 10px;">';
                        
                        // Build current URL for pagination links
                        $currentUrl = $_SERVER['REQUEST_URI'];
                        $urlParts = parse_url($currentUrl);
                        $basePath = $urlParts['path'];
                        
                        // Previous page
                        if ($currentPage > 1) {
                            $prevPage = $currentPage - 1;
                            echo '<a href="' . $basePath . '?page=' . $prevPage . '" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none; background: var(--surface, #ffffff);">‹ Назад</a>';
                        }
                        
                        // Page numbers
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        if ($startPage > 1) {
                            echo '<a href="' . $basePath . '?page=1" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none; background: var(--surface, #ffffff);">1</a>';
                            if ($startPage > 2) {
                                echo '<span style="padding: 8px 4px; color: var(--text-secondary, #666);">...</span>';
                            }
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            if ($i == $currentPage) {
                                echo '<span style="padding: 8px 12px; border: 2px solid var(--primary-color, #28a745); border-radius: 4px; color: white; background: var(--primary-color, #28a745); font-weight: 500;">' . $i . '</span>';
                            } else {
                                echo '<a href="' . $basePath . '?page=' . $i . '" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none; background: var(--surface, #ffffff);">' . $i . '</a>';
                            }
                        }
                        
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<span style="padding: 8px 4px; color: var(--text-secondary, #666);">...</span>';
                            }
                            echo '<a href="' . $basePath . '?page=' . $totalPages . '" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none; background: var(--surface, #ffffff);">' . $totalPages . '</a>';
                        }
                        
                        // Next page
                        if ($currentPage < $totalPages) {
                            $nextPage = $currentPage + 1;
                            echo '<a href="' . $basePath . '?page=' . $nextPage . '" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none; background: var(--surface, #ffffff);">Далее ›</a>';
                        }
                        
                        echo '</div>';
                        
                        // Show pagination info
                        $startItem = $offset + 1;
                        $endItem = min($offset + $newsPerPage, $totalNews);
                        echo '<div style="margin-top: 20px; text-align: center; color: var(--text-secondary, #666); font-size: 14px;">';
                        echo 'Показано ' . $startItem . '-' . $endItem . ' из ' . $totalNews . ' новостей';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert alert-info">';
                    echo '<h4>Новости не найдены</h4>';
                    echo '<p>Возможные причины:</p>';
                    echo '<ul>';
                    echo '<li>В базе данных нет одобренных новостей</li>';
                    echo '<li>Проблема подключения к базе данных</li>';
                    echo '<li>Новости требуют одобрения администратором</li>';
                    echo '</ul>';
                    echo '<p><a href="/news?debug=1">Показать отладочную информацию</a></p>';
                    echo '</div>';
                }
                ?>
            </div>
            <?php
        }
        ?>
    </main>
    
    <!-- Footer -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <!-- Dark Mode Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('preferred-theme', newTheme);
            
            // Update theme icons
            const themeIcon = document.getElementById('theme-icon');
            const themeIconUser = document.getElementById('theme-icon-user');
            const iconClass = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            
            if (themeIcon) themeIcon.className = iconClass;
            if (themeIconUser) themeIconUser.className = iconClass;
        }
        
        window.toggleTheme = toggleTheme;
        
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            const html = document.documentElement;
            
            html.setAttribute('data-bs-theme', savedTheme);
            html.setAttribute('data-theme', savedTheme);
            
            const themeIcon = document.getElementById('theme-icon');
            const themeIconUser = document.getElementById('theme-icon-user');
            const iconClass = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            
            if (themeIcon) themeIcon.className = iconClass;
            if (themeIconUser) themeIconUser.className = iconClass;
        });
    </script>
</body>
</html>
