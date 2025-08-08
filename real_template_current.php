<?php
// Load environment and database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Template content variables - can be set by including pages
$pageTitle = $pageTitle ?? 'Template';
$greyContent1 = $greyContent1 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Title/Header</p><p><a href="/real_components.php" style="color: #007bff; text-decoration: none;">→ View Components Page</a></p></div>';
$greyContent2 = $greyContent2 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Navigation/Categories</p></div>';
$greyContent3 = $greyContent3 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Metadata (Author, Date, Views)</p></div>';
$greyContent4 = $greyContent4 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Filters/Sorting</p></div>';
$greyContent5 = $greyContent5 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Main Content (Posts/Schools/Tests)</p></div>';
$greyContent6 = $greyContent6 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Pagination</p></div>';
$blueContent = $blueContent ?? '<div style="text-align: center; padding: 20px; margin: 0; color: white;"><p>Comments Section (for Posts, Schools, VPO, SPO)</p></div>';

// Check if this is /test/news and set content accordingly
$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/test/news') !== false) {
    $pageTitle = 'Новости образования';
    
    // Section 1: Page Title
    $greyContent1 = '<div style="padding: 30px 20px; margin: 0;">
        <h1 style="text-align: center; margin: 0; font-size: 32px; color: #333; font-weight: 600;">Новости образования</h1>
        <p style="text-align: center; margin: 10px 0 0 0; color: #666; font-size: 16px;">Актуальные новости о ВУЗах, колледжах и школах</p>
    </div>';
    
    // Section 2: Category Navigation
    ob_start();
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
    
    $newsNavItems = [
        ['title' => 'Все новости', 'url' => '/news'],
        ['title' => 'Новости ВПО', 'url' => '/news/novosti-vuzov'],
        ['title' => 'Новости СПО', 'url' => '/news/novosti-spo'],
        ['title' => 'Новости школ', 'url' => '/news/novosti-shkol'],
        ['title' => 'Новости образования', 'url' => '/news/novosti-obrazovaniya']
    ];
    
    renderCategoryNavigation($newsNavItems, $_SERVER['REQUEST_URI']);
    $greyContent2 = ob_get_clean();
    
    // Section 3: Metadata - Show placeholder for news listing
    $greyContent3 = '<div style="text-align: center; padding: 20px; margin: 0;"><p style="color: #999; font-size: 14px;">Metadata section (hidden on news listing)</p></div>';
    
    // Section 4: Filters and Sorting
    ob_start();
    echo '<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px;">';
    
    // Filters dropdown
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/filters-dropdown.php';
    renderFiltersDropdown([
        'sortOptions' => [
            'date_desc' => 'По дате (новые)',
            'date_asc' => 'По дате (старые)',
            'popular' => 'По популярности'
        ]
    ]);
    
    // Search inline
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
    renderSearchInline([
        'placeholder' => 'Поиск новостей...',
        'buttonText' => 'Найти'
    ]);
    
    echo '</div>';
    $greyContent4 = ob_get_clean();
    
    // Section 5: Content/Cards (News Grid)  
    ob_start();
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-grid.php';
    
    // Sample news data (16 items for 4x4 grid)
    $sampleNews = [
        [
            'id_news' => 1,
            'title_news' => 'Новости образования: важные изменения',
            'url_news' => 'sample-news-1',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s'),
            'category_title' => 'Образование',
            'category_url' => 'education'
        ],
        [
            'id_news' => 2,
            'title_news' => 'ЕГЭ 2024: что нового?',
            'url_news' => 'sample-news-2',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'category_title' => 'ЕГЭ',
            'category_url' => 'ege'
        ],
        [
            'id_news' => 3,
            'title_news' => 'Поступление в ВУЗы',
            'url_news' => 'sample-news-3',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'category_title' => 'ВПО',
            'category_url' => 'vpo'
        ],
        [
            'id_news' => 4,
            'title_news' => 'СПО: новые специальности',
            'url_news' => 'sample-news-4',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'category_title' => 'СПО',
            'category_url' => 'spo'
        ],
        [
            'id_news' => 5,
            'title_news' => 'ЕГЭ 2024: изменения в математике',
            'url_news' => 'sample-news-5',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
            'category_title' => 'ЕГЭ',
            'category_url' => 'ege'
        ],
        [
            'id_news' => 6,
            'title_news' => 'Дни открытых дверей в университетах',
            'url_news' => 'sample-news-6',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            'category_title' => 'ВПО',
            'category_url' => 'vpo'
        ],
        [
            'id_news' => 7,
            'title_news' => 'Цифровизация образования: новые технологии',
            'url_news' => 'sample-news-7',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
            'category_title' => 'Технологии',
            'category_url' => 'tech'
        ],
        [
            'id_news' => 8,
            'title_news' => 'Стипендии и гранты для студентов',
            'url_news' => 'sample-news-8',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
            'category_title' => 'Стипендии',
            'category_url' => 'grants'
        ],
        [
            'id_news' => 9,
            'title_news' => 'Профориентация: выбор специальности',
            'url_news' => 'sample-news-9',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
            'category_title' => 'Карьера',
            'category_url' => 'career'
        ],
        [
            'id_news' => 10,
            'title_news' => 'Дистанционное обучение: плюсы и минусы',
            'url_news' => 'sample-news-10',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-9 days')),
            'category_title' => 'Онлайн',
            'category_url' => 'online'
        ],
        [
            'id_news' => 11,
            'title_news' => 'Международные программы обмена',
            'url_news' => 'sample-news-11',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
            'category_title' => 'Международное',
            'category_url' => 'international'
        ],
        [
            'id_news' => 12,
            'title_news' => 'Летние школы и курсы',
            'url_news' => 'sample-news-12',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-11 days')),
            'category_title' => 'Курсы',
            'category_url' => 'courses'
        ],
        [
            'id_news' => 13,
            'title_news' => 'Научные конференции для студентов',
            'url_news' => 'sample-news-13',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            'category_title' => 'Наука',
            'category_url' => 'science'
        ],
        [
            'id_news' => 14,
            'title_news' => 'IT-специальности: тренды рынка',
            'url_news' => 'sample-news-14',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-13 days')),
            'category_title' => 'IT',
            'category_url' => 'it'
        ],
        [
            'id_news' => 15,
            'title_news' => 'Психология студенческой жизни',
            'url_news' => 'sample-news-15',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-14 days')),
            'category_title' => 'Психология',
            'category_url' => 'psychology'
        ],
        [
            'id_news' => 16,
            'title_news' => 'Творческие конкурсы для учащихся',
            'url_news' => 'sample-news-16',
            'image_news' => '/images/default-news.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
            'category_title' => 'Творчество',
            'category_url' => 'creative'
        ]
    ];
    
    renderNewsGrid($sampleNews, [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
    $greyContent5 = ob_get_clean();
    
    // Section 6: Pagination
    ob_start();
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern(1, 5, '/test/news');
    $greyContent6 = ob_get_clean();
    
    // Blue section: Show placeholder for news listing
    $blueContent = '<div style="text-align: center; padding: 20px; margin: 0; color: white;"><p style="opacity: 0.7; font-size: 14px;">Comments section (not used on news listing)</p></div>';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Страница'); ?> - 11-классники</title>
    
    <!-- Adsense Meta tag -->
    <meta name="google-adsense-account" content="ca-pub-2363662533799826">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- AdSense code snippet -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2363662533799826" crossorigin="anonymous"></script>
    
    <!-- Clarity -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "pmqwtsrnfg");
    </script>
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
        
        /* Header - flex-shrink: 0 so it keeps its size */
        .main-header {
            flex-shrink: 0;
            margin: 0; /* No margins */
            padding: 0; /* No padding */
            background: white; /* Ensure header has white background */
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
            
            .comments-section {
                padding: 25px 15px;
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
        
        /* Content - flex: 1 so it expands to fill space */
        .content {
            flex: 1 1 auto;
            background: transparent;
            padding: 0;
            margin: 0 8px;
            box-sizing: border-box;
            min-height: 0;
        }
        
        /* Comments section */
        .comments-section {
            background: blue;
            color: white;
            padding: 15px 8px;
            margin: 0 8px;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        
        /* Container - no padding, just for visualization */
        .content .container,
        .comments-section .container {
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
            
            
            .comments-section {
                padding: 40px;
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
            
            .comments-section {
                padding: 12px 5px;
                margin: 0 10px;
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
            
            .comments-section {
                padding: 50px;
                margin: 0 60px;
            }
        }
        
        /* Footer - flex-shrink: 0 so it keeps its size */
        .main-footer {
            flex-shrink: 0;
            margin: 0; /* No margins */
            padding: 0; /* No padding */
            background: #f8f9fa; /* Ensure footer has its light background */
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
            <!-- First section -->
            <div style="flex: 1; margin: 0;">
                <?php echo $greyContent1; ?>
            </div>
            
            <!-- Second section -->
            <div style="flex: 1; margin: 0;">
                <?php echo $greyContent2; ?>
            </div>
            
            <!-- Third section -->
            <div style="flex: 1; margin: 0;">
                <?php echo $greyContent3; ?>
            </div>
            
            <!-- Fourth section -->
            <div style="flex: 1; margin: 0;">
                <?php echo $greyContent4; ?>
            </div>
            
            <!-- Fifth section -->
            <div style="flex: 1; margin: 0;">
                <?php echo $greyContent5; ?>
            </div>
            
            <!-- Sixth section -->
            <div style="flex: 1; margin: 0;">
                <?php echo $greyContent6; ?>
            </div>
        </main>
        
        <!-- Comments Section (BLUE background) -->
        <div class="comments-section" style="background: blue;">
            <?php 
            echo $blueContent;
            ?>
        </div>
    </div>
    
    <!-- Website Footer -->
    <footer class="main-footer">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_footer.php'; ?>
    </footer>
    
    <!-- Cookie Consent -->
    <?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/components/cookie-consent.php';
    echo renderCookieConsent();
    ?>
</body>
</html>