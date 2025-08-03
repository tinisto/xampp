<?php
/**
 * Unified Template System
 * 
 * Usage:
 * include $_SERVER['DOCUMENT_ROOT'] . '/common-components/unified-template.php';
 * renderUnifiedPage($pageTitle, $pageContent, $headerStats = [], $showSearch = false);
 */

function renderUnifiedPage($pageTitle, $pageContent, $options = []) {
    // Default options
    $defaults = [
        'headerStats' => [],
        'showSearch' => false,
        'searchPlaceholder' => 'Поиск...',
        'searchId' => 'pageSearch',
        'metaDescription' => '',
        'metaKeywords' => '',
        'subtitle' => ''
    ];
    
    $options = array_merge($defaults, $options);
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
        <?php if ($options['metaDescription']): ?>
            <meta name="description" content="<?= htmlspecialchars($options['metaDescription']) ?>">
        <?php endif; ?>
        <?php if ($options['metaKeywords']): ?>
            <meta name="keywords" content="<?= htmlspecialchars($options['metaKeywords']) ?>">
        <?php endif; ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background: #f8f9fa;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .main-content {
                flex: 1;
            }
            .news-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                padding: 0 20px;
            }
            .news-card {
                background: white;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                transition: all 0.3s ease;
                position: relative;
                aspect-ratio: 1;
                display: flex;
                flex-direction: column;
            }
            .news-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            }
            .news-image-container {
                width: 100%;
                height: 60%;
                position: relative;
                flex-shrink: 0;
            }
            .news-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
                background: #f8f9fa;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #666;
            }
            .news-card:hover .news-image {
                transform: scale(1.02);
            }
            .news-content {
                padding: 15px;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                height: 100%;
            }
            .news-title {
                font-size: 14px;
                font-weight: 600;
                line-height: 1.4;
                color: #333;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 4;
                -webkit-box-orient: vertical;
                text-align: center;
                margin: 0;
            }
            .news-title a {
                color: inherit;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .news-title a:hover {
                color: #28a745;
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
            @media (max-width: 1200px) {
                .news-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }
            @media (max-width: 900px) {
                .news-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 768px) {
                .news-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php'; ?>
        
        <?php 
        renderPageHeader(
            $pageTitle, 
            $options['subtitle'], 
            $options['showSearch'], 
            $options['searchPlaceholder'], 
            $options['searchId'], 
            $options['headerStats']
        ); 
        ?>
        
        <main class="main-content">
            <div class="container">
                <?= $pageContent ?>
            </div>
        </main>

        <?php include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
?>