<?php
/**
 * Example of news page with context-aware loading placeholders
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders-v2.php';

// Simulate loading state (in production, this would be while fetching data)
$isLoading = isset($_GET['simulate_loading']);
$newsType = $_GET['news_type'] ?? '';

// Page setup
$pageTitle = 'Новости образования';
$metaD = 'Актуальные новости образования';
$metaK = 'новости, образование, вузы, школы';

?>
<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main style="flex: 1;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
            <h1 style="color: var(--text-primary, #333); margin-bottom: 20px;"><?= htmlspecialchars($pageTitle) ?></h1>
            
            <!-- News Type Navigation -->
            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px;">
                <?php
                $currentNewsType = $_GET['news_type'] ?? '';
                $activeStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; background: var(--primary-color, #28a745); color: white;";
                $inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0);";
                ?>
                
                <a href="/news" style="<?= empty($currentNewsType) ? $activeStyle : $inactiveStyle ?>">Все новости</a>
                <a href="/news/novosti-vuzov" style="<?= $currentNewsType === 'vpo' ? $activeStyle : $inactiveStyle ?>">Новости ВПО</a>
                <a href="/news/novosti-spo" style="<?= $currentNewsType === 'spo' ? $activeStyle : $inactiveStyle ?>">Новости СПО</a>
                <a href="/news/novosti-shkol" style="<?= $currentNewsType === 'school' ? $activeStyle : $inactiveStyle ?>">Новости школ</a>
                <a href="/news/novosti-obrazovaniya" style="<?= $currentNewsType === 'education' ? $activeStyle : $inactiveStyle ?>">Новости образования</a>
            </div>
            
            <?php if ($isLoading): ?>
                <!-- Show loading placeholders -->
                <?php renderPlaceholderGrid('news-card', 12, 4, ['showBadge' => empty($newsType)]); ?>
                
                <!-- Pagination placeholder -->
                <div style="margin-top: 40px; display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <div class="skeleton skeleton-animated" style="width: 40px; height: 40px; border-radius: 4px;"></div>
                    <?php endfor; ?>
                </div>
            <?php else: ?>
                <!-- Normal content would go here -->
                <div id="news-container" 
                     data-lazy-load="/api/news-content.php?type=<?= htmlspecialchars($newsType) ?>"
                     data-placeholder-type="news-card"
                     data-placeholder-count="12"
                     data-placeholder-columns="4"
                     data-content-type="html">
                    <!-- Placeholders will be shown until content loads -->
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <!-- Include lazy loader -->
    <script src="/js/lazy-content-loader.js"></script>
    
    <!-- Demo controls -->
    <?php if (!$isLoading): ?>
    <div style="position: fixed; bottom: 20px; right: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h4 style="margin: 0 0 10px 0;">Demo Controls</h4>
        <a href="?simulate_loading=1" style="display: block; margin-bottom: 5px;">Show Loading State</a>
        <button onclick="lazyLoader.refresh()" style="display: block; width: 100%; padding: 8px; margin-bottom: 5px;">Refresh Content</button>
        <button onclick="document.getElementById('news-container').dataset.simulateDelay = '2000'; lazyLoader.refresh();" style="display: block; width: 100%; padding: 8px;">Simulate Slow Network</button>
    </div>
    <?php endif; ?>
</body>
</html>