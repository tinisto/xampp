<?php
/**
 * SEO Optimization System
 * Advanced SEO features for better search engine visibility
 */

require_once __DIR__ . '/database/db_modern.php';

class SEOOptimizer {
    
    /**
     * Generate structured data (JSON-LD) for content
     */
    public static function generateStructuredData($type, $data) {
        $baseUrl = 'https://11klassniki.ru';
        
        switch ($type) {
            case 'news':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'NewsArticle',
                    'headline' => $data['title_news'],
                    'description' => substr(strip_tags($data['text_news']), 0, 160),
                    'url' => $baseUrl . '/news/' . $data['url_news'],
                    'datePublished' => $data['created_at'],
                    'dateModified' => $data['updated_at'] ?? $data['created_at'],
                    'author' => [
                        '@type' => 'Organization',
                        'name' => '11klassniki.ru'
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => '11klassniki.ru',
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $baseUrl . '/images/logo.png'
                        ]
                    ],
                    'mainEntityOfPage' => [
                        '@type' => 'WebPage',
                        '@id' => $baseUrl . '/news/' . $data['url_news']
                    ]
                ];
                
            case 'post':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $data['title_post'],
                    'description' => substr(strip_tags($data['text_post']), 0, 160),
                    'url' => $baseUrl . '/post/' . $data['url_slug'],
                    'datePublished' => $data['date_post'],
                    'dateModified' => $data['updated_at'] ?? $data['date_post'],
                    'author' => [
                        '@type' => 'Organization',
                        'name' => '11klassniki.ru'
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => '11klassniki.ru'
                    ],
                    'mainEntityOfPage' => $baseUrl . '/post/' . $data['url_slug']
                ];
                
            case 'school':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'EducationalOrganization',
                    'name' => $data['name_school'],
                    'description' => $data['description'] ?? 'Школа в России',
                    'url' => $baseUrl . '/school/' . $data['url_slug'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $data['city'] ?? '',
                        'addressRegion' => $data['region'] ?? '',
                        'addressCountry' => 'RU'
                    ]
                ];
                
            case 'university':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'CollegeOrUniversity',
                    'name' => $data['name'],
                    'description' => $data['description'] ?? 'Университет в России',
                    'url' => $baseUrl . '/vpo/' . $data['url_slug'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $data['city'] ?? '',
                        'addressRegion' => $data['region'] ?? '',
                        'addressCountry' => 'RU'
                    ]
                ];
                
            case 'event':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'Event',
                    'name' => $data['title'],
                    'description' => $data['description'],
                    'startDate' => $data['start_date'] . 'T' . $data['start_time'],
                    'endDate' => $data['end_date'] . 'T' . ($data['end_time'] ?? '23:59:59'),
                    'location' => [
                        '@type' => 'Place',
                        'name' => $data['location'],
                        'address' => $data['location']
                    ],
                    'organizer' => [
                        '@type' => 'Organization',
                        'name' => $data['organizer']
                    ]
                ];
        }
        
        return null;
    }
    
    /**
     * Generate meta tags for pages
     */
    public static function generateMetaTags($type, $data) {
        $baseUrl = 'https://11klassniki.ru';
        $siteName = '11klassniki.ru - Образовательный портал России';
        
        $meta = [
            'viewport' => 'width=device-width, initial-scale=1.0',
            'robots' => 'index, follow',
            'language' => 'ru',
            'author' => '11klassniki.ru',
        ];
        
        switch ($type) {
            case 'news':
                $meta['title'] = $data['title_news'] . ' | Новости образования';
                $meta['description'] = substr(strip_tags($data['text_news']), 0, 160);
                $meta['og:title'] = $data['title_news'];
                $meta['og:description'] = substr(strip_tags($data['text_news']), 0, 300);
                $meta['og:type'] = 'article';
                $meta['og:url'] = $baseUrl . '/news/' . $data['url_news'];
                $meta['article:published_time'] = $data['created_at'];
                $meta['twitter:card'] = 'summary_large_image';
                break;
                
            case 'post':
                $meta['title'] = $data['title_post'] . ' | Образовательные статьи';
                $meta['description'] = substr(strip_tags($data['text_post']), 0, 160);
                $meta['og:title'] = $data['title_post'];
                $meta['og:description'] = substr(strip_tags($data['text_post']), 0, 300);
                $meta['og:type'] = 'article';
                $meta['og:url'] = $baseUrl . '/post/' . $data['url_slug'];
                $meta['article:published_time'] = $data['date_post'];
                break;
                
            case 'school':
                $meta['title'] = $data['name_school'] . ' | Школы России';
                $meta['description'] = 'Информация о школе ' . $data['name_school'] . 
                    (isset($data['city']) ? ' в городе ' . $data['city'] : '');
                $meta['og:title'] = $data['name_school'];
                $meta['og:type'] = 'website';
                $meta['og:url'] = $baseUrl . '/school/' . $data['url_slug'];
                break;
                
            case 'homepage':
                $meta['title'] = 'Образовательный портал России | ВУЗы, школы, колледжи';
                $meta['description'] = 'Найдите лучшие учебные заведения России: университеты, школы, колледжи. Актуальные новости образования, советы абитуриентам.';
                $meta['og:title'] = 'Образовательный портал России';
                $meta['og:type'] = 'website';
                $meta['og:url'] = $baseUrl;
                break;
        }
        
        // Common Open Graph tags
        $meta['og:site_name'] = $siteName;
        $meta['og:locale'] = 'ru_RU';
        if (!isset($meta['og:image'])) {
            $meta['og:image'] = $baseUrl . '/images/logo.png';
        }
        
        return $meta;
    }
    
    /**
     * Generate breadcrumb schema
     */
    public static function generateBreadcrumbs($items) {
        $breadcrumbs = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];
        
        foreach ($items as $position => $item) {
            $breadcrumbs['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
                'item' => $item['url']
            ];
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Analyze content for SEO recommendations
     */
    public static function analyzeSEO($title, $content, $url = '') {
        $recommendations = [];
        $score = 100;
        
        // Title analysis
        if (strlen($title) < 30) {
            $recommendations[] = 'Заголовок слишком короткий. Рекомендуется 30-60 символов.';
            $score -= 10;
        } elseif (strlen($title) > 60) {
            $recommendations[] = 'Заголовок слишком длинный. Рекомендуется 30-60 символов.';
            $score -= 5;
        }
        
        // Content length analysis
        $contentLength = strlen(strip_tags($content));
        if ($contentLength < 300) {
            $recommendations[] = 'Контент слишком короткий. Рекомендуется минимум 300 символов.';
            $score -= 15;
        }
        
        // URL analysis
        if ($url && strlen($url) > 100) {
            $recommendations[] = 'URL слишком длинный. Рекомендуется менее 100 символов.';
            $score -= 5;
        }
        
        // Keyword density (basic analysis)
        $words = str_word_count(strtolower(strip_tags($content)), 1, 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя');
        $totalWords = count($words);
        $wordFreq = array_count_values($words);
        
        // Find most common words (excluding common stop words)
        $stopWords = ['и', 'в', 'на', 'с', 'для', 'по', 'из', 'к', 'от', 'о', 'а', 'но', 'как', 'что', 'это', 'все', 'еще', 'уже', 'только', 'же'];
        foreach ($stopWords as $stop) {
            unset($wordFreq[$stop]);
        }
        
        arsort($wordFreq);
        $topKeywords = array_slice($wordFreq, 0, 5, true);
        
        // Check for images
        preg_match_all('/<img[^>]+>/i', $content, $images);
        if (empty($images[0])) {
            $recommendations[] = 'Добавьте изображения для улучшения вовлеченности.';
            $score -= 5;
        }
        
        // Check for internal links
        preg_match_all('/<a[^>]+href=["\'][^"\']*11klassniki\.ru[^"\']*["\'][^>]*>/i', $content, $internalLinks);
        if (empty($internalLinks[0])) {
            $recommendations[] = 'Добавьте внутренние ссылки для улучшения навигации.';
            $score -= 5;
        }
        
        return [
            'score' => max(0, $score),
            'recommendations' => $recommendations,
            'keywords' => $topKeywords,
            'stats' => [
                'title_length' => strlen($title),
                'content_length' => $contentLength,
                'word_count' => $totalWords,
                'images_count' => count($images[0] ?? []),
                'internal_links' => count($internalLinks[0] ?? [])
            ]
        ];
    }
    
    /**
     * Generate sitemap data
     */
    public static function generateSitemapData() {
        $urls = [];
        
        // Homepage
        $urls[] = [
            'loc' => 'https://11klassniki.ru/',
            'changefreq' => 'daily',
            'priority' => '1.0',
            'lastmod' => date('Y-m-d')
        ];
        
        // News
        $news = db_fetch_all("
            SELECT url_news, created_at 
            FROM news 
            WHERE is_published = 1 
            ORDER BY created_at DESC 
            LIMIT 1000
        ");
        
        foreach ($news as $item) {
            $urls[] = [
                'loc' => 'https://11klassniki.ru/news/' . $item['url_news'],
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'lastmod' => date('Y-m-d', strtotime($item['created_at']))
            ];
        }
        
        // Posts
        $posts = db_fetch_all("
            SELECT url_slug, date_post 
            FROM posts 
            WHERE is_published = 1 
            ORDER BY date_post DESC 
            LIMIT 1000
        ");
        
        foreach ($posts as $item) {
            $urls[] = [
                'loc' => 'https://11klassniki.ru/post/' . $item['url_slug'],
                'changefreq' => 'monthly',
                'priority' => '0.7',
                'lastmod' => date('Y-m-d', strtotime($item['date_post']))
            ];
        }
        
        // Static pages
        $staticPages = [
            '/vpo' => ['changefreq' => 'weekly', 'priority' => '0.9'],
            '/spo' => ['changefreq' => 'weekly', 'priority' => '0.9'],
            '/schools' => ['changefreq' => 'weekly', 'priority' => '0.9'],
            '/news' => ['changefreq' => 'daily', 'priority' => '0.8'],
            '/events' => ['changefreq' => 'daily', 'priority' => '0.7'],
            '/privacy' => ['changefreq' => 'yearly', 'priority' => '0.3']
        ];
        
        foreach ($staticPages as $url => $data) {
            $urls[] = [
                'loc' => 'https://11klassniki.ru' . $url,
                'changefreq' => $data['changefreq'],
                'priority' => $data['priority'],
                'lastmod' => date('Y-m-d')
            ];
        }
        
        return $urls;
    }
}

// API endpoint for SEO data
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'analyze':
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $url = $_POST['url'] ?? '';
            
            echo json_encode(SEOOptimizer::analyzeSEO($title, $content, $url));
            break;
            
        case 'sitemap-data':
            echo json_encode(SEOOptimizer::generateSitemapData());
            break;
            
        case 'structured-data':
            $type = $_GET['type'] ?? '';
            $id = $_GET['id'] ?? '';
            
            if ($type === 'news' && $id) {
                $data = db_fetch_one("SELECT * FROM news WHERE id_news = ?", [$id]);
                echo json_encode(SEOOptimizer::generateStructuredData('news', $data));
            } elseif ($type === 'post' && $id) {
                $data = db_fetch_one("SELECT * FROM posts WHERE id = ?", [$id]);
                echo json_encode(SEOOptimizer::generateStructuredData('post', $data));
            }
            break;
    }
    exit;
}

// HTML interface for SEO tools
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Optimizer - 11klassniki.ru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h1><i class="fas fa-search-plus me-2"></i>SEO Optimizer</h1>
        <p class="text-muted">Advanced SEO analysis and optimization tools</p>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-analytics me-2"></i>Content SEO Analysis</h5>
                    </div>
                    <div class="card-body">
                        <form id="seoForm">
                            <div class="mb-3">
                                <label class="form-label">Заголовок</label>
                                <input type="text" class="form-control" name="title" placeholder="Введите заголовок...">
                                <small class="form-text text-muted">Рекомендуется 30-60 символов</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">URL (опционально)</label>
                                <input type="text" class="form-control" name="url" placeholder="page-url-slug">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Контент</label>
                                <textarea class="form-control" name="content" rows="10" placeholder="Введите текст контента..."></textarea>
                                <small class="form-text text-muted">Рекомендуется минимум 300 символов</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Анализировать
                            </button>
                        </form>
                        
                        <div id="results" class="mt-4" style="display: none;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-tools me-2"></i>SEO Tools</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/sitemap.xml" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-sitemap me-1"></i>Sitemap.xml
                            </a>
                            <a href="/health-check.php" class="btn btn-outline-success">
                                <i class="fas fa-heartbeat me-1"></i>Health Check
                            </a>
                            <a href="/analytics" class="btn btn-outline-info">
                                <i class="fas fa-chart-line me-1"></i>Analytics
                            </a>
                        </div>
                        
                        <hr>
                        
                        <h6>Quick Tips</h6>
                        <ul class="small">
                            <li>Заголовки: 30-60 символов</li>
                            <li>Описания: 120-160 символов</li>
                            <li>URL: короткие и понятные</li>
                            <li>Контент: минимум 300 слов</li>
                            <li>Используйте внутренние ссылки</li>
                            <li>Добавляйте alt-теги к изображениям</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <a href="/" class="btn btn-secondary">
                    <i class="fas fa-home me-1"></i>Back to Homepage
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('seoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('?action=analyze', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                const resultsDiv = document.getElementById('results');
                resultsDiv.style.display = 'block';
                
                let scoreClass = 'success';
                if (data.score < 70) scoreClass = 'warning';
                if (data.score < 50) scoreClass = 'danger';
                
                resultsDiv.innerHTML = `
                    <div class="alert alert-${scoreClass}">
                        <h5><i class="fas fa-chart-line me-2"></i>SEO Score: ${data.score}/100</h5>
                    </div>
                    
                    ${data.recommendations.length > 0 ? `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6><i class="fas fa-lightbulb me-2"></i>Recommendations</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                ${data.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-chart-bar me-2"></i>Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr><td>Title Length:</td><td>${data.stats.title_length}</td></tr>
                                        <tr><td>Content Length:</td><td>${data.stats.content_length}</td></tr>
                                        <tr><td>Word Count:</td><td>${data.stats.word_count}</td></tr>
                                        <tr><td>Images:</td><td>${data.stats.images_count}</td></tr>
                                        <tr><td>Internal Links:</td><td>${data.stats.internal_links}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-key me-2"></i>Top Keywords</h6>
                                </div>
                                <div class="card-body">
                                    ${Object.entries(data.keywords).length > 0 ? 
                                        Object.entries(data.keywords).map(([word, count]) => 
                                            `<span class="badge bg-primary me-1">${word} (${count})</span>`
                                        ).join('') 
                                        : '<em>No keywords found</em>'
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
            } catch (error) {
                document.getElementById('results').innerHTML = `
                    <div class="alert alert-danger">
                        Error analyzing content: ${error.message}
                    </div>
                `;
                document.getElementById('results').style.display = 'block';
            }
        });
    </script>
</body>
</html>