<?php
/**
 * Reusable Breadcrumb Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
 * renderBreadcrumb([
 *     ['text' => 'Главная', 'url' => '/'],
 *     ['text' => 'Школы России', 'url' => '/schools-all-regions'],
 *     ['text' => 'Калининградская область'] // Last item without URL
 * ]);
 */

function renderBreadcrumb($items = []) {
    if (empty($items)) return;
    ?>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <?php foreach ($items as $index => $item): ?>
                <?php if ($index < count($items) - 1): ?>
                    <li class="breadcrumb-item">
                        <a href="<?= htmlspecialchars($item['url']) ?>"><?= htmlspecialchars($item['text']) ?></a>
                    </li>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= htmlspecialchars($item['text']) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

// Include CSS only once
if (!defined('BREADCRUMB_CSS_INCLUDED')) {
    define('BREADCRUMB_CSS_INCLUDED', true);
    ?>
    <style>
        .breadcrumb-nav {
            margin-bottom: 10px;
        }
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            list-style: none;
        }
        .breadcrumb-item {
            font-size: 14px;
            color: #666;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            padding: 0 8px;
            color: #999;
        }
        .breadcrumb-item a {
            color: #28a745;
            text-decoration: none;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        .breadcrumb-item.active {
            color: #666;
        }
        @media (max-width: 576px) {
            .breadcrumb {
                font-size: 13px;
            }
            .breadcrumb-item + .breadcrumb-item::before {
                padding: 0 5px;
            }
        }
    </style>
    <?php
}
?>