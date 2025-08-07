<?php
/**
 * Real Title Component
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
    $customClass = $options['customClass'] ?? 'real-title';
    
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
        
        /* Force dark text in light mode - high specificity */
        body .<?php echo $customClass; ?>,
        html body .<?php echo $customClass; ?>,
        .yellow-bg-wrapper .<?php echo $customClass; ?>,
        .content .<?php echo $customClass; ?>,
        main .<?php echo $customClass; ?> {
            color: #333333 !important;
            visibility: visible !important;
            text-shadow: none !important;
        }
        
        /* Force white text in dark mode - even higher specificity */
        html[data-theme="dark"] body .<?php echo $customClass; ?>,
        html[data-bs-theme="dark"] body .<?php echo $customClass; ?>,
        [data-theme="dark"] .yellow-bg-wrapper .<?php echo $customClass; ?>,
        [data-bs-theme="dark"] .yellow-bg-wrapper .<?php echo $customClass; ?>,
        [data-theme="dark"] .content .<?php echo $customClass; ?>,
        [data-bs-theme="dark"] .content .<?php echo $customClass; ?>,
        [data-theme="dark"] main .<?php echo $customClass; ?>,
        [data-bs-theme="dark"] main .<?php echo $customClass; ?> {
            color: #ffffff !important;
            visibility: visible !important;
            text-shadow: none !important;
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
    </h1>
    
    <?php
}
?>