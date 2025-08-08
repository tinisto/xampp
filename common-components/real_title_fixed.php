<?php
/**
 * Real Title Component - Fixed for dark mode
 * 
 * Usage:
 * include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
 * renderRealTitle('Page Title', $options);
 */

function renderRealTitle($title, $options = []) {
    // Extract options with defaults
    $fontSize = $options['fontSize'] ?? '32px';
    $fontWeight = $options['fontWeight'] ?? '600';
    $color = $options['color'] ?? '#333';
    $textAlign = $options['textAlign'] ?? 'center';
    $margin = $options['margin'] ?? '20px 0';
    $padding = $options['padding'] ?? '0';
    $customClass = $options['customClass'] ?? 'real-title-' . uniqid();
    
    ?>
    <style>
        .<?php echo $customClass; ?> {
            font-size: <?php echo $fontSize; ?>;
            font-weight: <?php echo $fontWeight; ?>;
            color: <?php echo $color; ?>;
            text-align: <?php echo $textAlign; ?>;
            margin: <?php echo $margin; ?>;
            padding: <?php echo $padding; ?>;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1.2;
        }
        
        .<?php echo $customClass; ?>-subtitle {
            font-size: 0.5em;
            font-weight: 400;
            margin-top: 10px;
            opacity: 0.8;
        }
        
        /* Light mode - Force dark text */
        body .<?php echo $customClass; ?>,
        body .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        html body .<?php echo $customClass; ?>,
        html body .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        .yellow-bg-wrapper .<?php echo $customClass; ?>,
        .yellow-bg-wrapper .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        .content .<?php echo $customClass; ?>,
        .content .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        main .<?php echo $customClass; ?>,
        main .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle {
            color: #333333 !important;
            visibility: visible !important;
            text-shadow: none !important;
        }
        
        /* Dark mode - Force white text */
        html[data-theme="dark"] body .<?php echo $customClass; ?>,
        html[data-theme="dark"] body .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        html[data-bs-theme="dark"] body .<?php echo $customClass; ?>,
        html[data-bs-theme="dark"] body .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        body.dark-mode .<?php echo $customClass; ?>,
        body.dark-mode .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        [data-theme="dark"] .yellow-bg-wrapper .<?php echo $customClass; ?>,
        [data-theme="dark"] .yellow-bg-wrapper .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        [data-bs-theme="dark"] .yellow-bg-wrapper .<?php echo $customClass; ?>,
        [data-bs-theme="dark"] .yellow-bg-wrapper .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        [data-theme="dark"] .content .<?php echo $customClass; ?>,
        [data-theme="dark"] .content .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        [data-bs-theme="dark"] .content .<?php echo $customClass; ?>,
        [data-bs-theme="dark"] .content .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        [data-theme="dark"] main .<?php echo $customClass; ?>,
        [data-theme="dark"] main .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        [data-bs-theme="dark"] main .<?php echo $customClass; ?>,
        [data-bs-theme="dark"] main .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle {
            color: #ffffff !important;
            visibility: visible !important;
            text-shadow: none !important;
        }
        
        /* Even more specific dark mode selectors for stubborn cases */
        .dark .<?php echo $customClass; ?>,
        .dark .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle,
        .dark-theme .<?php echo $customClass; ?>,
        .dark-theme .<?php echo $customClass; ?> .<?php echo $customClass; ?>-subtitle {
            color: #ffffff !important;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .<?php echo $customClass; ?> {
                font-size: calc(<?php echo $fontSize; ?> * 0.8);
            }
        }
        
        @media (max-width: 480px) {
            .<?php echo $customClass; ?> {
                font-size: calc(<?php echo $fontSize; ?> * 0.7);
            }
        }
    </style>
    
    <h1 class="<?php echo $customClass; ?>">
        <?php echo htmlspecialchars($title); ?>
        <?php if (isset($options['subtitle']) && !empty($options['subtitle'])): ?>
            <div class="<?php echo $customClass; ?>-subtitle">
                <?php echo htmlspecialchars($options['subtitle']); ?>
            </div>
        <?php endif; ?>
    </h1>
    
    <?php
}
?>