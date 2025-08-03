<?php
/**
 * Examples of using context-aware loading placeholders
 * Include this file to see all placeholder types
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders-v2.php';
?>

<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Placeholder Examples</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background: var(--background, #f5f5f5);
        }
        .example-section {
            margin-bottom: 40px;
        }
        .example-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--text-primary, #333);
        }
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: var(--primary-color, #28a745);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()">Toggle Theme</button>

    <h1>Context-Aware Loading Placeholders</h1>

    <!-- News Cards Grid -->
    <div class="example-section">
        <h2 class="example-title">News Cards Grid (4 columns)</h2>
        <?php renderPlaceholderGrid('news-card', 4, 4); ?>
    </div>

    <!-- Post Cards with Images -->
    <div class="example-section">
        <h2 class="example-title">Post Cards with Images (3 columns)</h2>
        <?php renderPlaceholderGrid('post-card', 3, 3, ['showImage' => true, 'showBadge' => true]); ?>
    </div>

    <!-- School Cards -->
    <div class="example-section">
        <h2 class="example-title">School/Institution Cards (2 columns)</h2>
        <?php renderPlaceholderGrid('school-card', 2, 2); ?>
    </div>

    <!-- Test Cards -->
    <div class="example-section">
        <h2 class="example-title">Test Cards (4 columns)</h2>
        <?php renderPlaceholderGrid('test-card', 8, 4); ?>
    </div>

    <!-- Category Header -->
    <div class="example-section">
        <h2 class="example-title">Category Header</h2>
        <?php renderContextAwarePlaceholder('category-header'); ?>
    </div>

    <!-- Article Content -->
    <div class="example-section">
        <h2 class="example-title">Article Content</h2>
        <?php renderContextAwarePlaceholder('article-content'); ?>
    </div>

    <!-- Comments -->
    <div class="example-section">
        <h2 class="example-title">Comments</h2>
        <?php for ($i = 0; $i < 3; $i++): ?>
            <?php renderContextAwarePlaceholder('comment'); ?>
        <?php endfor; ?>
    </div>

    <!-- Table Rows -->
    <div class="example-section">
        <h2 class="example-title">Table Loading</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 12px; text-align: left;">Name</th>
                    <th style="padding: 12px; text-align: left;">Location</th>
                    <th style="padding: 12px; text-align: left;">Type</th>
                    <th style="padding: 12px; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <?php renderTableRowPlaceholder(['columns' => 4], 'skeleton-animated'); ?>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
        }
    </script>
</body>
</html>