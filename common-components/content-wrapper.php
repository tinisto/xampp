<?php
/**
 * Reusable Content Wrapper Component - New Layout Structure
 * 
 * Provides the new yellow-background layout structure with proper spacing
 * and responsive design across all pages between header and footer.
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/content-wrapper.php';
 * renderContentWrapper('start', ['title' => 'Page Title', 'showSearch' => false]); 
 * // Your content here
 * renderContentWrapper('end'); 
 * 
 * Or use with content parameter:
 * renderContentWrapper('full', ['title' => 'Page Title'], $content);
 */

function renderContentWrapper($mode = 'start', $options = [], $content = '') {
    if ($mode === 'start' || $mode === 'full') {
        ?>
        <!-- Yellow background wrapper for middle sections -->
        <div class="yellow-bg-wrapper">
            <!-- Green Page Header -->
            <div class="page-header">
                <?php 
                if (!empty($options['title'])) {
                    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
                    renderPageSectionHeader([
                        'title' => $options['title'],
                        'showSearch' => $options['showSearch'] ?? false
                    ]);
                }
                ?>
            </div>
            
            <!-- Main Content -->
            <main class="content">
                <div class="container">
        <?php
        if ($mode === 'full' && !empty($content)) {
            echo $content;
        }
    }
    
    if ($mode === 'end' || $mode === 'full') {
        ?>
                </div>
            </main>
        </div>
        <?php
    }
}

// Include the CSS only once
if (!defined('CONTENT_WRAPPER_CSS_INCLUDED')) {
    define('CONTENT_WRAPPER_CSS_INCLUDED', true);
    ?>
    <style>
        /* New Layout Structure CSS */
        
        /* Wrapper for yellow background sections */
        .yellow-bg-wrapper {
            background: yellow;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Page header (green) - flex-shrink: 0 so it keeps its size */
        .page-header {
            flex-shrink: 0;
        }
        
        /* Content - flex: 1 so it expands to fill space */
        .content {
            flex: 1 1 auto;
            background: red; /* RED background for main content */
            padding: 20px 10px; /* Reduced left/right padding on mobile */
            margin: 0 10px; /* Reduced left/right margins on mobile */
            box-sizing: border-box;
            min-height: 0; /* Allow shrinking */
            overflow: auto; /* Handle overflow internally */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-primary, #333);
        }
        
        /* Container - no padding, just for visualization */
        .content .container {
            max-width: none;
            margin: 0;
            padding: 0; /* No padding on container - it's on the parent */
            width: 100%;
        }
        
        /* Desktop - larger padding on colored divs */
        @media (min-width: 769px) {
            .content {
                padding: 40px; /* 40px padding on RED div */
                margin: 0 40px; /* Larger margins on desktop */
            }
        }
        
        /* Typography within content wrapper */
        .content-wrapper h1,
        .content-wrapper h2,
        .content-wrapper h3,
        .content-wrapper h4,
        .content-wrapper h5,
        .content-wrapper h6 {
            color: var(--text-primary, #333);
            font-weight: 600;
            line-height: 1.3;
            margin: 0 0 16px 0;
        }
        
        .content-wrapper h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 24px;
        }
        
        .content-wrapper h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .content-wrapper h3 {
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        
        .content-wrapper p {
            margin: 0 0 16px 0;
            line-height: 1.7;
        }
        
        .content-wrapper ul,
        .content-wrapper ol {
            margin: 0 0 16px 0;
            padding-left: 24px;
        }
        
        .content-wrapper li {
            margin-bottom: 8px;
        }
        
        /* Responsive typography */
        @media (max-width: 768px) {
            .content-wrapper h1 {
                font-size: 2rem;
                margin-bottom: 20px;
            }
            
            .content-wrapper h2 {
                font-size: 1.75rem;
                margin-bottom: 16px;
            }
            
            .content-wrapper h3 {
                font-size: 1.25rem;
                margin-bottom: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .content-wrapper h1 {
                font-size: 1.75rem;
                margin-bottom: 16px;
            }
            
            .content-wrapper h2 {
                font-size: 1.5rem;
                margin-bottom: 12px;
            }
            
            .content-wrapper h3 {
                font-size: 1.125rem;
                margin-bottom: 10px;
            }
        }
        
        /* Dark mode support */
        [data-theme="dark"] .content-wrapper {
            background-color: var(--background-dark, #1a1a1a); /* Dark mode background */
            color: var(--text-primary, #e4e6eb);
        }
        
        [data-theme="dark"] .content-wrapper h1,
        [data-theme="dark"] .content-wrapper h2,
        [data-theme="dark"] .content-wrapper h3,
        [data-theme="dark"] .content-wrapper h4,
        [data-theme="dark"] .content-wrapper h5,
        [data-theme="dark"] .content-wrapper h6 {
            color: var(--text-primary, #f7fafc);
        }
    </style>
    <?php
}
?>