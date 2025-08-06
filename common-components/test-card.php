<?php
/**
 * Reusable Test Card Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/test-card.php';
 * renderTestCard($test);
 */

// Include lazy loading component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/image-lazy-load.php';

function renderTestCard($test, $showBadge = true) {
    // Check for image
    $hasImage = false;
    $image = '';
    
    if (!empty($test['image'])) {
        $imagePath = "/images/test-images/" . $test['image'];
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            $hasImage = true;
            $image = $imagePath;
        }
    }
    
    // Prepare excerpt
    $excerpt = strip_tags($test['description'] ?? '');
    $excerpt = mb_strlen($excerpt) > 120 ? mb_substr($excerpt, 0, 120) . '...' : $excerpt;
    
    // Test URL
    $testUrl = "/test/" . htmlspecialchars($test['slug']);
    ?>
    
    <article class="test-card">
        <div class="test-card-wrapper">
            <div class="test-card-link">
                <div class="test-image-container">
                    <?php if ($hasImage): 
                        renderLazyImage([
                            'src' => htmlspecialchars($image),
                            'alt' => htmlspecialchars($test['title']),
                            'class' => 'test-image',
                            'aspectRatio' => '16:9'
                        ]);
                    else: ?>
                        <div class="test-image-placeholder">
                            <div class="test-icon" style="color: <?= htmlspecialchars($test['color']) ?>;">
                                <i class="<?= htmlspecialchars($test['icon']) ?>"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="test-content">
                    <h3 class="test-title">
                        <?= htmlspecialchars($test['title']) ?>
                    </h3>
                    <?php if (!empty($excerpt)): ?>
                        <p class="test-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                    <?php endif; ?>
                    <div class="test-meta">
                        <span class="test-questions">
                            <i class="fas fa-list-check"></i> <?= htmlspecialchars($test['questions']) ?>
                        </span>
                        <?php if (!empty($test['duration'])): ?>
                            <span class="test-duration">
                                <i class="fas fa-clock"></i> <?= htmlspecialchars($test['duration']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="test-buttons">
                        <a href="/test/<?= htmlspecialchars($test['slug']) ?>" class="btn-teaching">
                            Обучение
                        </a>
                        <a href="/test-full/<?= htmlspecialchars($test['slug']) ?>" class="btn-full">
                            Полный тест
                        </a>
                    </div>
                </div>
            </div>
            <?php 
            if ($showBadge && !empty($test['category'])) {
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';
                $categoryUrl = '/tests#' . strtolower(str_replace(' ', '-', $test['category']));
                renderCardBadge($test['category'], $categoryUrl, 'overlay', 'green');
            }
            ?>
        </div>
    </article>
    <?php
}

// Include the CSS only once
if (!defined('TEST_CARD_CSS_INCLUDED')) {
    define('TEST_CARD_CSS_INCLUDED', true);
    ?>
    <style>
        .tests-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 30px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .test-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            margin: 0;
            padding: 0;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .test-card * {
            box-sizing: border-box;
        }
        .test-card-wrapper {
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .test-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            border-color: rgba(0,0,0,0.15);
        }
        .test-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
            position: relative;
            cursor: default;
        }
        .test-image-container {
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
        
        /* Override for lazy image wrapper in test cards */
        .test-image-container .lazy-image-wrapper {
            border-radius: 0;
            position: relative !important;
            height: auto !important;
        }
        .test-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            display: block;
            margin: 0;
            padding: 0;
        }
        .test-card:hover .test-image {
            transform: scale(1.02);
        }
        .test-image-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin: 0;
            padding: 40px 0;
            aspect-ratio: 16 / 9;
        }
        .test-icon {
            font-size: 48px;
            opacity: 0.9;
        }
        .test-content {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .test-title {
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
        .test-card:hover .test-title {
            color: #28a745;
        }
        .test-excerpt {
            color: #666;
            font-size: 13px;
            line-height: 1.4;
            margin: 0 0 10px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .test-meta {
            margin-top: auto;
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #888;
        }
        .test-meta i {
            margin-right: 4px;
        }
        .test-buttons {
            display: flex;
            gap: 6px;
            margin-top: 12px;
        }
        .test-buttons a {
            flex: 1;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
            display: inline-block;
            line-height: 1.2;
        }
        .btn-teaching {
            background: transparent;
            color: #28a745;
            border: 1px solid #28a745;
        }
        .btn-teaching:hover {
            background: #28a745;
            color: white;
            transform: translateY(-1px);
        }
        .btn-full {
            background: transparent;
            color: #28a745;
            border: 1px solid #28a745;
        }
        .btn-full:hover {
            background: #28a745;
            color: white;
            transform: translateY(-1px);
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
        
        /* Category sections */
        .test-category {
            margin-bottom: 40px;
        }
        
        .category-title {
            color: var(--text-primary, #333);
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Responsive design */
        @media (max-width: 1200px) {
            .tests-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 900px) {
            .tests-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }
        /* Dark mode styles */
        [data-bs-theme="dark"] .test-card {
            background: #1a202c;
            color: #e4e6eb;
            border: 1px solid rgba(255,255,255,0.2);
        }
        [data-bs-theme="dark"] .test-card:hover {
            border-color: rgba(255,255,255,0.4);
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        }
        [data-bs-theme="dark"] .test-title {
            color: #f7fafc;
        }
        [data-bs-theme="dark"] .test-excerpt {
            color: #a0aec0;
        }
        [data-bs-theme="dark"] .test-meta {
            color: #718096;
        }
        [data-bs-theme="dark"] .test-image-container {
            background: #2d3748;
        }
        [data-bs-theme="dark"] .test-image-placeholder {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        }
        /* Dark mode button styles */
        [data-bs-theme="dark"] .btn-teaching {
            background: transparent;
            color: #4ade80;
            border-color: #4ade80;
        }
        [data-bs-theme="dark"] .btn-teaching:hover {
            background: #4ade80;
            color: #1a202c;
        }
        [data-bs-theme="dark"] .btn-full {
            background: transparent;
            color: #4ade80;
            border-color: #4ade80;
        }
        [data-bs-theme="dark"] .btn-full:hover {
            background: #4ade80;
            color: #1a202c;
            border-color: #4ade80;
        }
        
        @media (max-width: 600px) {
            .tests-grid {
                grid-template-columns: 1fr;
                padding: 20px 15px;
            }
        }
    </style>
    <?php
}
?>