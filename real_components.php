<?php
// Load environment and database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Template content variables - can be set by including pages
$pageTitle = $pageTitle ?? 'Template';
$greyContent1 = $greyContent1 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Title/Header</p></div>';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Components - 11-классники</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: #212529; /* Dark background for overscroll areas */
            position: relative;
        }
        
        /* Wrapper for yellow background sections */
        .yellow-bg-wrapper {
            background: white;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Dark mode for wrapper */
        [data-theme="dark"] .yellow-bg-wrapper,
        [data-bs-theme="dark"] .yellow-bg-wrapper {
            background: #1a202c;
        }
        
        /* Header - flex-shrink: 0 so it keeps its size + sticky */
        .main-header {
            flex-shrink: 0;
            margin: 0; /* No margins */
            padding: 0; /* No padding */
            background: white; /* Ensure header has white background */
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #ddd; /* Add border to match footer */
        }
        
        /* Dark mode for header */
        [data-theme="dark"] .main-header,
        [data-bs-theme="dark"] .main-header {
            background: #2d3748;
            border-bottom: 1px solid #4a5568;
        }
        
        /* Sections - flex: 1 so they expand equally */
        .section {
            flex: 1;
            margin: 0 8px;
            box-sizing: border-box;
        }
        
        /* Tablet styles */
        @media (min-width: 481px) and (max-width: 768px) {
            .content {
                margin: 0 20px;
            }
            
            .content > div {
                margin-left: 10px;
                margin-right: 10px;
            }
            
            .section {
                margin: 0 20px;
            }
        }
        
        /* Desktop - larger margins */
        @media (min-width: 769px) {
            .main-header {
                margin: 0;
            }
            
            .section {
                margin: 0 40px;
            }
        }
        
        /* Content - flex: 1 so it expands to fill space but not beyond viewport */
        .content {
            flex: 1 1 auto;
            background: transparent;
            padding: 0;
            margin: 0 8px;
            box-sizing: border-box;
            min-height: 0;
            max-height: none;
        }
        
        /* Container - no padding, just for visualization */
        .content .container {
            max-width: none;
            margin: 0;
            padding: 0; /* No padding on container - it's on the parent */
            width: 100%;
        }
        
        /* Desktop - no padding on red div */
        @media (min-width: 769px) {
            .content {
                padding: 0;
                margin: 0 40px;
            }
        }
        
        /* Extra small mobile devices */
        @media (max-width: 480px) {
            .content {
                margin: 0 10px;
            }
            
            .content > div {
                margin-left: 5px;
                margin-right: 5px;
            }
            
            .section {
                margin: 0 5px;
            }
            
            .yellow-bg-wrapper {
                padding: 0;
            }
        }
        
        /* Large desktop */
        @media (min-width: 1200px) {
            .content {
                margin: 0 60px;
            }
            
            .section {
                margin: 0 60px;
            }
        }
        
        /* Footer - flex-shrink: 0 so it keeps its size */
        .main-footer {
            flex-shrink: 0;
            margin: 0; /* No margins */
            background: #f8f9fa; /* Ensure footer has its light background */
            border-top: 1px solid #ddd; /* Add border to make it visible */
            min-height: 60px; /* Ensure minimum height */
        }
        
        /* Dark mode for footer */
        [data-theme="dark"] .main-footer,
        [data-bs-theme="dark"] .main-footer {
            background: #2d3748;
            border-top: 1px solid #4a5568;
        }
        
        /* Force dropdown to always have white background with dark text */
        .filters-dropdown select,
        [data-theme="dark"] .filters-dropdown select,
        [data-bs-theme="dark"] .filters-dropdown select,
        body[data-theme="dark"] .filters-dropdown select,
        body[data-bs-theme="dark"] .filters-dropdown select {
            background-color: white !important;
            background: white !important;
            color: #212529 !important;
            border-color: #ddd !important;
        }
        
        .filters-dropdown select option,
        [data-theme="dark"] .filters-dropdown select option,
        [data-bs-theme="dark"] .filters-dropdown select option {
            background-color: white !important;
            background: white !important;
            color: #212529 !important;
        }
        
        /* Ensure nothing clips dropdowns */
        body,
        .main-header,
        .header,
        .header-container,
        .header-actions,
        .yellow-bg-wrapper,
        .content {
            overflow: visible !important;
        }
        
        /* Force Bootstrap dropdowns to be visible */
        .dropdown-menu {
            z-index: 9999999 !important;
        }
        
        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Override any Bootstrap or other CSS that might hide dropdowns */
        .btn-group .dropdown-menu.show,
        .dropdown .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            position: absolute !important;
            transform: translate(0, 0) !important;
        }
        
        /* Fix user avatar dropdown visibility */
        .user-menu {
            position: relative !important;
        }
        
        .user-menu .dropdown-menu {
            display: none;
            position: absolute !important;
            right: 0;
            top: 100%;
            margin-top: 5px;
            min-width: 200px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 99999 !important;
        }
        
        .user-menu.dropdown.show .dropdown-menu {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }
        
        /* Dark mode dropdown */
        [data-theme="dark"] .user-menu .dropdown-menu,
        [data-bs-theme="dark"] .user-menu .dropdown-menu {
            background: #2d3748;
            border-color: #4a5568;
            color: #e4e6eb;
        }
        
        /* Dropdown items */
        .user-menu .dropdown-item {
            display: block;
            padding: 10px 16px;
            color: #333;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .user-menu .dropdown-item:hover {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        [data-theme="dark"] .user-menu .dropdown-item {
            color: #e4e6eb;
        }
        
        [data-theme="dark"] .user-menu .dropdown-item:hover {
            background: rgba(74, 222, 128, 0.1);
            color: #4ade80;
        }
        
        /* Mobile layout fixes */
        @media (max-width: 768px) {
            /* Hide logo on mobile */
            .header-brand,
            .header-brand-icon {
                display: none !important;
            }
            
            /* Show and position hamburger menu on left */
            .mobile-menu-toggle {
                display: flex !important;
                order: -1;
                margin-right: auto;
                margin-left: 0;
                background: transparent;
                border: none;
                font-size: 24px;
                color: var(--text-color, #333);
                cursor: pointer;
                padding: 8px;
                align-items: center;
                justify-content: center;
            }
            
            /* Dark mode hamburger color */
            [data-theme="dark"] .mobile-menu-toggle,
            [data-bs-theme="dark"] .mobile-menu-toggle {
                color: #e4e6eb;
            }
            
            /* Hide user avatar on mobile */
            .user-menu,
            .user-avatar {
                display: none !important;
            }
            
            /* Ensure theme toggle is on right */
            .theme-toggle-btn {
                margin-left: auto;
                order: 2;
            }
            
            /* Fix header actions layout */
            .header-actions {
                display: flex !important;
                width: 100%;
                justify-content: space-between;
                align-items: center;
            }
            
            /* Fix header container */
            .header-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
            }
            
            /* Hide desktop navigation on mobile */
            .header-nav {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Website Header -->
    <header class="main-header">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php'; ?>
    </header>
    
    <!-- Yellow background wrapper for middle sections -->
    <div class="yellow-bg-wrapper">
        <!-- Main Content (RED background) -->
        <main class="content" style="background: transparent; display: flex; flex-direction: column;">
            <!-- Only section - Title/Header -->
            <div style="flex: 1; margin: 0;">
                <?php 
                // Include Real Title component
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
                renderRealTitle('Components Page');
                
                // Search Inline component
                echo '<div style="text-align: center; margin: 20px 0;">';
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
                renderSearchInline([
                    'placeholder' => 'Поиск...',
                    'buttonText' => 'Найти',
                    'paramName' => 'search',
                    'width' => '300px'
                ]);
                echo '</div>';
                
                // Include Category Navigation component
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
                
                $navItems = [
                    ['title' => 'All Components', 'url' => '/real_components.php'],
                    ['title' => 'Navigation', 'url' => '#navigation'],
                    ['title' => 'Cards', 'url' => '#cards'],
                    ['title' => 'Forms', 'url' => '#forms'],
                    ['title' => 'Media', 'url' => '#media']
                ];
                
                renderCategoryNavigation($navItems, $_SERVER['REQUEST_URI']);
                
                // Filters Dropdown component (moved here)
                echo '<div style="text-align: center; margin: 20px 0;">';
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/filters-dropdown.php';
                renderFiltersDropdown([
                    'sortOptions' => [
                        'date_desc' => 'По дате (новые)',
                        'date_asc' => 'По дате (старые)',
                        'popular' => 'По популярности'
                    ]
                ]);
                echo '</div>';
                
                // Universal Cards Grid Container component
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
                
                // Fetch real news from database
                $sampleNewsGrid = [];
                try {
                    if ($hasDatabase && $connection) {
                        $query = "SELECT id_news, title_news, url_news, image_news, created_at, 
                                         categories.title_category as category_title, 
                                         categories.url_category as category_url 
                                  FROM news 
                                  LEFT JOIN categories ON news.category_id = categories.id_category 
                                  WHERE news.status = 'published' 
                                  ORDER BY created_at DESC 
                                  LIMIT 4";
                        $result = mysqli_query($connection, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $sampleNewsGrid[] = $row;
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Fallback to sample data if database fails
                }
                
                // Fallback to sample data if no real data
                if (empty($sampleNewsGrid)) {
                    $sampleNewsGrid = [
                        [
                            'id_news' => 1,
                            'title_news' => 'ЕГЭ 2024: Новые изменения в экзаменах',
                            'url_news' => 'ege-2024-changes',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s'),
                            'category_title' => 'ЕГЭ',
                            'category_url' => 'ege'
                        ],
                        [
                            'id_news' => 2,
                            'title_news' => 'Поступление в ВУЗы: советы абитуриентам',
                            'url_news' => 'university-admission-tips',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                            'category_title' => 'ВПО',
                            'category_url' => 'vpo'
                        ],
                        [
                            'id_news' => 3,
                            'title_news' => 'СПО: новые специальности в 2024 году',
                            'url_news' => 'spo-new-specialties-2024',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                            'category_title' => 'СПО',
                            'category_url' => 'spo'
                        ],
                        [
                            'id_news' => 4,
                            'title_news' => 'Профориентация: как выбрать профессию',
                            'url_news' => 'career-guidance-choosing-profession',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                            'category_title' => 'Профориентация',
                            'category_url' => 'career'
                        ]
                    ];
                }
                
                echo '<div style="margin: 20px 0;">';
                renderRealTitle('News Cards Grid', ['fontSize' => '24px', 'margin' => '0 0 15px 0']);
                renderCardsGrid($sampleNewsGrid, 'news', [
                    'columns' => 4,
                    'gap' => 20,
                    'showBadge' => true
                ]);
                echo '</div>';
                
                // Fetch real posts from database
                $samplePostsGrid = [];
                try {
                    if ($hasDatabase && $connection) {
                        $query = "SELECT id_post, title_post as title_news, url_post as url_news, 
                                         image_post as image_news, created_at,
                                         categories.title_category as category_title, 
                                         categories.url_category as category_url 
                                  FROM posts 
                                  LEFT JOIN categories ON posts.category_id = categories.id_category 
                                  WHERE posts.status = 'published' 
                                  ORDER BY created_at DESC 
                                  LIMIT 4";
                        $result = mysqli_query($connection, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $samplePostsGrid[] = $row;
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Fallback to sample data if database fails
                }
                
                // Fallback to sample data if no real data
                if (empty($samplePostsGrid)) {
                    $samplePostsGrid = [
                        [
                            'id_news' => 5,
                            'title_news' => 'Как подготовиться к ЕГЭ по математике',
                            'url_news' => 'how-to-prepare-ege-math',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s'),
                            'category_title' => 'Подготовка',
                            'category_url' => 'preparation'
                        ],
                        [
                            'id_news' => 6,
                            'title_news' => 'Лучшие ВУЗы России: рейтинг 2024',
                            'url_news' => 'best-universities-russia-2024',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                            'category_title' => 'Рейтинги',
                            'category_url' => 'rankings'
                        ],
                        [
                            'id_news' => 7,
                            'title_news' => 'Стипендии и гранты для студентов',
                            'url_news' => 'scholarships-grants-students',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                            'category_title' => 'Финансы',
                            'category_url' => 'finance'
                        ],
                        [
                            'id_news' => 8,
                            'title_news' => 'Дистанционное образование: плюсы и минусы',
                            'url_news' => 'distance-learning-pros-cons',
                            'image_news' => '/images/default-news.jpg',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                            'category_title' => 'Образование',
                            'category_url' => 'education'
                        ]
                    ];
                }
                
                echo '<div style="margin: 20px 0;">';
                renderRealTitle('Posts Cards Grid', ['fontSize' => '24px', 'margin' => '0 0 15px 0']);
                renderCardsGrid($samplePostsGrid, 'post', [
                    'columns' => 4,
                    'gap' => 20,
                    'showBadge' => true
                ]);
                echo '</div>';
                
                // Fetch real tests from database
                $sampleTestsGrid = [];
                try {
                    if ($hasDatabase && $connection) {
                        $query = "SELECT id_test, title_test, url_test, image_test, 
                                         difficulty, duration, questions_count,
                                         categories.title_category as category_title, 
                                         categories.url_category as category_url 
                                  FROM tests 
                                  LEFT JOIN categories ON tests.category_id = categories.id_category 
                                  WHERE tests.status = 'active' 
                                  ORDER BY created_at DESC 
                                  LIMIT 4";
                        $result = mysqli_query($connection, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $sampleTestsGrid[] = $row;
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Fallback to sample data if database fails
                }
                
                // Fallback to sample data if no real data
                if (empty($sampleTestsGrid)) {
                    $sampleTestsGrid = [
                        [
                            'id_test' => 1,
                            'title_test' => 'Тест по математике ЕГЭ',
                            'url_test' => 'ege-math-test',
                            'image_test' => '/images/default-test.jpg',
                            'difficulty' => 'Сложный',
                            'duration' => 30,
                            'questions_count' => 20,
                            'category_title' => 'Математика',
                            'category_url' => 'math'
                        ],
                        [
                            'id_test' => 2,
                            'title_test' => 'Тест по русскому языку',
                            'url_test' => 'russian-language-test',
                            'image_test' => '/images/default-test.jpg',
                            'difficulty' => 'Средний',
                            'duration' => 45,
                            'questions_count' => 25,
                            'category_title' => 'Русский язык',
                            'category_url' => 'russian'
                        ],
                        [
                            'id_test' => 3,
                            'title_test' => 'Тест по физике',
                            'url_test' => 'physics-test',
                            'image_test' => '/images/default-test.jpg',
                            'difficulty' => 'Сложный',
                            'duration' => 40,
                            'questions_count' => 15,
                            'category_title' => 'Физика',
                            'category_url' => 'physics'
                        ],
                        [
                            'id_test' => 4,
                            'title_test' => 'Тест по биологии',
                            'url_test' => 'biology-test',
                            'image_test' => '/images/default-test.jpg',
                            'difficulty' => 'Легкий',
                            'duration' => 35,
                            'questions_count' => 18,
                            'category_title' => 'Биология',
                            'category_url' => 'biology'
                        ]
                    ];
                }
                
                echo '<div style="margin: 20px 0;">';
                renderRealTitle('Tests Cards Grid', ['fontSize' => '24px', 'margin' => '0 0 15px 0']);
                renderCardsGrid($sampleTestsGrid, 'test', [
                    'columns' => 4,
                    'gap' => 20,
                    'showBadge' => true
                ]);
                echo '</div>';
                
                
                // Pagination component
                echo '<div style="margin: 20px 0;">';
                renderRealTitle('Pagination', ['fontSize' => '24px', 'margin' => '0 0 15px 0']);
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
                renderPaginationModern(2, 8, '/real_components.php');
                echo '</div>';
                
                // Add link to real_template
                echo '<div style="text-align: center; padding: 20px; margin: 0;"><a href="/real_template.php" style="color: #007bff; text-decoration: none;">→ View Full Template</a></div>';
                
                // Test Bootstrap dropdown
                ?>
                <div style="text-align: center; padding: 20px;">
                    <h3>Test Bootstrap Dropdown</h3>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Test Dropdown Button
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </div>
                </div>
                <?php
                ?>
            </div>
        </main>
    </div>
    
    <!-- Website Footer -->
    <footer class="main-footer">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_footer.php'; ?>
    </footer>
    
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Bootstrap 5 handles dropdowns automatically
    console.log('Bootstrap dropdowns enabled');
    console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
    
    // Debug dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        console.log('Found dropdowns with data-bs-toggle:', dropdowns.length);
        
        dropdowns.forEach((dropdown, index) => {
            console.log(`Dropdown ${index}:`, dropdown);
            
            // Try to manually init if Bootstrap is loaded
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                new bootstrap.Dropdown(dropdown);
                console.log(`Initialized dropdown ${index}`);
            }
        });
        
        // Check if dropdowns work with manual click
        document.querySelector('.user-avatar')?.addEventListener('click', function(e) {
            console.log('User avatar clicked - manual test');
            const menu = this.parentElement.querySelector('.dropdown-menu');
            console.log('Found menu:', menu);
            if (menu) {
                const isShown = menu.classList.contains('show');
                console.log('Menu currently shown:', isShown);
                console.log('Menu current styles:', {
                    display: window.getComputedStyle(menu).display,
                    visibility: window.getComputedStyle(menu).visibility,
                    opacity: window.getComputedStyle(menu).opacity,
                    position: window.getComputedStyle(menu).position,
                    zIndex: window.getComputedStyle(menu).zIndex
                });
            }
        });
    });
    </script>
</body>
</html>