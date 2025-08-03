<?php
/**
 * SEO-optimized news content page
 * Example of how to implement proper SEO for news articles
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/seo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';

// Sample news data (in real implementation, this would come from database)
$newsArticle = [
    'id' => 1,
    'title' => 'Новые правила приема в вузы 2024',
    'description' => 'Министерство образования утвердило новые правила приема в высшие учебные заведения. Рассказываем о главных изменениях для абитуриентов.',
    'content' => 'Полный текст новости...',
    'author' => 'Редакция 11klassniki.ru',
    'published_date' => '2024-01-15 10:00:00',
    'modified_date' => '2024-01-15 15:30:00',
    'category' => 'Новости образования',
    'tags' => ['вуз', 'поступление', 'правила приема', 'образование'],
    'image' => '/images/news/new-admission-rules-2024.jpg'
];

// Set up SEO configuration
$seoConfig = [
    'title' => $newsArticle['title'],
    'description' => $newsArticle['description'],
    'keywords' => implode(', ', $newsArticle['tags']) . ', новости образования, 11klassniki',
    'canonical' => 'https://11klassniki.ru/news/' . $newsArticle['id'],
    'image' => 'https://11klassniki.ru' . $newsArticle['image'],
    'og_type' => 'article',
    'robots' => 'index, follow',
    
    // Article-specific meta tags
    'article_author' => $newsArticle['author'],
    'article_published_time' => date('c', strtotime($newsArticle['published_date'])),
    'article_modified_time' => date('c', strtotime($newsArticle['modified_date'])),
    
    // Structured data for Article
    'structured_data_type' => 'Article',
    'structured_data' => [
        'headline' => $newsArticle['title'],
        'description' => $newsArticle['description'],
        'image' => 'https://11klassniki.ru' . $newsArticle['image'],
        'author' => [
            '@type' => 'Person',
            'name' => $newsArticle['author']
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => '11klassniki.ru',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => 'https://11klassniki.ru/images/logo.png'
            ]
        ],
        'datePublished' => date('c', strtotime($newsArticle['published_date'])),
        'dateModified' => date('c', strtotime($newsArticle['modified_date'])),
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => 'https://11klassniki.ru/news/' . $newsArticle['id']
        ],
        'articleSection' => $newsArticle['category'],
        'keywords' => $newsArticle['tags']
    ]
];

// Set up breadcrumbs
$breadcrumbs = [
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'Новости', 'url' => '/news'],
    ['text' => $newsArticle['category'], 'url' => '/news/category/' . urlencode($newsArticle['category'])],
    ['text' => $newsArticle['title']] // Current page, no URL
];

// Make SEO config available to template
$additionalData['seo'] = $seoConfig;
$additionalData['breadcrumbs'] = $breadcrumbs;
?>

<div class="container">
    <?php renderBreadcrumb($breadcrumbs); ?>
    
    <article class="news-article" itemscope itemtype="https://schema.org/NewsArticle">
        <header class="article-header">
            <h1 class="article-title" itemprop="headline"><?= htmlspecialchars($newsArticle['title']) ?></h1>
            
            <div class="article-meta">
                <span class="article-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <span itemprop="name"><?= htmlspecialchars($newsArticle['author']) ?></span>
                </span>
                
                <time class="article-date" datetime="<?= date('c', strtotime($newsArticle['published_date'])) ?>" 
                      itemprop="datePublished">
                    <?= date('d.m.Y H:i', strtotime($newsArticle['published_date'])) ?>
                </time>
                
                <?php if ($newsArticle['modified_date'] !== $newsArticle['published_date']): ?>
                    <time class="article-modified" datetime="<?= date('c', strtotime($newsArticle['modified_date'])) ?>" 
                          itemprop="dateModified">
                        Обновлено: <?= date('d.m.Y H:i', strtotime($newsArticle['modified_date'])) ?>
                    </time>
                <?php endif; ?>
            </div>
            
            <div class="article-category">
                <span class="badge bg-primary" itemprop="articleSection">
                    <?= htmlspecialchars($newsArticle['category']) ?>
                </span>
            </div>
        </header>
        
        <?php if (!empty($newsArticle['image'])): ?>
            <div class="article-image">
                <img src="<?= htmlspecialchars($newsArticle['image']) ?>" 
                     alt="<?= htmlspecialchars($newsArticle['title']) ?>"
                     class="img-fluid rounded"
                     itemprop="image"
                     loading="lazy">
            </div>
        <?php endif; ?>
        
        <div class="article-content" itemprop="articleBody">
            <div class="lead" itemprop="description">
                <?= htmlspecialchars($newsArticle['description']) ?>
            </div>
            
            <div class="content">
                <?= nl2br(htmlspecialchars($newsArticle['content'])) ?>
            </div>
        </div>
        
        <footer class="article-footer">
            <div class="article-tags">
                <strong>Теги:</strong>
                <?php foreach ($newsArticle['tags'] as $tag): ?>
                    <span class="badge bg-secondary me-1" itemprop="keywords">
                        <?= htmlspecialchars($tag) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            
            <div class="social-share mt-3">
                <strong>Поделиться:</strong>
                <a href="https://vk.com/share.php?url=<?= urlencode('https://11klassniki.ru/news/' . $newsArticle['id']) ?>&title=<?= urlencode($newsArticle['title']) ?>" 
                   target="_blank" class="btn btn-sm btn-primary me-2">
                    ВКонтакте
                </a>
                <a href="https://t.me/share/url?url=<?= urlencode('https://11klassniki.ru/news/' . $newsArticle['id']) ?>&text=<?= urlencode($newsArticle['title']) ?>" 
                   target="_blank" class="btn btn-sm btn-info me-2">
                    Telegram
                </a>
                <a href="https://connect.ok.ru/offer?url=<?= urlencode('https://11klassniki.ru/news/' . $newsArticle['id']) ?>&title=<?= urlencode($newsArticle['title']) ?>" 
                   target="_blank" class="btn btn-sm btn-warning">
                    Одноклассники
                </a>
            </div>
        </footer>
        
        <!-- Hidden structured data -->
        <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization" style="display: none;">
            <span itemprop="name">11klassniki.ru</span>
            <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                <span itemprop="url">https://11klassniki.ru/images/logo.png</span>
            </div>
        </div>
        
        <meta itemprop="mainEntityOfPage" content="https://11klassniki.ru/news/<?= $newsArticle['id'] ?>">
    </article>
</div>

<style>
.news-article {
    max-width: 800px;
    margin: 0 auto;
}

.article-header {
    margin-bottom: 2rem;
}

.article-title {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.article-meta {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.article-meta > * {
    margin-right: 1rem;
}

.article-image {
    margin: 2rem 0;
    text-align: center;
}

.article-content .lead {
    font-size: 1.1rem;
    font-weight: 300;
    margin-bottom: 2rem;
    padding: 1rem;
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
}

.article-footer {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #dee2e6;
}

.social-share .btn {
    text-decoration: none;
}

@media (max-width: 768px) {
    .article-title {
        font-size: 1.5rem;
    }
    
    .article-meta > * {
        display: block;
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
}
</style>