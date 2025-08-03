<?php
/**
 * Reusable Typography Components
 * 
 * Provides consistent typography across the entire website.
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/typography.php';
 * 
 * renderPageHeader('Title', 'Subtitle', ['centered' => true]);
 * renderSectionTitle('Section Title', 'Description');
 * renderText('Your content here', ['large' => true]);
 */

/**
 * Render page header with title and subtitle
 * NOTE: This function is deprecated - use renderPageHeader from page-header.php instead
 */
if (!function_exists('renderPageHeader')) {
    function renderPageHeader($title, $subtitle = '', $options = []) {
        $centered = $options['centered'] ?? false;
        $compact = $options['compact'] ?? false;
        $background = $options['background'] ?? false;
        $showSubtitle = $options['showSubtitle'] ?? true; // New option to hide subtitle
        
        $containerClass = 'page-header';
        if ($centered) $containerClass .= ' page-header-centered';
        if ($compact) $containerClass .= ' page-header-compact';
        if ($background) $containerClass .= ' page-header-background';
        
        ?>
        <div class="<?= $containerClass ?>">
            <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
            <?php if (!empty($subtitle) && $showSubtitle): ?>
                <p class="page-subtitle"><?= htmlspecialchars($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}

/**
 * Render section title with optional description
 */
function renderSectionTitle($title, $description = '', $options = []) {
    $level = $options['level'] ?? 2; // h2 by default
    $centered = $options['centered'] ?? false;
    $spacing = $options['spacing'] ?? 'normal'; // normal, compact, large
    
    $containerClass = 'section-header';
    if ($centered) $containerClass .= ' section-header-centered';
    $containerClass .= ' section-header-' . $spacing;
    
    ?>
    <div class="<?= $containerClass ?>">
        <?php if ($level === 1): ?>
            <h1 class="section-title"><?= htmlspecialchars($title) ?></h1>
        <?php elseif ($level === 2): ?>
            <h2 class="section-title"><?= htmlspecialchars($title) ?></h2>
        <?php elseif ($level === 3): ?>
            <h3 class="section-title"><?= htmlspecialchars($title) ?></h3>
        <?php endif; ?>
        
        <?php if (!empty($description)): ?>
            <p class="section-description"><?= htmlspecialchars($description) ?></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Render formatted text content
 */
function renderText($content, $options = []) {
    $size = $options['size'] ?? 'normal'; // small, normal, large
    $weight = $options['weight'] ?? 'normal'; // normal, medium, bold
    $color = $options['color'] ?? 'default'; // default, muted, primary
    
    $class = 'typography-text';
    $class .= ' text-' . $size;
    $class .= ' text-' . $weight;
    $class .= ' text-' . $color;
    
    ?>
    <div class="<?= $class ?>">
        <?= $content ?>
    </div>
    <?php
}

/**
 * Render a callout/highlight box
 */
function renderCallout($content, $type = 'info', $title = '') {
    $validTypes = ['info', 'success', 'warning', 'error'];
    if (!in_array($type, $validTypes)) {
        $type = 'info';
    }
    
    ?>
    <div class="callout callout-<?= $type ?>">
        <?php if (!empty($title)): ?>
            <h4 class="callout-title"><?= htmlspecialchars($title) ?></h4>
        <?php endif; ?>
        <div class="callout-content">
            <?= $content ?>
        </div>
    </div>
    <?php
}

// Include the CSS only once
if (!defined('TYPOGRAPHY_CSS_INCLUDED')) {
    define('TYPOGRAPHY_CSS_INCLUDED', true);
    ?>
    <style>
        /* Page Header Styles */
        .page-header {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color, #e2e8f0);
        }
        
        .page-header-centered {
            text-align: center;
        }
        
        .page-header-compact {
            margin-bottom: 24px;
            padding-bottom: 12px;
        }
        
        .page-header-background {
            background: linear-gradient(135deg, var(--header-bg-start, #28a745) 0%, var(--header-bg-end, #20c997) 100%);
            color: white;
            padding: 40px 20px; /* Equal top/bottom padding */
            margin: 0 calc(-50vw + 50%); /* Full viewport width */
            margin-top: -40px; /* Compensate for content wrapper padding */
            margin-bottom: 40px;
            border-radius: 0;
            border-bottom: none;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0 0 12px 0;
            color: var(--text-primary, #1a202c);
        }
        
        .page-subtitle {
            font-size: 1.125rem;
            line-height: 1.6;
            margin: 0;
            color: var(--text-secondary, #64748b);
            font-weight: 400;
        }
        
        .page-header-background .page-title,
        .page-header-background .page-subtitle {
            color: white;
        }
        
        /* Section Header Styles */
        .section-header {
            margin-bottom: 32px;
        }
        
        .section-header-centered {
            text-align: center;
        }
        
        .section-header-compact {
            margin-bottom: 20px;
        }
        
        .section-header-large {
            margin-bottom: 48px;
        }
        
        .section-title {
            font-size: 1.875rem;
            font-weight: 600;
            line-height: 1.3;
            margin: 0 0 8px 0;
            color: var(--text-primary, #1a202c);
        }
        
        .section-description {
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
            color: var(--text-secondary, #64748b);
        }
        
        /* Text Styles */
        .typography-text {
            line-height: 1.7;
        }
        
        .text-small {
            font-size: 0.875rem;
        }
        
        .text-normal {
            font-size: 1rem;
        }
        
        .text-large {
            font-size: 1.125rem;
        }
        
        .text-normal {
            font-weight: 400;
        }
        
        .text-medium {
            font-weight: 500;
        }
        
        .text-bold {
            font-weight: 600;
        }
        
        .text-default {
            color: var(--text-primary, #374151);
        }
        
        .text-muted {
            color: var(--text-secondary, #6b7280);
        }
        
        .text-primary {
            color: var(--primary-color, #28a745);
        }
        
        /* Callout Styles */
        .callout {
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
            border-left: 4px solid;
        }
        
        .callout-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0 0 12px 0;
        }
        
        .callout-content {
            margin: 0;
        }
        
        .callout-info {
            background: #eff6ff;
            border-left-color: #3b82f6;
            color: #1e40af;
        }
        
        .callout-success {
            background: #f0fdf4;
            border-left-color: #22c55e;
            color: #166534;
        }
        
        .callout-warning {
            background: #fffbeb;
            border-left-color: #f59e0b;
            color: #92400e;
        }
        
        .callout-error {
            background: #fef2f2;
            border-left-color: #ef4444;
            color: #dc2626;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .page-subtitle {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .page-header-background {
                padding: 24px 16px; /* Equal top/bottom padding on mobile */
                margin: 0 calc(-50vw + 50%);
                margin-top: -20px;
                margin-bottom: 24px;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 1.75rem;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
            
            .callout {
                padding: 16px;
            }
        }
        
        /* Dark mode support */
        [data-theme="dark"] .page-header {
            border-bottom-color: var(--border-color, #374151);
        }
        
        [data-theme="dark"] .page-title {
            color: var(--text-primary, #f9fafb);
        }
        
        [data-theme="dark"] .page-subtitle {
            color: var(--text-secondary, #d1d5db);
        }
        
        [data-theme="dark"] .section-title {
            color: var(--text-primary, #f9fafb);
        }
        
        [data-theme="dark"] .section-description {
            color: var(--text-secondary, #d1d5db);
        }
        
        [data-theme="dark"] .text-default {
            color: var(--text-primary, #e5e7eb);
        }
        
        [data-theme="dark"] .text-muted {
            color: var(--text-secondary, #9ca3af);
        }
        
        [data-theme="dark"] .callout-info {
            background: #1e3a8a;
            color: #dbeafe;
        }
        
        [data-theme="dark"] .callout-success {
            background: #14532d;
            color: #dcfce7;
        }
        
        [data-theme="dark"] .callout-warning {
            background: #78350f;
            color: #fef3c7;
        }
        
        [data-theme="dark"] .callout-error {
            background: #7f1d1d;
            color: #fecaca;
        }
        
        /* Dark mode header background */
        [data-theme="dark"] .page-header-background {
            background: linear-gradient(135deg, var(--header-bg-start, #374151) 0%, var(--header-bg-end, #4b5563) 100%);
        }
    </style>
    <?php
}
?>