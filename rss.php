<?php
// RSS feed generator
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Set XML header
header('Content-Type: application/rss+xml; charset=UTF-8');

// Base URL
$baseUrl = 'https://11klassniki.ru';

// Fetch latest news
$news = db_fetch_all("
    SELECT n.*, c.name as category_name
    FROM news n
    LEFT JOIN categories c ON n.category_id = c.id
    WHERE n.is_published = 1
    ORDER BY n.created_at DESC
    LIMIT 50
");

// Start RSS
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>11klassniki.ru - Новости образования</title>
        <link><?= $baseUrl ?></link>
        <description>Последние новости из мира образования России</description>
        <language>ru</language>
        <copyright>© <?= date('Y') ?> 11klassniki.ru</copyright>
        <lastBuildDate><?= date('r') ?></lastBuildDate>
        <atom:link href="<?= $baseUrl ?>/rss.xml" rel="self" type="application/rss+xml" />
        
        <?php foreach ($news as $item): ?>
        <item>
            <title><?= htmlspecialchars($item['title_news']) ?></title>
            <link><?= $baseUrl ?>/news/<?= htmlspecialchars($item['url_news']) ?></link>
            <description><?= htmlspecialchars(mb_substr(strip_tags($item['text_news']), 0, 300)) ?>...</description>
            <pubDate><?= date('r', strtotime($item['created_at'])) ?></pubDate>
            <guid isPermaLink="true"><?= $baseUrl ?>/news/<?= htmlspecialchars($item['url_news']) ?></guid>
            <?php if ($item['category_name']): ?>
            <category><?= htmlspecialchars($item['category_name']) ?></category>
            <?php endif; ?>
        </item>
        <?php endforeach; ?>
    </channel>
</rss>