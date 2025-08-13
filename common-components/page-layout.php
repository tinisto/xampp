<?php
/**
 * Unified Page Layout Component
 * 
 * Single, reusable layout for all pages
 * Replaces the old template system
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-layout.php';
 * renderPageLayout($sections, $options);
 */

// Include required components
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/comments-compact.php';

function renderPageLayout($sections = [], $options = []) {
    // Default options
    $defaults = [
        'maxWidth' => '1200px',
        'padding' => '20px',
        'spacing' => '30px',
        'showComments' => true,
        'showBreadcrumb' => false,
        'containerClass' => 'page-layout-component'
    ];
    
    $options = array_merge($defaults, $options);
    
    // Default sections
    $defaultSections = [
        'title' => '',
        'navigation' => '',
        'metadata' => '',
        'filters' => '',
        'content' => '',
        'pagination' => '',
        'comments' => []
    ];
    
    $sections = array_merge($defaultSections, $sections);
    
    ?>
    <div class="<?= htmlspecialchars($options['containerClass']) ?>" 
         style="max-width: <?= htmlspecialchars($options['maxWidth']) ?>; margin: 0 auto; padding: 0 <?= htmlspecialchars($options['padding']) ?>;">
        
        <?php if (!empty($sections['title'])): ?>
        <section class="page-section page-title-section" style="margin-bottom: <?= htmlspecialchars($options['spacing']) ?>;">
            <?= $sections['title'] ?>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($sections['navigation'])): ?>
        <section class="page-section page-navigation-section" style="margin-bottom: <?= htmlspecialchars($options['spacing']) ?>;">
            <?= $sections['navigation'] ?>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($sections['metadata'])): ?>
        <section class="page-section page-metadata-section" style="margin-bottom: 20px;">
            <?= $sections['metadata'] ?>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($sections['filters'])): ?>
        <section class="page-section page-filters-section" style="margin-bottom: <?= htmlspecialchars($options['spacing']) ?>;">
            <?= $sections['filters'] ?>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($sections['content'])): ?>
        <section class="page-section page-content-section" style="margin-bottom: <?= htmlspecialchars($options['spacing']) ?>;">
            <?= $sections['content'] ?>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($sections['pagination'])): ?>
        <section class="page-section page-pagination-section" style="margin-bottom: <?= htmlspecialchars($options['spacing']) ?>;">
            <?= $sections['pagination'] ?>
        </section>
        <?php endif; ?>
        
        <?php if ($options['showComments'] && !empty($sections['comments'])): ?>
        <section class="page-section page-comments-section" style="margin-bottom: <?= htmlspecialchars($options['spacing']) ?>;">
            <?php 
            if (is_array($sections['comments']) && isset($sections['comments']['type'], $sections['comments']['id'])) {
                renderCompactComments($sections['comments']['type'], $sections['comments']['id'], $sections['comments']['options'] ?? []);
            } else {
                echo $sections['comments'];
            }
            ?>
        </section>
        <?php endif; ?>
    </div>
    
    <style>
    /* Page Layout Component Styles */
    .page-layout-component {
        min-height: calc(100vh - 200px);
    }
    
    .page-section {
        position: relative;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-layout-component {
            padding: 0 15px !important;
        }
    }
    
    @media (max-width: 480px) {
        .page-layout-component {
            padding: 0 10px !important;
        }
    }
    
    /* Dark mode support */
    [data-bs-theme="dark"] .page-layout-component {
        color: #e0e0e0;
    }
    </style>
    
    <?php
}

/**
 * Helper function to create common page structures
 */
function createBasicPageSections($title, $content, $options = []) {
    $sections = [
        'title' => renderPageTitle($title, $options['subtitle'] ?? ''),
        'content' => $content
    ];
    
    if (!empty($options['navigation'])) {
        $sections['navigation'] = $options['navigation'];
    }
    
    if (!empty($options['comments'])) {
        $sections['comments'] = $options['comments'];
    }
    
    return $sections;
}

/**
 * Helper function to render page titles consistently
 */
function renderPageTitle($title, $subtitle = '', $options = []) {
    ob_start();
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
    renderRealTitle($title, [
        'fontSize' => $options['fontSize'] ?? '32px',
        'margin' => $options['margin'] ?? '20px 0',
        'subtitle' => $subtitle
    ]);
    return ob_get_clean();
}

/**
 * Helper function to render card grids consistently
 */
function renderPageCardGrid($items, $type = 'news', $options = []) {
    ob_start();
    renderCardsGrid($items, $type, $options);
    return ob_get_clean();
}
?>