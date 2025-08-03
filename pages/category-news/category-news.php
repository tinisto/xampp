<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get category information
if (isset($_GET['category_en'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['category_en']);
    
    // Fetch category data - try different column names
    $queryCategoryNews = "SELECT * FROM news_categories WHERE url_category_news = ? OR category_en = ?";
    $stmt = mysqli_prepare($connection, $queryCategoryNews);
    mysqli_stmt_bind_param($stmt, 'ss', $urlCategory, $urlCategory);
    mysqli_stmt_execute($stmt);
    $resultCategoryNews = mysqli_stmt_get_result($stmt);
    
    if ($resultCategoryNews && mysqli_num_rows($resultCategoryNews) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategoryNews);
        $pageTitle = $categoryData['title_category_news'] ?? $categoryData['title_category'] ?? 'Новости категории';
        $pageDescription = "Новости категории: " . $pageTitle;
        $metaD = $pageDescription;
        $metaK = $pageTitle . ', новости, образование';
        $categoryId = $categoryData['id_category_news'] ?? $categoryData['id_category'] ?? 1;
    } else {
        // Category not found, show default news page
        $pageTitle = 'Новости образования';
        $pageDescription = 'Актуальные новости образования';
        $metaD = $pageDescription;
        $metaK = 'новости, образование, школы, вузы';
        $categoryId = null;
    }
} else {
    header("Location: /404");
    exit();
}

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
    </style>
</head>
<body>
    <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <!-- Main Content -->
    <main style="flex: 1;">
        <div class="container">
            <!-- News Type Navigation -->
            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px;">
                <?php
                $inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; transition: all 0.3s ease; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0);";
                ?>
                
                <a href="/news" style="<?= $inactiveStyle ?>">Все новости</a>
                <a href="/news/novosti-vuzov" style="<?= $inactiveStyle ?>">Новости ВПО</a>
                <a href="/news/novosti-spo" style="<?= $inactiveStyle ?>">Новости СПО</a>
                <a href="/news/novosti-shkol" style="<?= $inactiveStyle ?>">Новости школ</a>
                <a href="/news/novosti-obrazovaniya" style="<?= $inactiveStyle ?>">Новости образования</a>
            </div>
            
            <h1 style="color: var(--text-primary, #333); margin-bottom: 30px;"><?= htmlspecialchars($pageTitle) ?></h1>
            
            <?php
            // Pagination setup
            $newsPerPage = 12; // 3 rows x 4 columns
            $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $offset = ($currentPage - 1) * $newsPerPage;
            
            // Count total news for pagination
            if ($categoryId) {
                $countQuery = "SELECT COUNT(*) as total FROM news WHERE category_news = ? AND approved = 1";
                $countStmt = mysqli_prepare($connection, $countQuery);
                mysqli_stmt_bind_param($countStmt, 'i', $categoryId);
                mysqli_stmt_execute($countStmt);
                $countResult = mysqli_stmt_get_result($countStmt);
                $totalNews = mysqli_fetch_assoc($countResult)['total'];
                mysqli_stmt_close($countStmt);
            } else {
                $countQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
                $countResult = mysqli_query($connection, $countQuery);
                $totalNews = mysqli_fetch_assoc($countResult)['total'];
            }
            $totalPages = ceil($totalNews / $newsPerPage);
            
            // Fetch news for this category with pagination
            if ($categoryId) {
                $query = "SELECT * FROM news WHERE category_news = ? AND approved = 1 ORDER BY date_news DESC LIMIT $newsPerPage OFFSET $offset";
                $stmt = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($stmt, 'i', $categoryId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            } else {
                // Show all news if category not found
                $query = "SELECT * FROM news WHERE approved = 1 ORDER BY date_news DESC LIMIT $newsPerPage OFFSET $offset";
                $result = mysqli_query($connection, $query);
            }
            
            if ($result && mysqli_num_rows($result) > 0) {
                echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">';
                while ($row = mysqli_fetch_assoc($result)) {
                    $date = new DateTime($row['date_news']);
                    
                    // Get category info for badge
                    $categoryQuery = "SELECT title_category FROM categories WHERE id_category = ?";
                    $categoryStmt = mysqli_prepare($connection, $categoryQuery);
                    mysqli_stmt_bind_param($categoryStmt, 'i', $row['category_news']);
                    mysqli_stmt_execute($categoryStmt);
                    $categoryResult = mysqli_stmt_get_result($categoryStmt);
                    $categoryData = mysqli_fetch_assoc($categoryResult);
                    $categoryName = $categoryData['title_category'] ?? 'Новости';
                    mysqli_stmt_close($categoryStmt);
                    
                    // Generate category color
                    $categoryColors = [
                        'Мир увлечений' => '#e74c3c',
                        'Разговор' => '#3498db', 
                        'Школы' => '#2ecc71',
                        'ВПО' => '#9b59b6',
                        'СПО' => '#f39c12',
                        'ЕГЭ' => '#1abc9c',
                        'default' => '#95a5a6'
                    ];
                    $badgeColor = $categoryColors[$categoryName] ?? $categoryColors['default'];
                    ?>
                    <div style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 20px; background: var(--surface, #ffffff); display: flex; flex-direction: column; position: relative;">
                        <!-- Category Badge -->
                        <div style="position: absolute; top: 15px; right: 15px; background: <?= $badgeColor ?>; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                            <?= htmlspecialchars($categoryName) ?>
                        </div>
                        
                        <h2 style="margin: 0 0 10px 0; font-size: 18px; line-height: 1.3; padding-right: 80px;">
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
                if (isset($stmt)) mysqli_stmt_close($stmt);
                
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
                echo '<p style="color: var(--text-secondary, #666);">Новости в данной категории не найдены.</p>';
            }
            ?>
        </div>
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
