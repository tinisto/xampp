<?php
/**
 * Reusable Content Wrapper Component
 * 
 * Provides consistent padding, margins, fonts, and responsive design
 * across all pages between header and footer.
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/content-wrapper.php';
 * renderContentWrapper('start'); // At the beginning of content
 * // Your content here
 * renderContentWrapper('end'); // At the end of content
 * 
 * Or use with content parameter:
 * renderContentWrapper('full', $content);
 */

function renderContentWrapper($mode = 'start', $content = '') {
    if ($mode === 'start' || $mode === 'full') {
        ?>
        <div class="content-wrapper">
            <div class="content-container">
        <?php
        if ($mode === 'full' && !empty($content)) {
            echo $content;
        }
    }
    
    if ($mode === 'end' || $mode === 'full') {
        ?>
            </div>
        </div>
        <?php
    }
}

// Include the CSS only once
if (!defined('CONTENT_WRAPPER_CSS_INCLUDED')) {
    define('CONTENT_WRAPPER_CSS_INCLUDED', true);
    ?>
    <style>
        /* Content Wrapper - Consistent spacing and typography */
        .content-wrapper {
            /* min-height: calc(100vh - 120px); REMOVED FOR TESTING */
            width: 100%;
            padding: 0;
            margin: 0; /* RESET MARGIN */
            position: relative; /* ENSURE PROPER POSITIONING */
            z-index: 1; /* BELOW GREEN HEADER */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-primary, #333);
            background-color: var(--background, #ffffff); /* Normal background */
            /* border: 5px solid darkred; REMOVED PER REQUEST */
        }
        
        .content-container {
            margin: 0 auto;
            padding: 40px 20px;
            padding-top: 40px; /* RESET TO NORMAL PADDING */
            width: 100%;
            box-sizing: border-box;
        }
        
        /* Mobile-first responsive design */
        @media (max-width: 768px) {
            .content-container {
                padding: 20px 16px; /* Equal padding on all sides on mobile */
            }
        }
        
        @media (max-width: 480px) {
            .content-container {
                padding: 16px 12px; /* Smaller padding on very small screens */
            }
        }
        
        /* Large screens */
        @media (min-width: 1400px) {
            .content-container {
                padding: 60px 40px;
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