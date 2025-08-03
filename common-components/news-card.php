<?php
/**
 * Reusable News Card Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';
 * renderNewsCard($newsItem);
 */

// Include lazy loading component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/image-lazy-load.php';

function renderNewsCard($news, $showBadge = true) {
    // Check for image
    $hasImage = false;
    $image = '';
    
    if (!empty($news['image_news_1'])) {
        $imagePath = "/images/news-images/{$news['id_news']}_1.jpg";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            $hasImage = true;
            $image = $imagePath;
        }
    }
    
    // Prepare excerpt
    $excerpt = strip_tags($news['text_news'] ?? '');
    $excerpt = mb_strlen($excerpt) > 120 ? mb_substr($excerpt, 0, 120) . '...' : $excerpt;
    
    // News URL
    $newsUrl = "/news/" . htmlspecialchars($news['url_news']);
    ?>
    
    <article class="news-card">
        <div class="news-card-wrapper">
            <a href="<?= $newsUrl ?>" class="news-card-link">
                <div class="news-image-container">
                    <?php if ($hasImage): 
                        renderLazyImage([
                            'src' => htmlspecialchars($image),
                            'alt' => htmlspecialchars($news['title_news']),
                            'class' => 'news-image',
                            'aspectRatio' => '16:9'
                        ]);
                    else: ?>
                        <div class="news-image-placeholder"><i class="fas fa-image fa-3x"></i></div>
                    <?php endif; ?>
                </div>
                <div class="news-content">
                    <h3 class="news-title">
                        <?= htmlspecialchars($news['title_news']) ?>
                    </h3>
                    <?php if (!empty($excerpt)): ?>
                        <p class="news-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php 
            if ($showBadge && !empty($news['title_category_news'])) {
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';
                $categoryUrl = !empty($news['url_category_news']) ? '/news/' . $news['url_category_news'] : '';
                renderCardBadge($news['title_category_news'], $categoryUrl, 'overlay', 'green');
            }
            ?>
        </div>
    </article>
    <?php
}

// Include the CSS only once
if (!defined('NEWS_CARD_CSS_INCLUDED')) {
    define('NEWS_CARD_CSS_INCLUDED', true);
    ?>
    <style>
        .news-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 30px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .news-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .news-card * {
            box-sizing: border-box;
        }
        .news-card-wrapper {
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .news-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .news-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
            position: relative;
        }
        .news-image-container {
            position: relative;
            width: 100%;
            background: #e9ecef;
            overflow: hidden;
            flex-shrink: 0;
            margin: 0;
            padding: 0;
            line-height: 0;
            font-size: 0;
        }
        
        /* Override for lazy image wrapper in news cards */
        .news-image-container .lazy-image-wrapper {
            border-radius: 0;
            position: relative !important;
            height: auto !important;
        }
        .news-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            display: block;
            margin: 0;
            padding: 0;
        }
        .news-card:hover .news-image {
            transform: scale(1.02);
        }
        .news-image-placeholder {
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            background: #dee2e6;
            margin: 0;
            padding: 0;
            aspect-ratio: 16 / 9;
        }
        .news-image-placeholder i {
            opacity: 0.7;
        }
        .news-content {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .news-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 10px 0;
            line-height: 1.3;
            color: #333;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .news-card:hover .news-title {
            color: #28a745;
        }
        .news-excerpt {
            color: #666;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            grid-column: 1 / -1;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        /* Ensure badge is clickable above card link */
        .card-badge-overlay {
            position: relative;
            z-index: 10;
        }
        
        
        /* Responsive design */
        @media (max-width: 1200px) {
            .news-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 900px) {
            .news-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }
        /* Dark mode styles */
        [data-bs-theme="dark"] .news-card {
            background: #1a202c;
            color: #e4e6eb;
            border: 1px solid rgba(255,255,255,0.2);
        }
        [data-bs-theme="dark"] .news-card:hover {
            border-color: rgba(255,255,255,0.4);
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        }
        [data-bs-theme="dark"] .news-title {
            color: #f7fafc;
        }
        [data-bs-theme="dark"] .news-summary {
            color: #a0aec0;
        }
        [data-bs-theme="dark"] .news-meta {
            color: #718096;
        }
        [data-bs-theme="dark"] .news-image-container {
            background: #2d3748;
        }
        [data-theme="dark"] .news-image-placeholder {
            background: #2d3748;
            color: #4a5568;
        }
        
        @media (max-width: 600px) {
            .news-grid {
                grid-template-columns: 1fr;
                padding: 20px 15px;
            }
        }
    </style>
    <?php
}
?>