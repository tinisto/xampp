<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
// Database connection is already included in check_under_construction.php

// Check if this is a specific news article, filtered news, or news listing
$categoryUrls = ['novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya'];

if (isset($_GET['url_news']) && !empty($_GET['url_news']) && !in_array($_GET['url_news'], $categoryUrls)) {
    // INDIVIDUAL ARTICLE: Use direct HTML rendering (bypassing problematic template engine)
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-data-fetch.php';
    
    if (isset($newsData) && !empty($newsData)) {
        // Render individual article directly with proper header/footer
        ?>
        <!DOCTYPE html>
        <html lang="ru" data-bs-theme="light" data-theme="light">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($newsData['title_news']); ?> - 11-классники</title>
            
            <?php if (!empty($newsData['meta_d_news'])): ?>
                <meta name="description" content="<?php echo htmlspecialchars($newsData['meta_d_news']); ?>">
            <?php endif; ?>
            
            <?php if (!empty($newsData['meta_k_news'])): ?>
                <meta name="keywords" content="<?php echo htmlspecialchars($newsData['meta_k_news']); ?>">
            <?php endif; ?>
            
            <link href="/css/unified-styles.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            
            <script>
                (function() {
                    try {
                        const savedTheme = localStorage.getItem('theme') || 'light';
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
            <!-- Header -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
            
            <main class="container-fluid" style="padding-top: 100px; min-height: calc(100vh - 60px);">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- Breadcrumb -->
                            <nav aria-label="breadcrumb" class="mb-4">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                                    <li class="breadcrumb-item"><a href="/news/">Новости</a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($newsData['title_news']); ?></li>
                                </ol>
                            </nav>
                            
                            <!-- Article -->
                            <article>
                                <h1 class="mb-4"><?php echo htmlspecialchars($newsData['title_news']); ?></h1>
                                
                                <div class="meta mb-4" style="color: var(--text-secondary, #666); border-bottom: 1px solid var(--border-color, #e2e8f0); padding-bottom: 15px;">
                                    <small>
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Опубликовано: <?php echo date('d.m.Y H:i', strtotime($newsData['date_news'])); ?>
                                    </small>
                                    <small class="ms-3">
                                        <i class="fas fa-eye me-2"></i>
                                        Просмотров: <?php echo $newsData['view_news']; ?>
                                    </small>
                                </div>
                                
                                <?php if (!empty($newsData['description_news'])): ?>
                                <div class="description mb-4" style="font-size: 18px; color: var(--text-secondary, #555); line-height: 1.6; font-style: italic; padding: 20px; background: var(--surface-secondary, #f8f9fa); border-left: 4px solid var(--primary-color, #007bff); border-radius: 4px;">
                                    <?php echo nl2br(htmlspecialchars($newsData['description_news'])); ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="content" style="line-height: 1.8; font-size: 16px; color: var(--text-primary, #333);">
                                    <?php echo nl2br(htmlspecialchars($newsData['text_news'])); ?>
                                </div>
                                
                                <?php if (!empty($newsData['source_news'])): ?>
                                <div class="source mt-4" style="padding: 15px; background: var(--surface-secondary, #f8f9fa); border-left: 4px solid var(--success-color, #28a745); border-radius: 4px;">
                                    <strong><i class="fas fa-link me-2"></i>Источник:</strong> 
                                    <?php echo htmlspecialchars($newsData['source_news']); ?>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Back to news button -->
                                <div class="mt-5 pt-4" style="border-top: 1px solid var(--border-color, #e2e8f0);">
                                    <a href="/news/" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Назад к новостям
                                    </a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </main>
            
            <!-- Footer -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
            
            <script>
                function toggleTheme() {
                    const html = document.documentElement;
                    const currentTheme = html.getAttribute('data-theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    html.setAttribute('data-bs-theme', newTheme);
                    html.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    const themeIcon = document.getElementById('theme-icon');
                    const themeIconUser = document.getElementById('theme-icon-user');
                    const iconClass = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                    
                    if (themeIcon) themeIcon.className = iconClass;
                    if (themeIconUser) themeIconUser.className = iconClass;
                }
                
                window.toggleTheme = toggleTheme;
                
                document.addEventListener('DOMContentLoaded', function() {
                    const savedTheme = localStorage.getItem('theme') || 'light';
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
        <?php
    } else {
        // Article not found - show 404
        header("HTTP/1.0 404 Not Found");
        echo "<!DOCTYPE html><html><head><title>Новость не найдена</title></head><body><h1>Новость не найдена</h1><p><a href='/news/'>Вернуться к новостям</a></p></body></html>";
    }
    
    exit(); // CRITICAL: exit after rendering individual article
    
} else {
    // NEWS LISTING: Use the working hardcoded HTML (unchanged)
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
    
    $showBadges = empty($newsType);
    
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
    $newsData = null;
    $urlNews = '';
}

// LISTING HTML (only reached if not individual article)
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
    
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme') || 'light';
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
    <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main class="container-fluid" style="padding-top: 100px; min-height: calc(100vh - 60px);">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4"><?php echo htmlspecialchars($pageTitle); ?></h1>
                    
                    <?php if ($showBadges): ?>
                    <div class="mb-4">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="/news/" class="btn btn-outline-primary btn-sm">Все новости</a>
                            <a href="/news/novosti-vuzov" class="btn btn-outline-info btn-sm">Новости ВПО</a>
                            <a href="/news/novosti-spo" class="btn btn-outline-success btn-sm">Новости СПО</a>
                            <a href="/news/novosti-shkol" class="btn btn-outline-warning btn-sm">Новости школ</a>
                            <a href="/news/novosti-obrazovaniya" class="btn btn-outline-secondary btn-sm">Новости образования</a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <?php
                        $newsPerPage = 12;
                        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                        $offset = ($currentPage - 1) * $newsPerPage;
                        
                        $whereClause = "WHERE approved = 1";
                        if (!empty($categoryFilter)) {
                            $whereClause .= " " . $categoryFilter;
                        }
                        
                        $countQuery = "SELECT COUNT(*) as total FROM news $whereClause";
                        $countResult = mysqli_query($connection, $countQuery);
                        $totalNews = $countResult ? mysqli_fetch_assoc($countResult)['total'] : 0;
                        
                        $newsQuery = "SELECT id, title_news, description_news, url_slug, date_news, category_news 
                                      FROM news $whereClause 
                                      ORDER BY date_news DESC 
                                      LIMIT $newsPerPage OFFSET $offset";
                        $newsResult = mysqli_query($connection, $newsQuery);
                        
                        if ($newsResult && mysqli_num_rows($newsResult) > 0):
                            while ($news = mysqli_fetch_assoc($newsResult)):
                                $categoryBadge = '';
                                $categoryClass = 'secondary';
                                switch ($news['category_news']) {
                                    case 1: $categoryBadge = 'ВПО'; $categoryClass = 'info'; break;
                                    case 2: $categoryBadge = 'СПО'; $categoryClass = 'success'; break;
                                    case 3: $categoryBadge = 'Школы'; $categoryClass = 'warning'; break;
                                    case 4: $categoryBadge = 'Образование'; $categoryClass = 'secondary'; break;
                                }
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <?php if ($categoryBadge && $showBadges): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-<?php echo $categoryClass; ?>"><?php echo $categoryBadge; ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <h5 class="card-title">
                                        <a href="/news/<?php echo htmlspecialchars($news['url_slug']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($news['title_news']); ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text flex-grow-1">
                                        <?php 
                                        $description = strip_tags($news['description_news']);
                                        echo htmlspecialchars(strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description); 
                                        ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <small class="text-muted">
                                            <?php echo date('d.m.Y', strtotime($news['date_news'])); ?>
                                        </small>
                                        <div class="mt-2">
                                            <a href="/news/<?php echo htmlspecialchars($news['url_slug']); ?>" class="btn btn-primary btn-sm">Читать далее</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h4>Новости не найдены</h4>
                                <p>В данной категории пока нет опубликованных новостей.</p>
                                <p><a href="/news/" class="btn btn-primary">Все новости</a></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($totalNews > $newsPerPage): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <?php
                        $totalPages = ceil($totalNews / $newsPerPage);
                        $basePath = strtok($_SERVER['REQUEST_URI'], '?');
                        
                        if ($currentPage > 1):
                            $prevPage = $currentPage - 1;
                        ?>
                        <a href="<?php echo $basePath; ?>?page=<?php echo $prevPage; ?>" class="btn btn-outline-primary me-2">‹ Назад</a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                            if ($i == $currentPage):
                        ?>
                        <span class="btn btn-primary me-1"><?php echo $i; ?></span>
                        <?php else: ?>
                        <a href="<?php echo $basePath; ?>?page=<?php echo $i; ?>" class="btn btn-outline-primary me-1"><?php echo $i; ?></a>
                        <?php 
                            endif;
                        endfor; 
                        ?>
                        
                        <?php if ($currentPage < $totalPages): 
                            $nextPage = $currentPage + 1;
                        ?>
                        <a href="<?php echo $basePath; ?>?page=<?php echo $nextPage; ?>" class="btn btn-outline-primary ms-2">Далее ›</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Показано <?php echo $offset + 1; ?>-<?php echo min($offset + $newsPerPage, $totalNews); ?> 
                            из <?php echo $totalNews; ?> новостей
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            const themeIcon = document.getElementById('theme-icon');
            const themeIconUser = document.getElementById('theme-icon-user');
            const iconClass = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            
            if (themeIcon) themeIcon.className = iconClass;
            if (themeIconUser) themeIconUser.className = iconClass;
        }
        
        window.toggleTheme = toggleTheme;
        
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
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