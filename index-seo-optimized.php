<?php
/**
 * SEO-Optimized Homepage
 * Example of implementing comprehensive SEO for the main page
 */

// Enable performance optimizations
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/performance.php';
enable_compression();

// Load environment and database
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// SEO Configuration for Homepage
$seoConfig = [
    'title' => '11klassniki.ru - Образовательный портал для школьников и абитуриентов',
    'description' => 'Образовательный портал 11-классники: новости образования, информация о школах, вузах и колледжах России, онлайн-тесты по ЕГЭ и ОГЭ, помощь в выборе профессии и поступлении.',
    'keywords' => '11 классников, образование, школы России, вузы, колледжи, ЕГЭ, ОГЭ, тесты онлайн, поступление, абитуриент, новости образования',
    'canonical' => 'https://11klassniki.ru/',
    'image' => 'https://11klassniki.ru/images/og-homepage.jpg',
    'og_type' => 'website',
    'robots' => 'index, follow',
    
    // Homepage structured data
    'structured_data_type' => 'WebSite',
    'structured_data' => [
        'name' => '11klassniki.ru',
        'url' => 'https://11klassniki.ru',
        'description' => 'Образовательный портал для школьников и абитуриентов России',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => 'https://11klassniki.ru/search?q={search_term_string}',
            'query-input' => 'required name=search_term_string'
        ]
    ],
    
    // Critical CSS for above-the-fold content
    'critical_css' => '
        .hero-section { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 4rem 0; 
            text-align: center; 
        }
        .hero-title { 
            font-size: 3rem; 
            font-weight: bold; 
            margin-bottom: 1rem; 
        }
        .hero-subtitle { 
            font-size: 1.2rem; 
            opacity: 0.9; 
            margin-bottom: 2rem; 
        }
        .stats-section { 
            padding: 3rem 0; 
            background: #f8f9fa; 
        }
        .stat-item { 
            text-align: center; 
            padding: 1rem; 
        }
        .stat-number { 
            font-size: 2.5rem; 
            font-weight: bold; 
            color: #007bff; 
        }
        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .stat-number { font-size: 2rem; }
        }
    '
];

$mainContent = 'index-seo-content.php';
$pageTitle = 'Главная';

// Template configuration for modern homepage
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'no-bootstrap',
    'darkMode' => true,
    'seo' => $seoConfig
];

// Include SEO-optimized template
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/seo-head.php';
?>

<body>
    <!-- Main content -->
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <h1 class="hero-title">Добро пожаловать на 11klassniki.ru</h1>
                <p class="hero-subtitle">
                    Твой путеводитель в мире образования: школы, вузы, тесты и новости
                </p>
                <div class="hero-actions">
                    <a href="/tests" class="btn btn-light btn-lg me-3">Пройти тест</a>
                    <a href="/news" class="btn btn-outline-light btn-lg">Новости</a>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">1000+</div>
                            <div class="stat-label">Школ в базе</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Вузов России</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">300+</div>
                            <div class="stat-label">Колледжей</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Тестов онлайн</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Links Section -->
        <section class="quick-links py-5">
            <div class="container">
                <h2 class="text-center mb-5">Быстрый доступ</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">🏫 Школы России</h5>
                                <p class="card-text">Найти школу в своем регионе, узнать контакты и особенности обучения</p>
                                <a href="/schools-all-regions" class="btn btn-primary">Найти школу</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">🎓 Вузы и колледжи</h5>
                                <p class="card-text">Выбрать высшее или среднее профессиональное образование</p>
                                <a href="/vpo-all-regions" class="btn btn-primary">Выбрать вуз</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">📝 Тесты и подготовка</h5>
                                <p class="card-text">Онлайн-тесты по всем предметам для подготовки к ЕГЭ и ОГЭ</p>
                                <a href="/tests" class="btn btn-primary">Пройти тест</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php
    // Include footer
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php')) {
        include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php';
    }
    ?>

    <!-- Performance optimization scripts -->
    <script>
        // Lazy load images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
        
        // Preload critical resources
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = '/news';
        document.head.appendChild(link);
    </script>
</body>
</html>